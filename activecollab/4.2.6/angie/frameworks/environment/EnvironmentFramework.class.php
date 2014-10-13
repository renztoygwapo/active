<?php

  /**
   * Environment framework definition
   *
   * @package angie.frameworks.environment
   */
  class EnvironmentFramework extends AngieFramework {
    
    /**
     * Framework name
     *
     * @var string
     */
    protected $name = 'environment';
    
    /**
     * Define environment framework updates
     */
    function defineRoutes() {
      Router::map('homepage', '', array('controller' => DEFAULT_CONTROLLER, 'action' => DEFAULT_ACTION, 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      Router::map('admin', 'admin', array('controller' => 'admin', 'action' => 'index', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      Router::map('public', 'public', array('controller' => 'public', 'action' => 'index', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));

      Router::map('wireframe_updates', 'wireframe-updates', array('controller' => 'backend', 'action' => 'wireframe_updates', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));

      Router::map('menu_refresh_url', 'refresh-menu', array('controller' => 'backend', 'action' => 'refresh_menu', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      Router::map('quick_add', 'quick-add', array('controller' => 'backend', 'action' => 'quick_add', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      
      // API
      Router::map('info', 'info', array('controller' => 'api', 'action' => 'info', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));

      // Disk Space
      Router::map('disk_space_admin', 'admin/disk-space', array('controller' => 'disk_space_admin', 'action' => 'index', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      Router::map('disk_space_usage', 'admin/disk-space/usage', array('controller' => 'disk_space_admin', 'action' => 'usage', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      Router::map('disk_space_admin_settings', 'admin/disk-space/settings', array('controller' => 'disk_space_admin', 'action' => 'settings', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      Router::map('disk_space_remove_application_cache', 'admin/disk-space/tools/remove-application-cache', array('controller' => 'disk_space_admin', 'action' => 'remove_application_cache', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      Router::map('disk_space_remove_logs', 'admin/disk-space/tools/remove-logs', array('controller' => 'disk_space_admin', 'action' => 'remove_logs', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      Router::map('disk_space_remove_old_application_versions', 'admin/disk-space/tools/remove-old-application-versions', array('controller' => 'disk_space_admin', 'action' => 'remove_old_application_versions', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      Router::map('disk_space_remove_orphan_files', 'admin/disk-space/tools/remove-orphan-files', array('controller' => 'disk_space_admin', 'action' => 'remove_orphan_files', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));

      // Appearance
      Router::map('appearance_admin', 'admin/appearance', array('controller' => 'appearance', 'action' => 'index', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      Router::map('appearance_admin_add_scheme', 'admin/appearance/add-scheme', array('controller' => 'appearance', 'action' => 'add', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      Router::map('appearance_admin_edit_scheme', 'admin/appearance/:scheme_id/edit', array('controller' => 'appearance', 'action' => 'edit', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      Router::map('appearance_admin_rename_scheme', 'admin/appearance/:scheme_id/rename', array('controller' => 'appearance', 'action' => 'rename', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      Router::map('appearance_admin_delete_scheme', 'admin/appearance/:scheme_id/delete', array('controller' => 'appearance', 'action' => 'delete', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      Router::map('appearance_admin_set_as_default_scheme', 'admin/appearance/:scheme_id/set-as-default', array('controller' => 'appearance', 'action' => 'set_as_default', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));

      // Scheduled Tasks Admin
      Router::map('scheduled_tasks_admin', 'admin/scheduled-tasks', array('controller' => 'scheduled_tasks_admin', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));

      // Network settings
      Router::map('network_settings', 'admin/network', array('controller' => 'network_admin', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      
      // Indices admin
      Router::map('indices_admin', 'admin/indices', array('controller' => 'indices_admin', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      Router::map('indices_admin_rebuild', 'admin/indices/rebuild', array('controller' => 'indices_admin', 'action' => 'rebuild', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      Router::map('indices_admin_rebuild_finish', 'admin/indices/rebuild/finish', array('controller' => 'indices_admin', 'action' => 'rebuild_finish', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      
      Router::map('object_contexts_admin_rebuild', 'admin/indices/object-contexts/rebuild', array('controller' => 'object_contexts_admin', 'action' => 'rebuild', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      Router::map('object_contexts_admin_clean', 'admin/indices/object-contexts/clean', array('controller' => 'object_contexts_admin', 'action' => 'clean', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      
      // Scheduled tasks
      Router::map('frequently', 'frequently', array('controller' => 'scheduled_tasks', 'action' => 'frequently', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      Router::map('hourly', 'hourly', array('controller' => 'scheduled_tasks', 'action' => 'hourly', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      Router::map('daily', 'daily', array('controller' => 'scheduled_tasks', 'action' => 'daily', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      
      // trash related
      Router::map('trash', 'trash', array('controller' => 'trash', 'action' => 'index', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      Router::map('trash_section', 'trash/:section_name', array('controller' => 'trash', 'action' => 'section', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      Router::map('trash_empty', 'trash/empty', array('controller' => 'trash', 'action' => 'empty_trash', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));

      Router::map('object_untrash', 'trash/untrash-object', array('controller' => 'trash', 'action' => 'untrash_object', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO), array('object_id' => Router::MATCH_ID));
      Router::map('object_delete', 'trash/delete-object', array('controller' => 'trash', 'action' => 'delete_object', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO), array('object_id' => Router::MATCH_ID));

      // Control Tower
      Router::map('control_tower', 'control-tower', array('controller' => 'control_tower', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      Router::map('control_tower_empty_cache', 'control-tower/empty-cache', array('controller' => 'control_tower', 'action' => 'empty_cache', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      Router::map('control_tower_delete_compiled_templates', 'control-tower/delete-compiled-templates', array('controller' => 'control_tower', 'action' => 'delete_compiled_templates', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      Router::map('control_tower_rebuild_images', 'control-tower/rebuild-images', array('controller' => 'control_tower', 'action' => 'rebuild_images', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      Router::map('control_tower_rebuild_localization', 'control-tower/rebuild-localization', array('controller' => 'control_tower', 'action' => 'rebuild_localization', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
      Router::map('control_tower_performance_checklist', 'control-tower/performance-checklist', array('controller' => 'control_tower', 'action' => 'performance_checklist', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));

      Router::map('control_tower_settings', 'admin/control-tower', array('controller' => 'control_tower', 'action' => 'settings', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));

	    // Firewall
	    Router::map('firewall', 'admin/firewall', array('controller' => 'firewall', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
    } // defineRoutes
    
    /**
     * Define state routes for given context
     *
     * @param string $context
     * @param string $context_path
     * @param string $controller_name
     * @param string $module_name
     * @param array $context_requirements
     */
    function defineStateRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
      Router::map("{$context}_archive", "$context_path/archive", array('controller' => $controller_name, 'action' => "{$context}_state_archive", 'module' => $module_name), $context_requirements);
      Router::map("{$context}_unarchive", "$context_path/unarchive", array('controller' => $controller_name, 'action' => "{$context}_state_unarchive", 'module' => $module_name), $context_requirements);
      Router::map("{$context}_trash", "$context_path/trash", array('controller' => $controller_name, 'action' => "{$context}_state_trash", 'module' => $module_name), $context_requirements);
      Router::map("{$context}_untrash", "$context_path/untrash", array('controller' => $controller_name, 'action' => "{$context}_state_untrash", 'module' => $module_name), $context_requirements);
      Router::map("{$context}_delete", "$context_path/delete", array('controller' => $controller_name, 'action' => "{$context}_state_delete", 'module' => $module_name), $context_requirements);
    } // defineStateRoutesFor
    
    /**
     * Define event handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_rawtext_to_richtext', 'on_rawtext_to_richtext');
      EventsManager::listen('on_daily', 'on_daily');
      EventsManager::listen('on_wireframe_updates', 'on_wireframe_updates');
    } // defineHandlers
    
  }