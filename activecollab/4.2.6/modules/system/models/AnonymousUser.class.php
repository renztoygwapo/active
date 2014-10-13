<?php

  /**
   * Anonymous user class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class AnonymousUser extends FwAnonymousUser {
    
    /**
     * Returns true if this user is member of owner company
     *
     * @return boolean
     */
    function isOwner() {
      return false;
    } // isOwner
    
    /**
     * Returns true if this user has management permissions in People section
     *
     * @return boolean
     */
    function isPeopleManager() {
      return false;
    } // isPeopleManager
    
    /**
     * Returns true if this user has global project management permissions
     *
     * @return boolean
     */
    function isProjectManager() {
      return false;
    } // isProjectManager
    
  }