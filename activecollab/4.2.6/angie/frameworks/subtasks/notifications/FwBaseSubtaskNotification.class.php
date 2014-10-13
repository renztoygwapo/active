<?php

  /**
   * Base subtask notification
   *
   * @package angie.frameworks.subtasks
   * @subpackage notifications
   */
  abstract class FwBaseSubtaskNotification extends Notification {

    /**
     * Return subtask instance
     *
     * @return Subtask
     */
    function getSubtask() {
      return DataObjectPool::get($this->getAdditionalProperty('subtask_class'), $this->getAdditionalProperty('subtask_id'));
    } // getSubtask

    /**
     * Set subtask
     *
     * @param Subtask $subtask
     * @return FwBaseSubtaskNotification
     */
    function &setSubtask(Subtask $subtask) {
      $this->setAdditionalProperty('subtask_class', get_class($subtask));
      $this->setAdditionalProperty('subtask_id', $subtask->getId());

      return $this;
    } // setSubtask

    /**
     * Return additional template variables
     *
     * @param NotificationChannel $channel
     * @return array
     */
    function getAdditionalTemplateVars(NotificationChannel $channel) {
      return array(
        'subtask' => $this->getSubtask(),
      );
    } // getAdditionalTemplateVars

  }