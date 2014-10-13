<?php

  /**
   * Search item implementation
   * 
   * @package angie.frameworks.search
   * @subpackage subpackage
   */
  abstract class ISearchItemImplementation {
  
    /**
     * Parent object
     *
     * @var ISearchItem|IObjectContext
     */
    protected $object;
    
    /**
     * Construct search item implementation
     * 
     * @param ISearchItem $object
     * @throws InvalidInstanceError
     */
    function __construct(ISearchItem $object) {
      if($object instanceof ISearchItem) {
        $this->object = $object;
      } else {
        throw new InvalidInstanceError('object', $object, 'ISearchItem');
      } // if
    } // __construct
    
    /**
     * Return list of indices that index parent object
     * 
     * Result is an array where key is the index name, while value is list of 
     * fields that's watched for changes
     * 
     * @return array
     */
    abstract function getIndices();
    
    /**
     * Return item context for given index
     * 
     * @param SearchIndex $index
     * @return string
     * @throws InvalidInstanceError
     */
    function getContext(SearchIndex $index) {
      if($this->object instanceof IObjectContext) {
        return $this->object->getObjectContextDomain() . ':' . $this->object->getObjectContextPath();
      } else {
        throw new InvalidInstanceError('$this->object', $this->object, 'IObjectContext');
      } // if
    } // getContext
    
    /**
     * Return additional properties for a given index
     * 
     * @param SearchIndex $index
     * @return mixed
     */
    function getAdditional(SearchIndex $index) {
      return null;
    } // getAdditional
    
    // ---------------------------------------------------
    //  Management
    // ---------------------------------------------------
    
    /**
     * Create records in search indices
     */
    function create() {
      foreach($this->getIndices() as $index => $fields) {
        Search::set($index, $this->object);
      } // foreach
    } // create
    
    /**
     * Update indices on parent update
     * 
     * @param array $modifications
     * @param boolean $force
     */
    function update($modifications = null, $force = false) {
      if($modifications && count($modifications)) {
        foreach($this->getIndices() as $index_name => $watched_fields) {
          $index = Search::getIndex($index_name);

          if($index instanceof SearchIndex) {
            if($this->object instanceof IState && isset($modifications['state'])) {
              $this->updateWhenStateIsChanged($index, $modifications);
            } else {
              $this->updateWhenStateIsNotChanged($index, $watched_fields, $modifications, $force);
            } // if
          } // if
        } // foreach
      } // if
    } // update

    /**
     * Update item that has a state change
     *
     * @param SearchIndex $index
     * @param array $modifications
     */
    private function updateWhenStateIsChanged(SearchIndex $index, $modifications) {
      $min_state = $index->getMinState();

      if($min_state !== null) {
        list($old_state, $new_state) = $modifications['state'];

        if($new_state >= $min_state) {
          Search::set($index, $this->object);
        } else {
          Search::remove($index, $this->object);
        } // if
      } // if
    } // updateWhenStateIsChanged

    /**
     * Update item when that either don't have state or state remained the same
     *
     * @param SearchIndex $index
     * @param array $watched_fields
     * @param array $modifications
     * @param boolean $force
     */
    private function updateWhenStateIsNotChanged(SearchIndex $index, $watched_fields, $modifications, $force) {
      if($force) {
        Search::set($index, $this->object);
      } else {
        foreach($modifications as $k => $v) {
          if(in_array($k, $watched_fields)) {
            Search::set($index, $this->object);
            break;
          } // if
        } // foreach
      } // if
    } // updateWhenStateIsNotChanged
    
    /**
     * Remove related records from indices
     */
    function clear() {
      foreach($this->getIndices() as $index => $fields) {
        Search::remove($index, $this->object);
      } // foreach
    } // clear

    // ---------------------------------------------------
    //  State
    // ---------------------------------------------------



    // ---------------------------------------------------
    //  Describe
    // ---------------------------------------------------

    /**
     * Describe for search
     *
     * @param IUser $user
     * @return array
     */
    function describeForSearch(IUser $user) {
      return array(
        'id' => $this->object->getId(),
        'type' => get_class($this->object),
        'type_underscore' => $this->object->getBaseTypeName(),
        'verbose_type' => $this->object->getVerboseType(),
        'name' => $this->object->getName(),
        'permalink' => $this->object->getViewUrl(),
        'is_crossed_over' => $this->object instanceof IComplete && $this->object->complete()->isCompleted(),
      );
    } // describeForSearch
    
  }