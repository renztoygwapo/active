<?php

  /**
   * Framework level category implementation
   *
   * @package angie.frameworks.categories
   * @subpackage models
   */
  abstract class FwCategory extends BaseCategory implements IRoutingContext, IHistory {
    
    /**
     * Return type name
     * 
     * By default, this function will return 'category', but it can be changed 
     * in 'group', 'collection' etc - whatever fits the needs of the specific 
     * situation where specific category type is used
     *
     * @return string
     */
    function getTypeName() {
      return 'category';
    } // getTypeName
    
    /**
     * Return items posted in this category
     *
     * @param IUser $user
     */
    abstract function getItems(IUser $user);
    
    /**
     * Return number of items that are in this category
     * 
     * @param IUser $user
     * @return integer
     */
    abstract function countItems(IUser $user);
    
    /**
     * Set attributes
     *
     * @param array $attributes
     */
    function setAttributes($attributes) {
      if(is_array($attributes) && isset($attributes['name'])) {
        $attributes['name'] = trim($attributes['name']);
      } // if
      
      parent::setAttributes($attributes);
    } // setAttributes
    
    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
      $result = parent::describe($user, $detailed, $for_interface);
      
      if($detailed) {
        $result['items'] = array();
        
        $items = $this->getItems($user);
        if($items) {
          foreach($items as $item) {
            $result['items'][] = $item->describe($user, false, $for_interface);
          } // foreach
        } // if
      } else {
        $result['items_count'] = $this->countItems($user);
      } // if
      
      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      $result = parent::describeForApi($user, $detailed);

      // activeCollab Timer legacy (@todo)
      if(!isset($result['type'])) {
        $result['type'] = get_class($this);
      } // if

      if(!isset($result['parent_type']) || !isset($result['parent_id'])) {
        $result['parent_type'] = $this->getParentType();
        $result['parent_id'] = $this->getParentId();
      } // if

      // / activeCollab Timer legacy

      if($detailed) {
        $result['items'] = array();

        $items = $this->getItems($user);
        if($items) {
          foreach($items as $item) {
            $result['items'][] = $item->describeForApi($user);
          } // foreach
        } // if
      } else {
        $result['items_count'] = $this->countItems($user);
      } // if

      return $result;
    } // describeForApi
    
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------
    
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
      if($this->routing_context === false) {
        $this->routing_context = $this->getParent()->getRoutingContext() . '_category';
      } // if
      
      return $this->routing_context;
    } // getRoutingContext
    
    /**
     * Routing context parameters
     *
     * @var array
     */
    private $routing_context_params = false;
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      if($this->routing_context_params === false) {
        $this->routing_context_params = is_array($this->getParent()->getRoutingContextParams()) ? 
          array_merge($this->getParent()->getRoutingContextParams(), array('category_id' => $this->getId())) : 
          array('category_id' => $this->getId());
      } // if
      
      return $this->routing_context_params;
    } // getRoutingContextParams
    
    /**
     * Cached history helper instance
     *
     * @var IHistoryImplementation
     */
    private $history = false;
    
    /**
     * Return category history helper instance
     *
     * @return IHistoryImplementation
     */
    function history() {
      if($this->history === false) {
        $this->history = new IHistoryImplementation($this);
      } // if
      
      return $this->history;
    } // history
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return section URL
     *
     * @return string
     */
    function getSectionUrl() {
      return Router::assemble($this->getParent()->getRoutingContext() . '_categories', $this->getParent()->getRoutingContextParams());
    } // getSectionUrl
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Validate model before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if($this->validatepresenceOf('name')) {
        if($this->getParentType() && $this->getParentId()) {
          $validate_uniqueness = $this->validateUniquenessOf('parent_type', 'parent_id', 'type', 'name');
        } else {
          $validate_uniqueness = $this->validateUniquenessOf('type', 'name');
        } // if
        
        if(!$validate_uniqueness) {
          $errors->addError(lang('Name needs to be unique'), 'name');
        } // if
      } else {
        $errors->addError(lang('Name is required'), 'name');
      } // if
    } // validate
    
  }