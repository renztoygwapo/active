<?php

  /**
   * Email notification channel
   *
   * @package angie.frameworks.notifications
   * @subpackage models
   */
  class EmailNotificationChannel extends NotificationChannel {

    // Short channel name
    const CHANNEL_NAME = 'email';

    /**
     * Return channel short name
     *
     * @return string
     */
    function getShortName() {
      return EmailNotificationChannel::CHANNEL_NAME;
    } // getShortName

    /**
     * Return verbose name of the channel
     *
     * @return string
     */
    function getVerboseName() {
      return lang('Email Notifications');
    } // getVerboseName

    /**
     * Open channel for sending
     */
    function open() {
      AngieApplication::mailer()->connect();

      parent::open();
    } // open

    /**
     * Close channel after notifications have been sent
     */
    function close($sending_interupted = false) {
      AngieApplication::mailer()->disconnect();

      parent::close($sending_interupted);
    } // close

    /**
     * Send notification via this channel
     *
     * @param Notification $notification
     * @param IUser $recipient
     * @param boolean $skip_sending_queue
     * @throws Exception
     */
    function send(Notification &$notification, IUser $recipient, $skip_sending_queue = false) {
      $template = $this->getTemplateForNotification($notification);
      $template->assign(array(
        'recipient' => $recipient,
        'language' => $recipient->getLanguage(),
        'context_view_url' => $this->getParentViewUrlForUser($notification, $recipient),
      ));

      $content = $template->fetch();

      if(strpos($content, '================================================================================')) {
        list($subject, $body) = explode('================================================================================', $content);

        $subject = undo_htmlspecialchars(trim($subject)); // Subject does not have to be escaped
        $body = trim($body);
      } else {
        $subject = lang('[No Subject]', null, true, $recipient->getLanguage());
        $body = trim($content);
      } // if

      $mailing_method = $skip_sending_queue ? AngieMailerDelegate::SEND_INSTANTNLY : $recipient->getMailingMethod();

      AngieApplication::mailer()->send($recipient, $subject, $body, array(
        'sender' => $notification->getSender() instanceof IUser ? $notification->getSender() : AngieApplication::mailer()->getDefaultSender(),
        'context' => $notification->getParent(),
        'attachments' => $notification->getAttachments($this),
        'decorator' => $notification->getDecorator(),
        'subscription_code' => $notification->getSubscriptionCode($recipient),
        'decorate' => true, // array_var($additional, 'decorate', true)
      ), $mailing_method);
    } // send

    /**
     * Return parent view URL for given user
     *
     * @param Notification $notification
     * @param IUser $user
     * @return string
     */
    private function getParentViewUrlForUser(Notification $notification, IUser $user) {
      $parent = $notification->getParent();

      if($parent instanceof ApplicationObject) {
        $default_view_url = AngieApplication::cache()->getByObject($parent, 'default_notification_view_url', function() use ($parent) {
          return $parent instanceof IRoutingContext ? $parent->getViewUrl() : null;
        });

        return AngieApplication::cache()->getByObject($parent, array('notification_view_url', $user->getEmail()), function() use ($user, $parent, $default_view_url) {
          $context_view_url = $default_view_url;
          EventsManager::trigger('on_notification_context_view_url', array(&$user, &$parent, &$context_view_url));

          return $context_view_url;
        });
      } // if

      return null;
    } // getParentViewUrlForUser

    /**
     * Cached template instances
     *
     * @var array
     */
    private $templates = array();

    /**
     * Return template for a particular notification
     *
     * @param Notification $notification
     * @return Smarty_Internal_Template
     */
    private function &getTemplateForNotification(Notification &$notification) {
      $notification_id = $notification->getId();

      if(!isset($this->templates[$notification_id])) {
        $this->templates[$notification_id] = SmartyForAngie::getInstance()->createTemplate($notification->getTemplatePath($this));

        $this->templates[$notification_id]->assign(array(
          'sender' => $notification->getSender() instanceof IUser ? $notification->getSender() : AngieApplication::mailer()->getDefaultSender(),
          'context' => $notification->getParent(),
          'style' => array(
            'link' => $notification->getDecorator()->getLinkStyle(),
          ),
        ));

        $this->templates[$notification_id]->assign($notification->getAdditionalTemplateVars($this));
      } // if

      return $this->templates[$notification_id];
    } // getTemplateForNotification

  }