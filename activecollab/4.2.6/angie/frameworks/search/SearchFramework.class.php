<?php

  /**
   * Search and tagging framework
   *
   * @package angie.frameworks.Search
   */
  class SearchFramework extends AngieFramework {
    
    /**
     * Short framework name
     *
     * @var string
     */
    protected $name = 'search';
    
    /**
     * Define routes
     */
    function defineRoutes() {
      Router::map('backend_search', SEARCH_FRAMEWORK_BACKEND_SEARCH_BASE . '/search', array('controller' => 'backend_search', 'module' => SEARCH_FRAMEWORK_INJECT_INTO));
      Router::map('search_settings', SEARCH_FRAMEWORK_ADMIN_ROUTE_BASE . '/search-settings', array('controller' => 'search_settings', 'module' => SEARCH_FRAMEWORK_INJECT_INTO));

      Router::map('search_index_admin_rebuild', 'admin/indices/search/:search_index_name/rebuild', array('controller' => 'search_index_admin', 'action' => 'rebuild', 'module' => SEARCH_FRAMEWORK_INJECT_INTO));
      Router::map('search_index_admin_reinit', 'admin/indices/search/:search_index_name/reinit', array('controller' => 'search_index_admin', 'action' => 'reinit', 'module' => SEARCH_FRAMEWORK_INJECT_INTO));
    } // defineRoutes
    
    /**
     * Define event handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_all_indices', 'on_all_indices');
      EventsManager::listen('on_rebuild_all_indices', 'on_rebuild_all_indices');
      EventsManager::listen('on_object_context_changed', 'on_object_context_changed');
      EventsManager::listen('on_admin_panel', 'on_admin_panel');
    } // defineHandlers
    
  }