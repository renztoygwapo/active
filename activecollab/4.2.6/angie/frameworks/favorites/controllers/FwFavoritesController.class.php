<?php

  // Build on top of users controller
  AngieApplication::useController('users', AUTHENTICATION_FRAMEWORK_INJECT_INTO);

  /**
   * Favorites controller
   * 
   * @package angie.frameworks.favorites
   * @subpackage controllers
   */
  abstract class FwFavoritesController extends UsersController {
    
    /**
     * Selected object, that we'll add to or remove from favorites
     *
     * @var ApplicationObject
     */
    protected $active_object;
    
    /**
     * Execute before any other action
     */
    function __before() {
      parent::__before();
      
      if($this->active_user->isNew()) {
        $this->response->notFound();
      } // if
      
      if($this->request->isAsyncCall() || ($this->request->isApiCall()) && $this->request->isSubmitted() || true) {
        $object_type = $this->request->get('object_type');
        $object_id = $this->request->getId('object_id');
        
        if($object_id && class_exists($object_type, true)) {
          $this->active_object = new $object_type($object_id);
        } // if        
      } else {
        $this->response->badRequest();
      } // if
    } // __before
    
    /**
     * List favorite objects
     */
    function index() {
      if($this->request->isApiCall()) {
        $this->response->respondWithData(Favorites::findFavoriteObjectsByUser($this->active_user), array(
          'as' => 'favorite_objects',
        ));
      } else {
        $this->smarty->assign(array(
          'favorites' => Favorites::findFavoriteObjectsByUser($this->active_user)
        ));
      } // if
    } // index
  
    /**
     * Add selected object to favorites
     */
    function add_to_favorites() {
      if(!($this->active_object instanceof ICanBeFavorite) || !$this->active_object->isLoaded() || !$this->active_object->canView($this->logged_user)) {
        $this->response->notFound();
      } // if
    	
      try {
        $this->active_user->favorites()->add($this->active_object);
        $this->response->respondWithData($this->active_object, array(
	        'as' => $this->active_object->getBaseTypeName(),
					'detailed' => true, 
        ));
      } catch(Exception $e) {
        $this->response->exception($e);
      } // try
    } // add_to_favorites
    
    /**
     * Remove selected objects from favorites
     */
    function remove_from_favorites() {
      if(!($this->active_object instanceof ICanBeFavorite) || !$this->active_object->isLoaded() || !$this->active_object->canView($this->logged_user)) {
        $this->response->notFound();
      } // if
    	
      try {
        $this->active_user->favorites()->remove($this->active_object);
        $this->response->respondWithData($this->active_object, array(
	        'as' => $this->active_object->getBaseTypeName(),
					'detailed' => true, 
        ));
      } catch(Exception $e) {
        $this->response->exception($e);
      } // try
    } // remove_from_favorites
    
  }