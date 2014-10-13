<?php

  /**
   * New project request for client notification
   *
   * @package activeCollab.modules.system
   * @subpackage notifications
   */
  class NewProjectRequestForClientNotification extends Notification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang('Thank You for Your Request', null, true, $user->getLanguage());
    } // getMessage

    /**
     * This notification should not be displayed in web interface
     *
     * @param NotificationChannel $channel
     * @param IUser $recipient
     * @return bool
     */
    function isThisNotificationVisibleInChannel(NotificationChannel $channel, IUser $recipient) {
      if($channel instanceof EmailNotificationChannel) {
        return true; // Always deliver this notification to email
      } elseif($channel instanceof WebInterfaceNotificationChannel) {
        return false; // Never deliver this notification to web interface
      } // if

      return parent::isThisNotificationVisibleInChannel($channel, $recipient);
    } // isThisNotificationVisibleInChannel

  }