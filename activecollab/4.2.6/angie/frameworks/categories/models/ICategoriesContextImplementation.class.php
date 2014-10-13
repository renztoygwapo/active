<?php

  /**
   * Categories context implementation
   *
   * @package angie.frameworks.categories
   * @subpackage models
   */
  class ICategoriesContextImplementation {
    
    /**
     * Parent object instance
     *
     * @var ICategoriesContext
     */
    protected $object;
    
    /**
     * Construct object's categories context helper
     *
     * @param ICategoriesContext $object
     */
    function __construct(ICategoriesContext $object) {
      $this->object = $object;
    } // __construct
    
    /**
     * Cached categories
     *
     * @var array
     */
    private $categories = array();
    
    /**
     * Return categories, optionally filtered by type
     *
     * @param string $type
     */
    function get($type = null) {
      $cache_index = $type ? $type : '-- All --';
      
      if(!isset($this->categories[$cache_index])) {
        $this->categories[$cache_index] = Categories::findBy($this->object, $type);
      } // if
      
      return $this->categories[$cache_index];
    } // get
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns ture if $user can manage categories
     *
     * @param User $user
     * @param string $type
     * @return boolean
     */
    function canManage(User $user, $type = null) {
      return $user->isAdministrator();
    } // canManage

    // ---------------------------------------------------
    //  URLs
    // ---------------------------------------------------

    /**
     * Return manage categories URL
     *
     * @return string
     * @throws NotImplementedError
     */
    function getManageCategoriesUrl() {
      if($this->object instanceof IRoutingContext) {
        return Router::assemble($this->object->getRoutingContext() . '_categories', $this->object->getRoutingContextParams());
      } else {
        throw new NotImplementedError(__METHOD__);
      } // if
    } // getManageCategoriesUrl
    
  }