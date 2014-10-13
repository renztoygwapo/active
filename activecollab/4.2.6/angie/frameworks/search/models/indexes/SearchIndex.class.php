<?php

  /**
   * Foundation class that every search index extends
   * 
   * @package angie.frameworks.search
   * @subpackage models
   */
  abstract class SearchIndex {

    // Types of ID-s
    const ID_NUMERIC = 'numeric';
    const ID_STRING = 'string';
    
    // Index filed types
    const FIELD_NUMERIC = 'numeric';
    const FIELD_DATE = 'date';
    const FIELD_DATETIME = 'datetime';
    const FIELD_STRING = 'string';
    const FIELD_TEXT = 'text';
    
    /**
     * Provider used to store and query the data
     *
     * @var SearchProvider
     */
    protected $provider;
    
    /**
     * Construct search index
     * 
     * @param SearchProvider $provider
     */
    function __construct(SearchProvider &$provider) {
      $this->provider = $provider;
    } // __construct
    
    /**
     * Return short name of this index
     * 
     * @return string
     */
    abstract function getShortName();
  
    /**
     * Return index name
     * 
     * @return string
     */
    abstract function getName();
    
    /**
     * Return index fields
     */
    abstract function getFields();

    /**
     * Return type of ID field
     *
     * @return string
     */
    function getIdType() {
      return SearchIndex::ID_NUMERIC;
    } // getIdType

    /*
     * Return array of priority fields with their priority
     *
     * @return mixed
     */
    function getPriorityFields() {
      if (in_array('name', array_keys($this->getFields()))) {
        return array('name');
      } // if
      return null;
    } // getPriorityFields

    /**
     * Return min state
     *
     * @return int
     */
    function getMinState() {
      return STATE_ARCHIVED;
    } // getMinState
    
    /**
     * Return array of available filters
     * 
     * Filters are indexed by field and can be either array of values or array 
     * of arrays of values
     * 
     * @return array
     */
    function getFilters() {
      return array();
    } // getFilters
    
    /**
     * Return object context domains that this index searchs through
     * 
     * @return string
     */
    function getObjectContextDomains() {
      return null;
    } // getObjectContextDomains
    
    /**
     * Return context filter for a given user
     * 
     * @param IUser $user
     * @return string|array
     */
    function getUserFilter(IUser $user) {
      return null;
    } // getUserFilter
    
    /**
     * Return true if this index is considered advanced search
     * 
     * @return boolean
     */
    function isAdvanced() {
      return true;
    } // isAdvanced
    
    /**
     * Return true if this search index is initialized
     * 
     * @param boolean $use_cache
     * @return boolean
     */
    function isInitialized($use_cache = true) {
      return $this->provider->isInitialized($this, $use_cache);
    } // isInitialized
    
    /**
     * Initialize this search index
     */
    function initialize() {
      return $this->provider->initialize($this);
    } // initialize
    
    /**
     * Tear down index
     */
    function tearDown() {
      return $this->provider->tearDown($this);
    } // tearDown
    
    /**
     * Return total number of records in this index
     * 
     * @return integer
     */
    function countRecords() {
      return $this->provider->countRecords($this);
    } // countRecords
    
    /**
     * Return file size of this index
     * 
     * @return integer
     */
    function calculateSize() {
      return $this->provider->calculateSize($this);
    } // calculateSize

    /**
     * Query index for the given terms
     * 
     * @param IUser $user
     * @param string $search_for
     * @param array|null $criterions
     * @return array
     * @throws InvalidInstanceError
     */
    function query(IUser $user, $search_for, $criterions = null) {
      if($user instanceof IUser) {
        return $this->provider->query($user, $this, $search_for, $criterions);
      } else {
        throw new InvalidInstanceError('user', $user, 'IUser');
      } // if
    } // query

    /**
     * Return item instance
     *
     * @param IUser $user
     * @param string $item_class
     * @param string $item_id
     * @return DataObject
     */
    function loadItemDetails(IUser $user, $item_class, $item_id) {
      if($item_class && $item_id && class_exists($item_class, true)) {
        $item = DataObjectPool::get($item_class, $item_id);

        if($item instanceof ISearchItem && $item->isLoaded()) {
          return $item->search()->describeForSearch($user);
        } // if
      } // if

      return null;
    } // loadItemDetails
    
    /**
     * Add or update item in the index
     * 
     * $item can implement ISearchItem interface or be an array of fields that 
     * need to be added to the interface
     *
     * @param $item_class
     * @param $item_id
     * @param string $item_context
     * @param null|array $additional
     */
    function set($item_class, $item_id, $item_context = null, $additional = null) {
      $this->provider->set($this, $item_class, $item_id, $item_context, $additional);
    } // set
    
    /**
     * Remove an item from the interface
     *
     * @param string $item_class
     * @param integer $item_id
     */
    function remove($item_class, $item_id) {
      $this->provider->remove($this, $item_class, $item_id);
    } // remove
    
    /**
     * Drop all items from the interface
     */
    function clear() {
      $this->provider->clear($this->getShortName());
    } // clear
    
    // ---------------------------------------------------
    //  Rebuild
    // ---------------------------------------------------
    
    /**
     * Return steps to rebuild this search index
     */
    function getRebuildSteps() {
      return array(
        array(
          'text' => lang('Initialize Index'), 
        	'url' => $this->getReinitUrl(),
        ),
      );
    } // getRebuildSteps
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return rebuild index URL
     * 
     * @return string
     */
    function getRebuildUrl() {
      return Router::assemble('search_index_admin_rebuild', array('search_index_name' => $this->getShortName()));
    } // getRebuildUrl
    
    /**
     * Return check index URL
     * 
     * @return string
     */
    function getReinitUrl() {
      return Router::assemble('search_index_admin_reinit', array('search_index_name' => $this->getShortName()));
    } // getReinitUrl
    
  }