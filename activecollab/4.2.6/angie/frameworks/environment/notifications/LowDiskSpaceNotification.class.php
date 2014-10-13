<?php

  /**
   * Low Disk Space notification
   *
   * @package angie.frameworks.environment
   * @subpackage notifications
   */
  class LowDiskSpaceNotification extends DiskSpaceNotification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang("Low Disk Space", null, true, $user->getLanguage());
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