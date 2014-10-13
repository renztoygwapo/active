<?php

  /**
   * Notify email sender notification
   *
   * @package angie.frameworks.email
   * @subpackage notifications
   */
  abstract class FwNotifyEmailSenderNotification extends Notification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang("New :type has been Created", array(
        'type' => $this->getParent() instanceof ApplicationObject ? $this->getParent()->getVerboseType(true, $user->getLanguage()) : '',
      ), true, $user->getLanguage());
    } // getMessage

    /**
     * This notification should not be displayed in web interface
     *
     * @param NotificationChannel $channel
     * @param IUser $recipient
     * @return bool
     */
    function isThisNotificationVisibleInChannel(NotificationChannel $channel, IUser $recipient) {
      if($channel instanceof WebInterfaceNotificationChannel) {
        return false;
      } // if

      return parent::isThisNotificationVisibleInChannel($channel, $recipient);
    } // isThisNotificationVisibleInChannel

  }