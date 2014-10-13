<?php

  /**
   * Homescreen helper implementation
   * 
   * @package angie.frameworks.homescreens
   * @subpackage models
   */
  class IHomescreenImplementation {
    
    /**
     * Parent object
     *
     * @var User|IHomescreen
     */
    protected $object;
  
    /**
     * Construct deksktop helper instance
     * 
     * @param User|IHomescreen $object
     * @throws InvalidInstanceError
     */
    function __construct(IHomescreen $object) {
      if($object instanceof User) {
        $this->object = $object;
      } else {
        throw new InvalidInstanceError('object', $object, 'User');
      } // if
    } // __construct

    /**
     * Return home screen tabs
     *
     * @return HomescreenTab[]
     */
    function getTabs() {
      $result = array();

      $custom_tabs = HomescreenTabs::findByUser($this->object);

      if($custom_tabs) {
        foreach($custom_tabs as $custom_tab) {
          $result[] = $custom_tab;
        } // foreach
      } // if

      return $result;
    } // getTabs

    /**
     * Returns true if parent object can have a descktop sec configured for it
     *
     * @return boolean
     */
    function canHaveOwn() {
      return true; // return $this->object->isMember();
    } // canHaveOwnSet
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------

    /**
     * Return manage home screen URL
     *
     * @return string
     */
    function getManageUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_homescreen', $this->object->getRoutingContextParams());
    } // getManageUrl

    /**
     * Return add home screen tab URL
     *
     * @return string
     */
    function getAddTabUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_homescreen_tabs_add', $this->object->getRoutingContextParams());
    } // getAddTabUrl

    /**
     * Return reorder tabs URL
     *
     * @return string
     */
    function getReorderTabsUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_homescreen_tabs_reorder', $this->object->getRoutingContextParams());
    } // getReorderTabsUrl
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can manage home screen for parent object
     * 
     * @param IUser $user
     * @return boolean
     * @throws InvalidInstanceError
     */
    function canManageSet(IUser $user) {
      if($user instanceof IUser) {
        return $this->object->canEdit($user);
      } else {
        throw new InvalidInstanceError('user', $user, 'User');
      } // if
    } // canManageSet
    
  }