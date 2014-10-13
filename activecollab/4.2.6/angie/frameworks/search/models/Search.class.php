<?php

  /**
   * Main application search interface class
   * 
   * @package angie.frameworks.search
   * @subpackage models
   */
  final class Search {

    /**
     * Return list of available search providers
     *
     * @return SearchProvider[]
     */
    static function getAvailableProviders() {
      $providers = array(
        new MySqlSearchProvider(),
        new ElasticSearchProvider(),
      );

      EventsManager::trigger('on_search_providers', array(&$providers));

      usort($providers, function(SearchProvider $a, SearchProvider $b) {
        return strcmp($a->getName(), $b->getName());
      });

      return $providers;
    } // getAvailableProviders
    
    /**
     * Selected provider
     *
     * @var SearchProvider
     */
    private static $provider = false;
    
    /**
     * Return loaded authentication provider
     *
     * @return SearchProvider
     * @throws InvalidParamError
     */
    static function &getProvider() {
      if(self::$provider === false) {
        $seach_provider_class = ConfigOptions::getValue('search_provider');
        
        if($seach_provider_class && class_exists($seach_provider_class)) {
          self::$provider = new $seach_provider_class();
        } // if
        
        if(empty(self::$provider) || !(self::$provider instanceof SearchProvider)) {
          throw new InvalidParamError('search_provider', $seach_provider_class, "Search provider class '$seach_provider_class' not found or invalid");
        } // if
      } // if
      
      return self::$provider;
    } // getProvider
    
    /**
     * Query index for given search string
     * 
     * $index can be search index instance or name of the index that needs to be 
     * searched
     * 
     * $criterions is a list of additional search filters and criterions
     * 
     * @param IUser $user
     * @param mixed $index
     * @param string $search_for
     * @param array $criterions
     * @return array
     * @throws InvalidInstanceError
     */
    static function query(IUser $user, $index, $search_for, $criterions = null) {
      if($user instanceof IUser) {
        return self::getProvider()->query($user, ($index instanceof SearchIndex ? $index : Search::getIndex($index)), $search_for, $criterions);
      } else {
        throw new InvalidInstanceError('user', $user, 'IUser');
      } // if
    } // query
    
    /**
     * Query search index and return paged search results
     * 
     * @param IUser $user
     * @param SearchIndex|string $index
     * @param string $search_for
     * @param mixed $criterions
     * @param integer $page
     * @param integer $per_page
     * @return array
     * @throws InvalidInstanceError
     */
    static function queryPaginated(IUser $user, $index, $search_for, $criterions = null, $page = 1, $per_page = 30) {
      if($user instanceof IUser) {
        return self::getProvider()->queryPaginated($user, ($index instanceof SearchIndex ? $index : Search::getIndex($index)), $search_for, $criterions, $page, $per_page);
      } else {
        throw new InvalidInstanceError('user', $user, 'IUser');
      } // if
    } // queryPaginated
  
    /**
     * Add / update index data
     * 
     * $item can be an instance of ISearchItem class or array with proper set of 
     * properties (class and id fields are required)
     * 
     * @param SearchIndex|string $index
     * @param mixed $item
     * @return mixed
     * @throws InvalidParamError
     */
    static function set($index, $item) {
      $index = $index instanceof SearchIndex ? $index : Search::getIndex($index);
      
      if($item instanceof ISearchItem) {
        return self::getProvider()->set($index, get_class($item), $item->getId(), $item->search()->getContext($index), $item->search()->getAdditional($index));
      } elseif(is_array($item)) {
        $item_id = array_required_var($item, 'id', true);
        $item_class = array_required_var($item, 'class', true);
        $item_context = array_var($item, 'context', null, true);
        
        return self::getProvider()->set($index, $item_class, $item_id, $item_context, $item);
      } else {
        throw new InvalidParamError('item', $item, '$item is expected to be an array of instance of ISearchItem class');
      } // if
    } // set
    
    /**
     * Remove an item from a given index
     * 
     * @param string $index
     * @param ISearchItem|array $item
     * @return mixed
     * @throws InvalidParamError
     */
    static function remove($index, $item) {
      if($item instanceof ISearchItem) {
        return self::getProvider()->remove($index, get_class($item), $item->getId());
      } elseif(is_array($item)) {
        $item_id = array_required_var($item, 'id', true);
        $item_class = array_required_var($item, 'class', true);
        
        return self::getProvider()->remove($index, $item_class, $item_id);
      } else {
        throw new InvalidParamError('item', $item, '$item is expected to be an array of instance of ISearchItem class');
      } // if
    } // remove
    
    /**
     * Update context of a given item
     * 
     * @param IObjectContext $item
     * @param string $old_context
     * @param string $new_context
     */
    static function updateItemContext(IObjectContext $item, $old_context, $new_context) {
      foreach(Search::getIndices() as $index) {
        self::getProvider()->updateItemContext($index, $item, $old_context, $new_context);
      } // foreach
    } // updateItemContext
    
    // ---------------------------------------------------
    //  Tips
    // ---------------------------------------------------
    
    /**
     * Return true if active search provider has some search tips
     * 
     * @return boolean
     */
    static function hasTips() {
      return self::getProvider()->hasTips();
    } // hasTips
    
    /**
     * Return array of search tips
     * 
     * @return array
     */
    static function getTips() {
      return self::getProvider()->getTips();
    } // getTips
    
    // ---------------------------------------------------
    //  Indice management
    // ---------------------------------------------------
    
    /**
     * Initialize defined indices
     * 
     * This functions goes through all defined indices and initializes them, in case they are not already initialized
     *
     * @param boolean $force
     */
    static function initialize($force = false) {
      if(empty($force) && ConfigOptions::getValue('search_initialized_on')) {
        return;
      } // if

      self::getProvider()->initializeEnvironment();
      
      foreach(Search::getIndices() as $index) {
        if(!$index->isInitialized(false)) {
          self::getProvider()->initialize($index);
        } // if
      } // foreach
      
      ConfigOptions::setValue('search_initialized_on', time());
    } // initialize
    
    /**
     * Cached list of loaded indices
     *
     * @var array
     */
    static private $indices = false;
    
    /**
     * Return list of indices
     * 
     * @return SearchIndex[]
     */
    static function getIndices() {
      if(self::$indices === false) {
        self::$indices = array();
        
        $provider =& self::getProvider();
        
        EventsManager::trigger('on_search_indices', array(&self::$indices, &$provider));
      } // if
      
      return self::$indices;
    } // getIndices
    
    /**
     * Return a specific indice
     * 
     * @param string $index
     * @return SearchIndex
     * @throws InvalidParamError
     */
    static function getIndex($index) {
      if(self::$indices === false) {
        self::getIndices();
      } // if
      
      if(self::$indices && isset(self::$indices[$index]) && self::$indices[$index] instanceof SearchIndex) {
        return self::$indices[$index];
      } else {
        throw new InvalidParamError('index', $index);
      } // if
    } // getIndice
    
  }
  
  Search::initialize();