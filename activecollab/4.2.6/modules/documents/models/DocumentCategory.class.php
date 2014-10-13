<?php

  /**
   * Document category implementation
   *
   * @package activeCollab.modules.documents
   * @subpackage models
   */
  class DocumentCategory extends Category {
    
    /**
     * Return documents
     *
     * @param IUser $user
     * @return DBResult
     */
    function getItems(IUser $user) {
      return Documents::findByCategory($this, STATE_VISIBLE, $user->getMinVisibility());
    } // getItems
    
    /**
     * Return number of items that are in this category
     * 
     * @param IUser $user
     * @return integer
     */
    function countItems(IUser $user) {
      return Documents::countByCategory($this, STATE_VISIBLE, $user->getMinVisibility());
    } // countItems
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can rename this category
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return Documents::canManage($user);
    } // canEdit
    
    /**
     * Returns true if user can delete this category
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return Documents::canManage($user);
    } // canDelete
    
    // ---------------------------------------------------
    //  URLs
    // ---------------------------------------------------

    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'document_category';
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
    //  System
    // ---------------------------------------------------
    
    /**
     * Remove documents category
     *
     * @return boolean
     * @throws Exception
     */
    function delete() {
      try {
        DB::beginWork('Removing document category @ ' . __CLASS__);
        
        parent::delete();
        DB::execute('UPDATE ' . TABLE_PREFIX . 'documents SET category_id = ? WHERE category_id = ?', null, $this->getId());
        
        DB::commit('Document category removed @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to remove document category @ ' . __CLASS__);
        
        throw $e;
      } // try
      
      return true;
    } // delete
    
  }