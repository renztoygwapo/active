<?php

  /**
   * Notify new assignee notification
   *
   * @package angie.frameworks.assignee
   * @subpackage notifications
   */
  abstract class FwNotifyOldAssigneeNotification extends Notification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      $parent = $this->getParent();

      return lang("You are no Longer Responsible for ':name' :type", array(
        'name' => $parent instanceof ApplicationObject ? $parent->getName() : '',
        'type' => $parent instanceof ApplicationObject ? $parent->getVerboseType(true, $user->getLanguage()) : '',
      ), true, $user->getLanguage());
    } // getMessage

  }