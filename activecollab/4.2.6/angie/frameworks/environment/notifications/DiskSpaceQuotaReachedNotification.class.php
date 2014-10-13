<?php

  /**
   * Disk Space Quota Reached notification
   *
   * @package angie.frameworks.environment
   * @subpackage notifications
   */
  class DiskSpaceQuotaReachedNotification extends DiskSpaceNotification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang("Disk Quota Full", null, true, $user->getLanguage());
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
        return true; // Force email, regardless of settings
      } // if

      return parent::isThisNotificationVisibleInChannel($channel, $recipient);
    } // isThisNotificationVisibleInChannel

  }