<?php

  /**
   * Remind self notification
   *
   * @package angie.frameworks.reminders
   * @subpackage notifications
   */
  class FwRemindSelfNotification extends BaseReminderNotification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      $parent = $this->getParent();

      return lang("Self reminder about ':name' :type", array(
        'name' => $parent instanceof ApplicationObject ? $parent->getName() : '',
        'type' => $parent instanceof ApplicationObject ? $parent->getVerboseType(true, $user->getLanguage()) : ''
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
      if($channel instanceof EmailNotificationChannel || $channel instanceof WebInterfaceNotificationChannel) {
        return true;
      } // if

      return parent::isThisNotificationVisibleInChannel($channel, $recipient);
    } // isThisNotificationVisibleInChannel

  }