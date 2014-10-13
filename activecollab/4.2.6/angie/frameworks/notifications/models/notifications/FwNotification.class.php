<?php

  /**
   * Framework level notification implementation
   *
   * @package angie.frameworks.notifications
   * @subpackage models
   */
  abstract class FwNotification extends BaseNotification implements IRoutingContext, IReadOnly {

    /**
     * Cached short name
     *
     * @var bool
     */
    private $short_name = false;

    /**
     * Automatically exclude sender from recipient list
     *
     * @var bool
     */
    protected $automatically_exclude_sender = true;

    /**
     * Return short name
     *
     * @return string
     */
    function getShortName() {
      if($this->short_name === false) {
        $class_name = get_class($this);

        $this->short_name = Inflector::underscore(substr($class_name, 0, strlen($class_name) - 12));
      } // if

      return $this->short_name;
    } // getShortName

    /**
     * Return notification message in plain text
     *
     * @param IUser $user
     * @return string
     */
    abstract function getMessage(IUser $user);

    /**
     * Return full HTML notification
     *
     * @param IUser $user
     * @return string
     */
    function getClickableMessage(IUser $user) {
      return $this->getMessage($user);
    } // getClickableMessage

    /**
     * Return full HTML message that can be used in application interface
     *
     * @param IUser $user
     * @return string
     */
    function getMessageForWebInterface(IUser $user) {
      $message = clean($this->getMessage($user));

      if($this->getParent() instanceof ApplicationObject) {
        return '<a href="' . clean($this->getParent()->getViewUrl()) . '" class="quick_view_item">' . $message . '</a>';
      } else {
        return $message;
      } // if
    } // getMessageForWebInterface

    /**
     * Return template path for a given channel
     *
     * @param NotificationChannel|string $channel
     * @return string
     * @throws FileDnxError
     */
    function getTemplatePath($channel) {
      return AngieApplication::notifications()->getNotificationTemplatePath($this, $channel);
    } // getTemplatePath

    /**
     * Return additional template variables
     *
     * @param NotificationChannel $channel
     * @return array
     */
    function getAdditionalTemplateVars(NotificationChannel $channel) {
      return array();
    } // getAdditionalTemplateVars

    /**
     * Return notification sender
     *
     * @return IUser|null
     */
    function getSender() {
      return $this->getUserFromFieldSet('sender');
    } // getSender

    /**
     * Set notification sender
     *
     * @param IUser|null $user
     * @return IUser|null
     */
    function setSender($user) {
      return $this->setUserFromFieldSet($user, 'sender');
    } // setSender

    private $decorator = false;

    /**
     * @param FwOutgoingMessageDecorator $decorator
     * @return mixed
     */
    function setDecorator(FwOutgoingMessageDecorator $decorator) {
      $this->decorator = $decorator;
      return $this;
    } // setDecorator

    /**
     * @return FwOutgoingMessageDecorator
     */
    function getDecorator() {
      return $this->decorator instanceof FwOutgoingMessageDecorator ? $this->decorator : AngieApplication::mailer()->getDecorator();
    } // getDecorator

    /**
     * Returns true if $user is sender of this notification
     *
     * @param IUser $user
     * @return boolean
     * @throws InvalidInstanceError
     */
    function isSender(IUser $user) {
      if($user instanceof User) {
        return $this->getSenderId() == $user->getId();
      } elseif($user instanceof IUser) {
        return $this->getSenderEmail() == $user->getEmail();
      } else {
        throw new InvalidInstanceError('user', $user, 'IUser');
      } // if
    } // isSender

    /**
     * Return true if we should collect mentiones info from the parent (in cases where parent is a valid ApplicationObject instance)
     *
     * @return bool
     */
    protected function getMentionsFromParent() {
      return true;
    } // getMentionsFromParent

    /**
     * Set notification parent instance
     *
     * @param ApplicationObject $parent
     * @param boolean $save
     * @return ApplicationObject
     * @throws InvalidInstanceError
     */
    function setParent($parent, $save = false) {
      if($parent instanceof ApplicationObject && $this->getMentionsFromParent() && is_foreachable($parent->getNewMentions())) {
        $this->setMentionedUsers($parent->getNewMentions());
      } // if

      return parent::setParent($parent, $save);
    } // setParent

    /**
     * Return files attached to this notification, if any
     *
     * @param NotificationChannel $channel
     * @return array
     */
    function getAttachments(NotificationChannel $channel) {
      return array();
    } // getAttachments

    /**
     * Return visit URL
     *
     * @param IUser $user
     * @return string
     */
    function getVisitUrl(IUser $user) {
      return $this->getParent() instanceof IRoutingContext ? $this->getParent()->getViewUrl() : '#';
    } // getVisitUrl

    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
      return array(
        'id' => $this->getId(),
        'message' => $for_interface == AngieApplication::INTERFACE_DEFAULT ? $this->getMessageForWebInterface($user) : $this->getMessage($user),
        'sender' => $this->getSender() instanceof IDescribe ? AngieApplication::describe()->object($this->getSender(), $user) : null,
        'parent' => $this->getParent() instanceof IDescribe ? AngieApplication::describe()->object($this->getParent(), $user) : null,
        'is_seen' => $user instanceof User ? Notifications::isSeen($this, $user) : true,
        'is_read' => $user instanceof User ? Notifications::isRead($this, $user) : true,
        'created_on' => $this->getCreatedOn(),
        'mentioned_users' => $this->getMentionedUsers(),
      );
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      return array(
        'id' => $this->getId(),
        'message' => $this->getMessage($user),
        'sender' => $this->getSender() instanceof IDescribe ? AngieApplication::describe()->objectForApi($this->getSender(), $user) : null,
        'parent' => $this->getParent() instanceof IDescribe ? AngieApplication::describe()->objectForApi($this->getParent(), $user) : null,
        'is_seen' => $user instanceof User ? Notifications::isSeen($this, $user) : true,
        'is_read' => $user instanceof User ? Notifications::isRead($this, $user) : true,
        'created_on' => $this->getCreatedOn(),
      );
    } // describeForApi

    /**
     * Disable describe cache
     *
     * @return bool
     */
    function disableDescribeCache() {
      return true;
    } // disableDescribeCache

    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'notification';
    } // getRoutingContext

    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array(
        'notification_id' => $this->getId(),
      );
    } // getRoutingContextParams

    // ---------------------------------------------------
    //  Repipients
    // ---------------------------------------------------

    /**
     * Cached array of recipients
     *
     * @var array
     */
    private $recipients = false;

    /**
     * Return array of notification recipients
     *
     * @param boolean $use_cache
     * @return array
     */
    function getRecipients($use_cache = true) {
      if(empty($use_cache) || $this->recipients === false) {
        $recipients_table = TABLE_PREFIX . 'notification_recipients';

        $this->recipients = Users::findOnlyUsersFromUserListingTable($recipients_table, 'recipient', DB::prepare("$recipients_table.notification_id = ?", $this->getId()));
      } // if

      return $this->recipients;
    } // getRecipients

    /**
     * Add recipients to this notification
     *
     * @param User|User[] $r
     * @throws InvalidParamError
     * @throws InvalidInstanceError
     */
    function addRecipient($r) {
      if($r instanceof User) {
        $r = array($r);
      } // if

      if(is_foreachable($r)) {
        $notification_id = $this->getId();
        $batch = new DBBatchInsert(TABLE_PREFIX . 'notification_recipients', array('notification_id', 'recipient_id', 'recipient_name', 'recipient_email', 'is_mentioned'));

        foreach($r as $recipient) {
          $batch->insert($notification_id, $recipient->getId(), $recipient->getName(), $recipient->getEmail(), $this->isUserMentioned($recipient));
        } // foreach

        $batch->done();
      } else {
        throw new InvalidParamError('r', $r, '$r is expected to be one or more IUser instances');
      } // if
    } // addRecipient

    /**
     * Remove one or more recipients from this notification
     *
     * @param User|User[] $r
     */
    function removeRecipient($r) {
      if($r instanceof User) {
        $r = array($r);
      } // if

      if(is_foreachable($r)) {
        $recipient_ids = array();

        foreach($r as $recipient) {
          $recipient_ids[] = $recipient->getId();
        } // foreach

        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'notification_recipients WHERE notification_id = ? AND recipient_id IN (?)', $this->getId(), $recipient_ids);
      } // if
    } // removeRecipient

    /**
     * Remove all recipients from this notification
     */
    function clearRecipients() {
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'notification_recipients WHERE notification_id = ?', $this->getId());
      $this->recipients = false;
    } // clearRecipients

    /**
     * Returns true if $user viewed this notification
     *
     * @param User $user
     * @param boolean $use_cache
     * @return boolean
     */
    function isSeen(User $user, $use_cache = true) {
      return Notifications::isSeen($this, $user, $use_cache);
    } // isSeen

    /**
     * Returns true if $user viewed this notification
     *
     * @param User $user
     * @param boolean $use_cache
     * @return boolean
     */
    function isRead(User $user, $use_cache = true) {
      return Notifications::isRead($this, $user, $use_cache);
    } // isRead

    // ---------------------------------------------------
    //  User groups
    // ---------------------------------------------------

    /**
     * Send to administrators
     *
     * @param boolean $skip_sending_queue
     * @return Notification
     */
    function &sendToAdministrators($skip_sending_queue = false) {
      return $this->sendToUsers(Users::findAdministrators(), $skip_sending_queue);
    } // administrators

    /**
     * Send to subscribers
     *
     * @param boolean $skip_sending_queue
     * @return Notification
     * @throws InvalidInstanceError if $context does not implement ISubscriptions interface
     */
    function &sendToSubscribers($skip_sending_queue = false) {
      $parent = $this->getParent();

      if (!($parent instanceof ISubscriptions)) {
        throw new InvalidInstanceError('parent', $parent, 'ISubscriptions');
      } // if

      if ($parent->subscriptions()->hasSubscribers()) {
        return $this->sendToUsers($parent->subscriptions()->get(), $skip_sending_queue);
      } //if

      return false;
    } // subscribers

    /**
     * Send to responsible assignee
     *
     * @param boolean $skip_sending_queue
     * @return Notification
     * @throws InvalidInstanceError if $context does not implement IAssignees interface
     */
    function &sendToAssignee($skip_sending_queue = false) {
      $parent = $this->getParent();

      if($parent instanceof IAssignees) {
        return $this->sendToUsers($parent->assignees()->getAssignee(), $skip_sending_queue);
      } else {
        throw new InvalidInstanceError('parnet', $parent, 'IAssignees');
      } // if
    } // sendToAssignee

    /**
     * Send to all assignees
     *
     * @param boolean $skip_sending_queue
     * @return Notification
     * @throws InvalidInstanceError if $context does not implement IAssignees interface
     */
    function &sendToAllAssignees($skip_sending_queue = false) {
      $parent = $this->getParent();

      if($parent instanceof IAssignees) {
        return $this->sendToUsers($parent->assignees()->getAllAssignees(), $skip_sending_queue);
      } else {
        throw new InvalidInstanceError('parnet', $parent, 'IAssignees');
      } // if
    } // sendToAllAssignees

    /**
     * Send to multiple groups of users
     *
     * @param array $groups
     * @param bool $skip_sending_queue
     * @throws InvalidParamError
     */
    function &sendToGroupsOfUsers($groups, $skip_sending_queue = false) {
      if(is_foreachable($groups)) {
        $users = array();

        foreach($groups as $group) {
          if($group && is_foreachable($group)) {
            foreach($group as $user) {
              if($user instanceof IUser) {
                $email = $user->getEmail();

                if(isset($users[$email])) {
                  continue;
                } // if

                $users[$email] = $user;
              } else {
                throw new InvalidParamError('groups', $groups, '$groups can have arrays of IUser instances only');
              } // if
            } // foreach
          } // if
        } // foreach

        $this->sendToUsers($users, $skip_sending_queue);
      } else {
        throw new InvalidParamError('groups', $groups, '$groups is expected to be array of groups of users');
      } // if
    } // sendToGroupsOfUsers

    /**
     * Send to provided group of users
     *
     * @param IUser|IUser[] $users
     * @param boolean $skip_sending_queue
     * @return Notification
     * @throws InvalidParamError
     * @throws InvalidInstanceError
     * @throws Exception
     */
    function &sendToUsers($users, $skip_sending_queue = false) {
      AngieApplication::notifications()->sendNotificationToRecipients($this, $users, $skip_sending_queue);

      return $this;
    } // sendToUsers

    // ---------------------------------------------------
    //  Utility Methods
    // ---------------------------------------------------

    /**
     * Returns true if $user was mentioned in this notification
     *
     * @param IUser $user
     * @return bool
     */
    function isUserMentioned($user) {
      if($user instanceof User) {
        $mentioned_users = $this->getMentionedUsers();

        return is_array($mentioned_users) && in_array($user->getId(), $mentioned_users);
      } // if

      return false;
    } // isUserMentioned

    /**
     * Return array of mentioned users, if any
     *
     * @return array|null
     */
    function getMentionedUsers() {
      return $this->getAdditionalProperty('mentioned_users');
    } // getMentionedUsers

    /**
     * Set array of mentioned users
     *
     * @param array $value
     * @return array
     */
    protected function setMentionedUsers($value) {
      return $this->setAdditionalProperty('mentioned_users', $value);
    } // setMentionedUsers

    /**
     * Returns true if $user can see this notification
     *
     * @param IUser $user
     * @return bool
     */
    function isThisNotificationVisibleToUser(IUser $user) {
      if($user instanceof IState && $user->getState() <= STATE_ARCHIVED) {
        return false; // Not visible if used is archived or deleted
      } // if

      $parent = $this->getParent();

      if($parent instanceof IVisibility && $user->getMinVisibility() > $parent->getVisibility()) {
        return false; // Don't send notification to archived users
      } // if

      if($this->automatically_exclude_sender && $this->isSender($user)) {
        return false; // Ignore sender by default
      } // if

      return true;
    } // isThisNotificationVisibleToUser

    /**
     * Returns true if $user is blocking this notifcation
     *
     * @param IUser $user
     * @return bool
     */
    function isUserBlockingThisNotification(IUser $user) {
      return false;
    } // isUserBlockingThisNotification

    /**
     * This notification should not be displayed in web interface
     *
     * @param NotificationChannel $channel
     * @param IUser $recipient
     * @return bool
     */
    function isThisNotificationVisibleInChannel(NotificationChannel $channel, IUser $recipient) {
      if($recipient instanceof User) {
        if($this->isUserMentioned($recipient) && $channel instanceof EmailNotificationChannel) {
          return true; // When user is mentioned, send email notification
        } // if

        return $channel->isEnabledFor($recipient);
      } // if

      return true;
    } // isThisNotificationVisibleInChannel

    /**
     * Return subscription code
     *
     * @param IUser $recipient
     * @return string
     */
    function getSubscriptionCode(IUser $recipient) {
      return $this->getParent() instanceof ISubscriptions ? $this->getParent()->subscriptions()->getSubscriptionCodeFor($recipient) : null;
    } // getSubscriptionCode

    // ---------------------------------------------------
    //  Template variables
    // ---------------------------------------------------

    /**
     * Array of additional template vars, indexed by variable name
     *
     * @var array
     */
    private $additional_template_vars = array();

    /**
     * Set additional template variables
     *
     * @param mixed $p1
     * @param mixed $p2
     * @return Notification
     */
    function &setAdditionalTemplateVars($p1, $p2 = null) {
      if(is_array($p1)) {
        $this->additional_template_vars = array_merge($this->additional_template_vars, $p1);
      } else {
        $this->additional_template_vars[$p1] = $p2;
      } // if

      return $this;
    } // setAdditionalTemplateVars

    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------

    /**
     * Get mark as read URL
     *
     * @return string
     */
    function getMarkAsReadUrl() {
      return Router::assemble('notification_mark_read', $this->getRoutingContextParams());
    } // getMarkAsReadUrl

    /**
     * Get mark as read URL
     *
     * @return string
     */
    function getMarkAsUnreadUrl() {
      return Router::assemble('notification_mark_unread', $this->getRoutingContextParams());
    } // getMarkAsUnreadUrl

    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------

    /**
     * Delete notification from the database
     *
     * @return bool
     * @throws Exception
     */
    function delete() {
      try {
        DB::beginWork('Deleting notification @ ' . __CLASS__);

        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'notification_recipients WHERE notification_id = ?', $this->getId());
        parent::delete();

        DB::commit('Notification deleted @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to delete notification @ ' . __CLASS__);
        throw $e;
      } // try

      return true;
    } // delete

  }