<?php

  /**
   * User favorites implementation
   * 
   * @package angie.frameworks.favorites
   * @subpackage models
   */
  class IUserFavoritesImplementation {
  
    /**
     * Parent user instance
     *
     * @var User
     */
    protected $object;
    
    /**
     * Construct user favorites helper
     * 
     * @param User $object
     */
    function __construct(User $object) {
      $this->object = $object;
    } // __construct
    
    /**
     * Get objects that are added to favorites
     * 
     * @return array
     */
    function get() {
      
    } // get
    
    /**
     * Returns true if $object is favorite for parent user
     * 
     * @param ICanBeFavorite $object
     * @return boolean
     */
    function isFavorite(ICanBeFavorite $object) {
      return Favorites::isFavorite($object, $this->object);
    } // isFavorite
    
    /**
     * Add object to favorites
     * 
     * @param ICanBeFavorite $object
     */
    function add(ICanBeFavorite $object) {
      Favorites::addToFavorites($object, $this->object);
    } // add
    
    /**
     * Remove given object from favorites
     * 
     * @param ICanBeFavorite $object
     */
    function remove(ICanBeFavorite $object) {
      Favorites::removeFromFavorites($object, $this->object);
    } // remove
    
    // ---------------------------------------------------
    //  Describe
    // ---------------------------------------------------
    
    /**
     * Describe subscription of the parent object for $user
     *
     * @param ICanBeFavorite $object
     * @param boolean $detailed
     * @param boolean $for_interface
     * @param array $result
     */
    function describeObject(ICanBeFavorite $object, $detailed, $for_interface, &$result) {
      $result['is_favorite'] = $this->isFavorite($object);

    	$result['urls']['add_to_favorites'] = $this->getAddToFavoritesUrl($object);
    	$result['urls']['remove_from_favorites'] = $this->getRemoveFromFavoritesUrl($object);
    } // describe

    /**
     * Describe subscription of the parent object for $user
     *
     * @param ICanBeFavorite $object
     * @param boolean $detailed
     * @param array $result
     */
    function describeObjectForApi(ICanBeFavorite $object, $detailed, &$result) {
      $result['is_favorite'] = $this->isFavorite($object);

      if($detailed) {
        $result['urls']['add_to_favorites'] = $this->getAddToFavoritesUrl($object);
        $result['urls']['remove_from_favorites'] = $this->getRemoveFromFavoritesUrl($object);
      } // if
    } // describeObjectForApi
    
    // ---------------------------------------------------
    //  URl-s
    // ---------------------------------------------------
    
    /**
     * Return add to favorites URL
     * 
     * @param ICanBeFavorite $object
     * @return string
     */
    function getAddToFavoritesUrl(ICanBeFavorite $object) {
      $params = $this->object->getRoutingContextParams();
      
      $params['object_type'] = get_class($object);
      $params['object_id'] = $object->getId();
      
      return Router::assemble($this->object->getRoutingContext() . '_add_to_favorites', $params);
    } // getAddToFavoritesUrl
    
    /**
     * Return remove from favorites URL
     * 
     * @param ICanBeFavorite $object
     * @return string
     */
    function getRemoveFromFavoritesUrl(ICanBeFavorite $object) {
      $params = $this->object->getRoutingContextParams();
      
      $params['object_type'] = get_class($object);
      $params['object_id'] = $object->getId();
      
      return Router::assemble($this->object->getRoutingContext() . '_remove_from_favorites', $params);
    } // getRemoveFromFavoritesUrl
    
  }