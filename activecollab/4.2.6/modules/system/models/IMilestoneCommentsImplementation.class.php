<?php

  /**
   * Milestone comments helper implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IMilestoneCommentsImplementation extends IProjectObjectCommentsImplementation {
  
    /**
     * Create a new comment instance
     *
     * @return MilestoneComment
     */
    function newComment() {
      $comment = new MilestoneComment();
      $comment->setParent($this->object);
      
      return $comment;
    } // newComment
    
  }