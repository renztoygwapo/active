<?php

  /**
   * Notify new subtask assignee notification
   *
   * @package angie.frameworks.subtasks
   * @subpackage notifications
   */
  abstract class FwNotifyNewSubtaskAssigneeNotification extends BaseSubtaskNotification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang("You are now Responsible for ':name' Subtask", array(
        'name' => $this->getSubtask() instanceof Subtask ? $this->getSubtask()->getName() : '',
      ), true, $user->getLanguage());
    } // getMessage

    /**
     * Get is reassigned
     *
     * @return boolean
     */
    function getIsReassigned() {
      return $this->getAdditionalProperty('is_reassigned');
    } // getIsReassigned

    /**
     * Set is reassigned
     *
     * @param boolean $value
     * @return NotifyNewSubtaskAssigneeNotification
     */
    function &setIsReassigned($value) {
      $this->setAdditionalProperty('is_reassigned', (boolean) $value);

      return $this;
    } // setIsReassigned

    /**
     * Return additional template variables
     *
     * @param NotificationChannel $channel
     * @return array
     */
    function getAdditionalTemplateVars(NotificationChannel $channel) {
      $result = parent::getAdditionalTemplateVars($channel);
      $result['title_lang'] = $this->getIsReassigned() ? 'Subtask Reassigned' : 'Subtask Assigned';

      return $result;
    } // getAdditionalTemplateVars

  }