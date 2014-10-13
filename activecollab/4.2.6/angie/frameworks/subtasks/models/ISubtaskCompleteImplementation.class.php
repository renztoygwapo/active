<?php

  /**
   * Subtask specific complete implementation
   *
   * @package angie.frameworks.subtasks
   * @subpackage models
   */
  class ISubtaskCompleteImplementation extends ICompleteImplementation {

    /**
     * Mark this object as completed
     *
     * @param User $by
     * @param Comment $comment
     */
    function complete(IUser $by, $comment = null) {
      parent::complete($by, $comment);
      
      if($this->object->getParent() instanceof ISubtasks) {
        AngieApplication::cache()->removeByObject($this->object->getParent(), 'subtasks_count');
      } // if
    } // complete
    
    /**
     * Mark this item as opened
     *
     * @param User $by
     * @param Comment $comment
     * @return boolean
     */
    function open(IUser $by, $comment = null) {
      parent::open($by, $comment);
      
      if($this->object->getParent() instanceof ISubtasks) {
        AngieApplication::cache()->removeByObject($this->object->getParent(), 'subtasks_count');
      } // if
    } // open
    
  }