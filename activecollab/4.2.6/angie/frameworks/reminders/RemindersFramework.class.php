<?php

  /**
   * Reminders framework
   *
   * @package angie.frameworks.reminders
   */
  class RemindersFramework extends AngieFramework {
  
  	/**
  	 * Short framework name
  	 * 
  	 * @var string
  	 */
  	protected $name = 'reminders';
  	
  	/**
  	 * Define reminder module routes
  	 */
  	function defineRoutes() {
  		Router::map('reminders', 'reminders', array('controller' => 'reminders', 'action' => 'index', 'module' => REMINDERS_FRAMEWORK_INJECT_INTO));
  	} // defineRoutes
  	
  	/**
     * Define reminder routes for given context
     *
     * @param string $context
     * @param string $context_path
     * @param string $controller_name
     * @param string $module_name
     * @param array $context_requirements
     */
    function defineRemindersRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
      $reminder_requirements = is_array($context_requirements) ? array_merge($context_requirements, array('reminder_id' => '\d+')) : array('reminder_id' => '\d+');
      
      Router::map("{$context}_reminders", "$context_path/reminders", array('controller' => $controller_name, 'action' => "{$context}_reminders", 'module' => $module_name), $context_requirements);
      Router::map("{$context}_reminders_add", "$context_path/reminders/add", array('controller' => $controller_name, 'action' => "{$context}_add_reminder", 'module' => $module_name), $context_requirements);
      Router::map("{$context}_reminders_nudge", "$context_path/reminders/nudge", array('controller' => $controller_name, 'action' => "{$context}_nudge_reminder", 'module' => $module_name), $context_requirements);
      
      Router::map("{$context}_reminder", "$context_path/reminders/:reminder_id", array('controller' => $controller_name, 'action' => "{$context}_view_reminder", 'module' => $module_name), $reminder_requirements);
      Router::map("{$context}_reminder_edit", "$context_path/reminders/:reminder_id/edit", array('controller' => $controller_name, 'action' => "{$context}_edit_reminder", 'module' => $module_name), $reminder_requirements);
      Router::map("{$context}_reminder_send", "$context_path/reminders/:reminder_id/send", array('controller' => $controller_name, 'action' => "{$context}_send_reminder", 'module' => $module_name), $reminder_requirements);
      Router::map("{$context}_reminder_dismiss", "$context_path/reminders/:reminder_id/dismiss", array('controller' => $controller_name, 'action' => "{$context}_dismiss_reminder", 'module' => $module_name), $reminder_requirements); 
      Router::map("{$context}_reminder_delete", "$context_path/reminders/:reminder_id/delete", array('controller' => $controller_name, 'action' => "{$context}_delete_reminder", 'module' => $module_name), $reminder_requirements); 
    } // defineRemindersRoutesFor
    
    /**
     * Define reminder routes for given context
     *
     * @param string $context
     * @param string $context_path
     * @param string $controller_name
     * @param string $module_name
     * @param array $context_requirements
     */
    function defineUserRemindersRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
      $context_requirements = is_array($context_requirements) ? array_merge($context_requirements, array('reminder_id' => '\d+')) : array('reminder_id' => '\d+');
      
      Router::map("{$context}_reminders", "$context_path/reminders", array('controller' => $controller_name, 'action' => "{$context}_user_reminders", 'module' => $module_name), $context_requirements);
    } // defineUserRemindersRoutesFor
    
    /**
     * Define event handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_frequently', 'on_frequently');
      EventsManager::listen('on_homescreen_widget_types', 'on_homescreen_widget_types');
    } // defineHandlers
  	
  }