<?php

  /**
   * Discussion category implementation
   *
   * @package activeCollab.modules.discussions
   * @subpackage models
   */
  class DiscussionCategory extends ProjectObjectCategory {
    
    /**
     * Return discussions posted in this category
     *
     * @param IUser $user
     * @return DBResult
     */
    function getItems(IUser $user) {
      return Discussions::findByCategory($this, STATE_VISIBLE, $user->getMinVisibility());
    } // getItems
    
    /**
     * Return number of discussions in this category
     * 
     * @param IUser $user
     * @return integer
     */
    function countItems(IUser $user) {
      return Discussions::countByCategory($this, STATE_VISIBLE, $user->getMinVisibility());
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
      return 'project_discussion_category';
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
        return parent::canDelete($user) || Discussions::canManage($user, $this->getParent());
      } else {
        return false;
      } // if
    } // canDelete
    
  }