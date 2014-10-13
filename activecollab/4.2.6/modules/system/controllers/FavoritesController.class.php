<?php

  // Build on top of favorites controller
  AngieApplication::useController('fw_favorites', FAVORITES_FRAMEWORK);

  /**
   * Application level favorites controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class FavoritesController extends FwFavoritesController {

    /**
     * Add selected object to favorites
     */
    function add_to_favorites() {
      if(!($this->active_object instanceof ICanBeFavorite) || !$this->active_object->isLoaded() || !$this->active_object->canView($this->logged_user)) {
        $this->response->notFound();
      } // if

      try {
        $this->active_user->favorites()->add($this->active_object);
        if ($this->active_object instanceof Project) {
          clean_menu_projects_and_quick_add_cache($this->logged_user);
        } // if
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
        if ($this->active_object instanceof Project) {
          clean_menu_projects_and_quick_add_cache($this->logged_user);
        } // if
        $this->response->respondWithData($this->active_object, array(
          'as' => $this->active_object->getBaseTypeName(),
          'detailed' => true,
        ));
      } catch(Exception $e) {
        $this->response->exception($e);
      } // try
    } // remove_from_favorites
  
  }