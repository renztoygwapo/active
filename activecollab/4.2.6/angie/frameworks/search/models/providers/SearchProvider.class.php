<?php

  /**
   * Search provider foundation
   * 
   * @package angie.frameworks.search
   * @subpackage models
   */
  abstract class SearchProvider {

    /**
     * Return name of the search provider
     *
     * @return string
     */
    function getName() {
      return 'Unknown Search Provider'; // No need to localise this - I added these values just so we don't need to make these methods abstract and potentialy break backward compatibility
    } // getName

    /**
     * Return search provider description
     *
     * @return string
     */
    function getDescription() {
      return 'Description not provided'; // No need to localise this - I added these values just so we don't need to make these methods abstract and potentialy break backward compatibility
    } // getDescription

    /**
     * Return provider specific settings
     *
     * @return mixed
     */
    function getSettings() {
      return null;
    } // getSettings

    /**
     * Return render settings template
     *
     * @return string
     */
    function getRenderSettingsTemplate() {
      return null;
    } // getRenderSettingsTemplate

    /**
     * Set provider data from settings array that gets submitted
     *
     * @param array $settings
     */
    function setSettings($settings) {

    } // setSettings
    
    /**
     * Query index for given search string
     *
     * @param IUser $user
     * @param SearchIndex $index
     * @param string $search_for
     * @param mixed $criterions
     * @return array
     */
    abstract function query(IUser $user, SearchIndex $index, $search_for, $criterions = null);
    
    /**
     * Query paginated
     * 
     * @param IUser $user
     * @param SearchIndex $index
     * @param string $search_for
     * @param mixed $criterions
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    abstract function queryPaginated(IUser $user, SearchIndex $index, $search_for, $criterions = null, $page = 1, $per_page = 30);
    
    /**
     * Add or update item in the index
     * 
     * @param string $index
     * @param string $item_class
     * @param integer $item_id
     * @param string $item_context
     * @param mixed $additional
     */
    abstract function set($index, $item_class, $item_id, $item_context = null, $additional = null);
    
    /**
     * Remove given item from a given index
     * 
     * @param mixed $index
     * @param string $item_class
     * @param integer $item_id
     */
    abstract function remove($index, $item_class, $item_id);
    
    /**
     * Clear given index
     * 
     * @param string $index_name
     */
    abstract function clear($index_name);
    
    /**
     * Update item context in a given index
     * 
     * @param SearchIndex $index
     * @param IObjectContext $item
     * @param string $old_context
     * @param string $new_context
     */
    abstract function updateItemContext(SearchIndex $index, IObjectContext $item, $old_context, $new_context);
    
    /**
     * Returns true if $index is initalized
     * 
     * @param SearchIndex $index
     * @param boolean $use_cache
     * @return boolean
     */
    abstract function isInitialized(SearchIndex $index, $use_cache = true);

    /**
     * Initialize entire environment used by this provider
     */
    abstract function initializeEnvironment();
    
    /**
     * Initalize given index
     * 
     * @param SearchIndex $index
     */
    abstract function initialize(SearchIndex $index);
    
    /**
     * Tear down given search index
     * 
     * @param SearchIndex $index
     */
    abstract function tearDown(SearchIndex $index);
    
    /**
     * Return total number of records in given index
     * 
     * @param SearchIndex $index
     * @return integer
     */
    abstract function countRecords(SearchIndex $index);
    
    /**
     * Return file size of the index
     * 
     * @param SearchIndex $index
     * @return
     */
    abstract function calculateSize(SearchIndex $index);
    
    // ---------------------------------------------------
    //  Tips
    // ---------------------------------------------------
    
    /**
     * Returns true if this engine has any tips
     */
    function hasTips() {
      return false;
    } // hasTips
    
    /**
     * Return tips for this particular search engine
     * 
     * @return array
     */
    function getTips() {
      return null;
    } // getTips
    
  }