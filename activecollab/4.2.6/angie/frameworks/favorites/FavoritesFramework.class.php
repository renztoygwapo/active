<?php

  /**
   * Favorites framework definition
   * 
   * @package angie.frameworks.favorites
   */
  class FavoritesFramework extends AngieFramework {
    
    /**
     * Framework name
     *
     * @var string
     */
    protected $name = 'favorites';
    
    /**
     * Define framework routes
     */
    function defineRoutes() {
      if (FAVORITES_FRAMEWORK_DEFINE_ROUTES) {
      	Router::map('user_favorites', '/users/:user_id/favorites', array('controller' => 'favorites', 'module' => FAVORITES_FRAMEWORK_INJECT_INTO), array('user_id' => Router::MATCH_ID));
        Router::map('user_add_to_favorites', 'users/:user_id/favorites/add', array('controller' => 'favorites', 'action' => 'add_to_favorites', 'module' => FAVORITES_FRAMEWORK_INJECT_INTO), array('user_id' => Router::MATCH_ID));
        Router::map('user_remove_from_favorites', 'users/:user_id/favorites/remove', array('controller' => 'favorites', 'action' => 'remove_from_favorites', 'module' => FAVORITES_FRAMEWORK_INJECT_INTO), array('user_id' => Router::MATCH_ID));
      } // if
    } // defineRoutes
  
  }