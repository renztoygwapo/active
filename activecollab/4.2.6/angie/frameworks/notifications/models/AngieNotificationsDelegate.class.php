<?php

  /**
   * Angie notifications delegate
   *
   * @package angie.frameworks.notifications
   * @subpackage models
   */
  class AngieNotificationsDelegate extends AngieDelegate {

    /**
     * Create a notification about given event within a given context
     *
     * @param string $event
     * @param ApplicationObject $context
     * @param IUser $sender
     * @return Notification
     */
    function notifyAbout($event, $context = null, $sender = null, $decorator = null) {
      $notification = $this->eventToNotificationInstance($event);

      if($context) {
        $notification->setParent($context);
      } // if

      if($sender) {
        $notification->setSender($sender);
      } // if

      if ($decorator instanceof FwOutgoingMessageDecorator) {
        $notification->setDecorator($decorator);
      } // if

      return $notification;
    } // notifyAbout

    /**
     * Return notification template path
     *
     * @param Notification $notification
     * @param NotificationChannel|string $channel
     * @return string
     * @throws FileDnxError
     */
    function getNotificationTemplatePath(Notification $notification, $channel) {
      $notification_class = get_class($notification);
      $channel_name = $channel instanceof NotificationChannel ? $channel->getShortName() : $channel;

      return AngieApplication::cache()->get(array('notification_template_paths', $notification_class, $channel_name), function() use ($notification, $notification_class, $channel_name) {
        $class = new ReflectionClass($notification_class);

        $main_path = dirname($class->getFileName()) . "/{$channel_name}/" . $notification->getShortName() . '.tpl';
        if(is_file($main_path)) {
          return $main_path;
        } else {
          $parent_class = $class->getParentClass();

          $inherited_path = dirname($parent_class->getFileName()) . "/{$channel_name}/" . $notification->getShortName() . '.tpl';

          if(is_file($inherited_path)) {
            return $inherited_path;
          } else {
            throw new FileDnxError($main_path);
          } // if
        } // if
      });
    } // getNotificationTemplatePath

    /**
     * Convert event signature to class name, load the class and create an instance
     *
     * @param string $event
     * @return Notification
     * @throws FileDnxError
     * @throws InvalidParamError
     * @throws ClassNotImplementedError
     */
    private function eventToNotificationInstance($event) {
      if(strpos($event, '/') === false) {
        $module_name = SYSTEM_MODULE;
        $event_name = $event;
      } else {
        list($module_name, $event_name) = explode('/', $event);
      } // if

      $module = AngieApplication::getModule($module_name);
      if($module instanceof AngieFramework) {
        $notification_class_name = Inflector::camelize($event_name) . 'Notification';

        if(!class_exists($notification_class_name, false)) {
          $notification_class_path = $module->getPath() . "/notifications/{$notification_class_name}.class.php";
          if(is_file($notification_class_path)) {
            require_once $notification_class_path;

            if(!class_exists($notification_class_name, false)) {
              throw new ClassNotImplementedError($notification_class_name, $notification_class_path);
            } // if
          } else {
            throw new FileDnxError($notification_class_path, "Failed to load notification class for '$event' event");
          } // if
        } // if

        $notification = new $notification_class_name();

        if($notification instanceof Notification) {
          return $notification;
        } else {
          throw new ClassNotImplementedError($notification_class_name, $notification_class_path, "Class '$notification_class_name' found, but it does not inherit Notification class");
        }
      } else {
        throw new InvalidParamError('event', $event, "Invalid module name found in '$event' event");
      } // if
    } // eventToNotificationInstance

    // ---------------------------------------------------
    //  Channels and Sending
    // ---------------------------------------------------

    /**
     * Send $notification to the list of recipients
     *
     * @param Notification $notification
     * @param IUser[] $users
     * @param boolean $skip_sending_queue
     * @throws Exception
     * @throws InvalidInstanceError
     */
    function sendNotificationToRecipients(Notification &$notification, $users, $skip_sending_queue = false) {
      if($users instanceof IUser) {
        $users = array($users);
      } // if

      if(empty($users) || !is_foreachable($users)) {
        return;
      } // if

      if($notification->isNew()) {
        $notification->save();
      } // if

      $recipients = array();

      // Check recipients list
      foreach($users as $user) {
        if($user instanceof IUser) {
          if(isset($recipients[$user->getEmail()])) {
            continue;
          } // if

          if(!$notification->isThisNotificationVisibleToUser($user) || $notification->isUserBlockingThisNotification($user)) {
            continue; // Remove from list of recipients if user can't see this notification, or if user is blocking it
          } // if

          $recipients[$user->getEmail()] = $user;
        } else {
          throw new InvalidInstanceError('user', $user, 'IUser');
        } // if
      } // foreach

      if(count($recipients)) {
        try {
          $this->openChannels();

          foreach($recipients as $recipient) {
            foreach($this->getChannels() as $channel) {
              if($notification->isThisNotificationVisibleInChannel($channel, $recipient)) {
                $channel->send($notification, $recipient, $skip_sending_queue);
              } // if
            } // foreach
          } // foreach

          $this->closeChannels();
        } catch(Exception $e) {
          $this->closeChannels(true);
          throw $e;
        } // try
      } // if
    } // sendNotificationToRecipients

    /**
     * Array of registered notification channels
     *
     * @var array
     */
    private $channels = false;

    /**
     * Return notification channels
     *
     * @return NotificationChannel[]
     */
    function &getChannels() {
      if($this->channels === false) {
        $this->channels = array(new WebInterfaceNotificationChannel());

        EventsManager::trigger('on_notification_channels', array(&$this->channels));
      } // if

      return $this->channels;
    } // getChannels

    /**
     * Indicate whether channels are open
     *
     * @var bool
     */
    private $channels_are_open = false;

    /**
     * Returns true if channels are open
     *
     * @return bool
     */
    function channelsAreOpen() {
      return $this->channels_are_open;
    } // channelsAreOpen

    /**
     * Open notifications channels for bulk sending
     *
     * @throws Error
     */
    function openChannels() {
      if($this->channels_are_open) {
        throw new Error('Channels are already open');
      } // if

      foreach($this->getChannels() as $channel) {
        $channel->open();
      } // foreach

      $this->channels_are_open = true;
    } // openChannels

    /**
     * Close notification channels for bulk sending
     *
     * @param boolean $sending_interupted
     * @throws Error
     */
    function closeChannels($sending_interupted = false) {
      if(empty($this->channels_are_open)) {
        throw new Error('Channels are not open');
      } // if

      for($i = count($this->channels) - 1; $i >= 0; $i--) {
        $this->channels[$i]->close($sending_interupted);
      } // for

      $this->channels_are_open = false;
    } // closeChannels

    /**
     * Cached flag values, per user
     *
     * @var array
     */
    private $can_override_default_settings = array();

    /**
     * Return true if $user can override global settings of any channel
     *
     * @param User $user
     * @return bool
     */
    function canOverrideDefaultSettings(User $user) {
      $user_id = $user->getId();

      if(!array_key_exists($user_id, $this->can_override_default_settings)) {
        $this->can_override_default_settings[$user_id] = false;

        foreach($this->getChannels() as $channel) {
          if($channel instanceof WebInterfaceNotificationChannel) {
            continue;
          } // if

          if($channel->canOverrideDefaultStatus($user)) {
            $this->can_override_default_settings[$user_id] = true;
            break;
          } // if
        } // foreach
      } // if


      return $this->can_override_default_settings[$user_id];
    } // canOverrideDefaultSettings

  }