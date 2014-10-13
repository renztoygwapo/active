<?php

  /**
   * Project category class
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ProjectCategory extends Category {
    
    /**
     * Return task posted in this category
     *
     * @param IUser $user
     * @return DBResult
     */
    function getItems(IUser $user) {
      return Projects::findByCategory($user, $this);
    } // getItems
    
    /**
     * Return number of projects that are in this category
     * 
     * @param IUser $user
     * @return integer
     */
    function countItems(IUser $user) {
      return Projects::countByCategory($user, $this);
    } // countItems
    
    // ---------------------------------------------------
    //  Interfaces
    // ---------------------------------------------------
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'project_category';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('category_id' => $this->getId());
    } // getRoutingContextParams
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Check if $user can update this project category
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return $user->isProjectManager();
    } // canEdit
    
    /**
     * Return true if $user can delete this category
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return $user->isProjectManager();
    } // canDelete
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Delete this object from database
     *
     * @return boolean
     */
    function delete() {
      try {
        DB::beginWork('Removing project category @ ' . __CLASS__);
        
        parent::delete();
        DB::execute('UPDATE ' . TABLE_PREFIX . 'projects SET category_id = ? WHERE category_id = ?', null, $this->getId());
        
        DB::commit('Project category removed @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to remove project category @ ' . __CLASS__);
        
        throw $e;
      } // try
      
      return true;
    } // delete
  
  }

?>