<?php

  /**
   * Forgot password notification
   *
   * @package angie.frameworks.authentication
   * @subpackage notifications
   */
  class FwForgotPasswordNotification extends Notification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang('Reset Your Password', null, true, $user->getLanguage());
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
        return true; // Always deliver this notification via email
      } elseif($channel instanceof WebInterfaceNotificationChannel) {
        return false; // Never deliver this notification to web interface
      } // if

      return parent::isThisNotificationVisibleInChannel($channel, $recipient);
    } // isThisNotificationVisibleInChannel

  }