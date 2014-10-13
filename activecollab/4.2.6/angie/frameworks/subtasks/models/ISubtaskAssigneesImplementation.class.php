<?php

  /**
   * Subtask specific assignees implementation
   *
   * @package angie.frameworks.subtasks
   * @subpackage models
   */
  class ISubtaskAssigneesImplementation extends IAssigneesImplementation {

    /**
     * Subtasks don't support other assignees
     *
     * @var bool
     */
    protected $support_multiple_assignees = false;

    /**
     * Send email notifications about re-assignment
     *
     * @param User $old_assignee
     * @param User $new_assignee
     * @param User $reassigned_by
     */
    function notifyOnReassignment($old_assignee, $new_assignee, User $reassigned_by) {
      $notify_new_assignee = $notify_old_assignee = false;

      if($old_assignee instanceof User && $new_assignee instanceof User) {
        if($old_assignee->getId() != $new_assignee->getId()) {
          $notify_new_assignee = $notify_old_assignee = true;
        } // if
      } elseif($old_assignee instanceof User) {
        $notify_old_assignee = true;
      } elseif($new_assignee instanceof User) {
        $notify_new_assignee = true;
      } // if

      if($notify_new_assignee) {
        AngieApplication::notifications()
          ->notifyAbout(SUBTASKS_FRAMEWORK_INJECT_INTO . '/notify_new_subtask_assignee', $this->object->getParent(), $reassigned_by)
          ->setSubtask($this->object)
          ->setIsReassigned($old_assignee instanceof User)
          ->sendToUsers($new_assignee);
      } // if

      if($notify_old_assignee) {
        AngieApplication::notifications()
          ->notifyAbout(SUBTASKS_FRAMEWORK_INJECT_INTO . '/notify_old_subtask_assignee', $this->object->getParent(), $reassigned_by)
          ->setSubtask($this->object)
          ->sendToUsers($old_assignee);
      } // if
    } // notifyOnReassignment

  }