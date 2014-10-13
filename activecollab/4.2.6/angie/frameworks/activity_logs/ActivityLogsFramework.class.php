<?php

  /**
   * Activity logs framework definition file
   *
   * @package angie.frameworks.activity_logs
   */
  class ActivityLogsFramework extends AngieFramework {
    
    /**
     * Short framework name
     *
     * @var string
     */
    protected $name = 'activity_logs';
    
    /**
     * Define routes
     */
    function defineRoutes() {
      Router::map('activity_logs_admin_rebuild', 'admin/indices/activity-logs/rebuild', array('controller' => 'activity_logs_admin', 'action' => 'rebuild', 'module' => ACTIVITY_LOGS_FRAMEWORK_INJECT_INTO));
      Router::map('activity_logs_admin_clean', 'admin/indices/activity-logs/clean', array('controller' => 'activity_logs_admin', 'action' => 'clean', 'module' => ACTIVITY_LOGS_FRAMEWORK_INJECT_INTO));

      $this->defineActivityLogsRoutesFor('backend', '', 'backend', SYSTEM_MODULE);
    } // defineRoutes

    /**
     * Define activity logs routes for given context
     *
     * @param string $context
     * @param string $context_path
     * @param array $context_defaults
     * @param array $context_requirements
     */
    function defineActivityLogsRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
      Router::map("{$context}_activity_log", "$context_path/activity-log", array('controller' => $controller_name, 'action' => "{$context}_activity_log", 'module' => $module_name), $context_requirements);
      Router::map("{$context}_activity_log_rss", "$context_path/activity-log/rss", array('controller' => $controller_name, 'action' => "{$context}_activity_log_rss", 'module' => $module_name), $context_requirements);
    } // defineActivityLogsRoutesFor
    
    /**
     * Define event handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_all_indices', 'on_all_indices');
      EventsManager::listen('on_rebuild_all_indices', 'on_rebuild_all_indices');
      EventsManager::listen('on_homescreen_widget_types', 'on_homescreen_widget_types');
      EventsManager::listen('on_object_context_changed', 'on_object_context_changed');
    } // defineHandlers
    
  }