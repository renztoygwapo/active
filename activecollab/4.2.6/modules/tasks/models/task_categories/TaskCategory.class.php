<?php

  /**
   * Task category implementation
   *
   * @package activeCollab.modules.tasks
   * @subpackage models
   */
  class TaskCategory extends ProjectObjectCategory {
    
    /**
     * Return task posted in this category
     *
     * @param IUser $user
     * @return DBResult
     */
    function getItems(IUser $user) {
      return Tasks::findByCategory($this, STATE_VISIBLE, $user->getMinVisibility());
    } // getItems
    
    /**
     * Return number of items in a given category
     * 
     * @param IUser $user
     * @return integer
     */
    function countItems(IUser $user) {
      return Tasks::countByCategory($this, STATE_VISIBLE, $user->getMinVisibility());
    } // countItems
    
    /**
     * Routing context name
     *
     * @var string
     */
    private $routing_context = false;
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'project_task_category';
    } // getRoutingContext
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can delete this category
     * 
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      if($user instanceof User) {
        return parent::canDelete($user) || Tasks::canManage($user, $this->getParent());
      } else {
        return false;
      } // if
    } // canDelete
    
  }