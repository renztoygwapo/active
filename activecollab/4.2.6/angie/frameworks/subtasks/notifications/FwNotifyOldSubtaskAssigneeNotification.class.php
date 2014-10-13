<?php

  /**
   * Notify old subtask assignee notification
   *
   * @package angie.frameworks.subtasks
   * @subpackage notifications
   */
  abstract class FwNotifyOldSubtaskAssigneeNotification extends BaseSubtaskNotification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang("You are no Longer Responsible for ':name' Subtask", array(
        'name' => $this->getSubtask() instanceof Subtask ? $this->getSubtask()->getName() : '', 
      ), true, $user->getLanguage());
    } // getMessage

  }