<?php

  /**
   * Remind notification
   *
   * @package angie.frameworks.reminders
   * @subpackage notifications
   */
  class FwRemindNotification extends BaseReminderNotification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      $parent = $this->getParent();

      return lang("Reminder about ':name' :type", array(
        'name' => $parent instanceof ApplicationObject ? $parent->getName() : '',
        'type' => $parent instanceof ApplicationObject ? $parent->getVerboseType(true, $user->getLanguage()) : ''
      ), true, $user->getLanguage());
    } // getMessage

  }