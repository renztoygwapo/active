<?php

  /**
   * Asset cateogry definition
   *
   * @package activeCollab.modules.files
   * @subpackage models
   */
  class AssetCategory extends ProjectObjectCategory {
    
    /**
     * Return assets posted in this category
     *
     * @param IUser $user
     * @return DBResult
     */
    function getItems(IUser $user) {
      return ProjectAssets::findByCategory($this, STATE_VISIBLE, $user->getMinVisibility());
    } // getItems
    
    /**
     * Return number of items in this category
     * 
     * @param IUser $user
     * @return integer
     */
    function countItems(IUser $user) {
      return ProjectAssets::countByCategory($this, STATE_VISIBLE, $user->getMinVisibility());
    } // countItems
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'project_asset_category';
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
        return parent::canDelete($user) || ProjectAssets::canManage($user, $this->getParent());
      } else {
        return false;
      } // if
    } // canDelete
    
  }