<?php

  /**
   * Project exporter module definition
   *
   * @package activeCollab.modules.project_exporter
   * @subpackage models
   */
  class ProjectExporterModule extends AngieModule {
    
    /**
     * Plain module name
     *
     * @var string
     */
    protected $name = 'project_exporter';
    
    /**
     * Module version
     *
     * @var string
     */
    protected $version = '4.0';
    
    // ---------------------------------------------------
    //  Events and Routes
    // ---------------------------------------------------
    
    /**
     * Define module routes
     */
    function defineRoutes() {
      Router::map('project_exporter', 'projects/:project_slug/project_exporter', array('controller' => 'project_exporter', 'action' => 'index'));
      Router::map('project_exporter_section_exporter', 'projects/:project_slug/project_exporter/export/:exporter_id', array('controller' => 'project_exporter', 'action' => 'export'));
      
      Router::map('project_exporter_finalize_export', 'projects/:project_slug/project_exporter/finalize', array('controller' => 'project_exporter', 'action' => 'finalize'));
      Router::map('project_exporter_download_export', 'projects/:project_slug/project_exporter/download', array('controller' => 'project_exporter', 'action' => 'download'));
    } // defineRoutes
    
    /**
     * Define event handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_object_options', 'on_object_options');
    } // defineHandlers
    
    // ---------------------------------------------------
    //  Names
    // ---------------------------------------------------
    
    /**
     * Get module display name
     *
     * @return string
     */
    function getDisplayName() {
      return lang('Project Exporter');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @return string
     */
    function getDescription() {
      return lang('Export project as a static website');
    } // getDescription
    
    /**
     * Return module uninstallation message
     *
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated. Files created with this module will not be deleted');
    } // getUninstallMessage
    
  }