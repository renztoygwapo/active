<?php

  /**
   * Data Sources framework definition
   *
   * @package angie.frameworks.data_sources
   * @subpackage models
   */
  class DataSourcesFramework extends AngieFramework {
    
    /**
     * Plain module name
     *
     * @var string
     */
    protected $name = 'data_sources';
    
    /**
     * Module version
     *
     * @var string
     */
    protected $version = '1.0';
    
    // ---------------------------------------------------
    //  Events and Routes
    // ---------------------------------------------------
    
    /**
     * Define module routes
     */
    function defineRoutes() {
      Router::map('data_sources', 'admin/data-sources', array('controller' => 'data_sources_admin'));
      Router::map('data_source_add', 'admin/data-sources/add', array('controller' => 'data_sources_admin', 'action' => 'add'));
      Router::map('data_source_edit', 'admin/data-sources/:data_source_id/edit', array('controller' => 'data_sources_admin', 'action' => 'edit'), array('data_source_id' => Router::MATCH_ID));
      Router::map('data_source', 'admin/data-sources/:data_source_id/view', array('controller' => 'data_sources_admin', 'action' => 'view'), array('data_source_id' => Router::MATCH_ID));
      Router::map('data_source_delete', 'admin/data-sources/:data_source_id/delete', array('controller' => 'data_sources_admin', 'action' => 'delete'), array('data_source_id' => Router::MATCH_ID));
      Router::map('data_source_test_connection', 'admin/data-sources/test-connection', array('controller' => 'data_sources_admin', 'action' => 'test_connection'));

      Router::map('data_source_import', 'data-sources/:data_source_id/import', array('controller' => 'data_sources', 'action' => 'import'), array('data_source_id' => Router::MATCH_ID));
      Router::map('data_source_validate_before_import', 'data-sources/:data_source_id/validate', array('controller' => 'data_sources', 'action' => 'validate_import'), array('data_source_id' => Router::MATCH_ID));

    } // defineRoutes
    
    /**
     * Define event handlers
     */
    function defineHandlers() {
      if(AngieApplication::isOnDemand()) {
        EventsManager::listen('on_admin_panel', 'on_admin_panel');
        EventsManager::listen('on_new_data_source', 'on_new_data_source');
        EventsManager::listen('on_project_deleted', 'on_project_deleted');
      } //if
    } // defineHandlers

  }