<?php

  /**
   * Task comments helper implementation
   *
   * @package activeCollab.modules.tasks
   * @subpackage models
   */
  class ITaskCommentsImplementation extends IProjectObjectCommentsImplementation {
    
    /**
     * Create a new comment instance
     *
     * @return TaskComment
     */
    function newComment() {
      $comment = new TaskComment();
      $comment->setParent($this->object);
      
      return $comment;
    } // newComment
    
    /**
     * Submit the comment and pick up the default setting for task reopening
     *
     * @param string $body
     * @param IUser $by
     * @param array $additional
     * @return TaskComment
     */
    function submit($body, $by, $additional = null) {
      if(empty($additional) || empty($additional['reopen_if_completed'])) {
        $reopen = false;

        if(ConfigOptions::getValue('tasks_auto_reopen')) {
          if(ConfigOptions::getValue('tasks_auto_reopen_clients_only')) {
            $reopen = $by instanceof AnonymousUser ? true : !$by->isOwner(); // Anonymous user or a client
          } else {
            $reopen = true;
          } // if
        } // if

        if(empty($additional)) {
          $additional = array('reopen_if_completed' => $reopen);
        } else {
          $additional['reopen_if_completed'] = $reopen;
        } // if
      } // if
    	
    	return parent::submit($body, $by, $additional);
    } // function
    
  }