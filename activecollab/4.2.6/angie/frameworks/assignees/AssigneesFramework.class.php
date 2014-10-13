<?php

  /**
   * Assignees framework definition file
   * 
   * @package angie.frameworks.assignees
   */
  class AssigneesFramework extends AngieFramework {
    
    /**
     * Short framework name
     *
     * @var string
     */
    protected $name = 'assignees';
    
    /**
     * Define framework routes
     */
    function defineRoutes() {
      Router::map('assignment_labels', 'info/labels/assignment', array('controller' => 'assignees_api', 'action' => 'labels', 'module' => ASSIGNEES_FRAMEWORK_INJECT_INTO));
      LabelsFramework::defineLabelsAdminRoutesFor('assignments_admin', 'admin/assignments', 'assignment_labels_admin', ASSIGNEES_FRAMEWORK_INJECT_INTO);
    } // defineRoutes
    
  	/**
     * Define assignment routes for given context
     *
     * @param string $context
     * @param string $context_path
     * @param array $context_defaults
     * @param array $context_requirements
     */
    function defineAssigneesRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {      
      Router::map("{$context}_assignees", "$context_path/assignees", array('controller' => $controller_name, 'action' => "{$context}_assignees", 'module' => $module_name));
    } // defineAssigneesRoutesFor
    
    /**
     * Define handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_admin_panel', 'on_admin_panel');
    } // defineHandlers
    
  }