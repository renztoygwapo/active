<?php

  /**
   * Time and expense tracking module definition
   *
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  class TrackingModule extends AngieModule {
    
    /**
     * Plain module name
     *
     * @var string
     */
    protected $name = 'tracking';
    
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
      Router::map('time', 'time', array('controller' => 'time', 'action' => 'index'));
      
      // Tracking Reports
      Router::map('tracking_reports', 'reports/tracking', array('controller' => 'tracking_reports'));
      Router::map('tracking_reports_add', 'reports/tracking/add', array('controller' => 'tracking_reports', 'action' => 'add'));
      Router::map('tracking_reports_run', 'tracking/tracking/run', array('controller' => 'tracking_reports', 'action' => 'run'));
      Router::map('tracking_reports_export', 'tracking/tracking/export', array('controller' => 'tracking_reports', 'action' => 'export'));
      
      Router::map('tracking_report', 'reports/tracking/:tracking_report_id', array('controller' => 'tracking_reports', 'action' => 'view'), array('tracking_report_id' => '\d+'));
      Router::map('tracking_report_edit', 'reports/tracking/:tracking_report_id/edit', array('controller' => 'tracking_reports', 'action' => 'edit'), array('tracking_report_id' => '\d+'));
      Router::map('tracking_report_delete', 'reports/tracking/:tracking_report_id/delete', array('controller' => 'tracking_reports', 'action' => 'delete'), array('tracking_report_id' => '\d+'));

      Router::map('tracking_reports_change_status', 'reports/tracking/change/status', array('controller' => 'tracking_reports', 'action' => 'change_records_status'));


      Router::map('estiamted_vs_tracked_time_report', 'reports/estimated-vs-tracked-time', array('controller' => 'estimated_vs_tracked_time', 'action' => 'estimated_vs_tracked_time'));
      Router::map('estiamted_vs_tracked_time_report_run', 'reports/estimated-vs-tracked-time-run', array('controller' => 'estimated_vs_tracked_time', 'action' => 'estimated_vs_tracked_time_run'));

      Router::map('budget_vs_cost_report', 'reports/budget-vs-cost', array('controller' => 'budget_vs_cost', 'action' => 'budget_vs_cost'));
      
      // Section
      Router::map('project_tracking', 'projects/:project_slug/tracking', array('controller' => 'project_tracking', 'action' => 'log'));
      Router::map('project_tracking_mass_update', 'projects/:project_slug/tracking/mass-update', array('controller' => 'project_tracking', 'action' => 'log_mass_update'));
      Router::map('project_tracking_get_totals', 'projects/:project_slug/tracking/get-totals', array('controller' => 'project_tracking', 'action' => 'log_get_totals'));
      Router::map('project_tracking_timesheet', 'projects/:project_slug/tracking/timesheet', array('controller' => 'project_tracking', 'action' => 'timesheet'));
      Router::map('project_tracking_timesheet_day', 'projects/:project_slug/tracking/timesheet/day-details', array('controller' => 'project_tracking', 'action' => 'timesheet_day'));

      // My time homescreen widget
      Router::map('my_time_homescreen_widget_weekly_time', 'homescreen/widgets/:widget_id/weekly-time', array('controller' => 'my_time_homescreen_widget', 'action' => 'weekly_time'), array('widget_id' => Router::MATCH_ID));
      Router::map('my_time_homescreen_widget_add_time', 'homescreen/widgets/:widget_id/add-time', array('controller' => 'my_time_homescreen_widget', 'action' => 'add_time'), array('widget_id' => Router::MATCH_ID));
      Router::map('my_time_homescreen_widget_refresh', 'homescreen/widgets/:widget_id/refresh', array('controller' => 'my_time_homescreen_widget', 'action' => 'refresh'), array('widget_id' => Router::MATCH_ID));

      // API
      Router::map('job_types_info', 'info/job-types', array('controller' => 'tracking_api', 'action' => 'job_types'));
      Router::map('expense_categories_info', 'info/expense-categories', array('controller' => 'tracking_api', 'action' => 'expense_categories'));

      // Admin
      Router::map('job_types_admin', 'admin/job-types', array('controller' => 'job_types_admin'));
      Router::map('job_types_add', 'admin/job-types/add', array('controller' => 'job_types_admin', 'action' => 'add'));
      
      Router::map('job_type', 'admin/job-types/:job_type_id', array('controller' => 'job_types_admin', 'action' => 'view'), array('job_type_id' => Router::MATCH_ID));
      Router::map('job_type_edit', 'admin/job-types/:job_type_id/edit', array('controller' => 'job_types_admin', 'action' => 'edit'), array('job_type_id' => Router::MATCH_ID));
      Router::map('job_type_set_as_default', 'admin/job-types/:job_type_id/set-as-default', array('controller' => 'job_types_admin', 'action' => 'set_as_default'), array('job_type_id' => Router::MATCH_ID));
      Router::map('job_type_archive', 'admin/job-types/:job_type_id/archive', array('controller' => 'job_types_admin', 'action' => 'archive'), array('job_type_id' => Router::MATCH_ID));
      Router::map('job_type_unarchive', 'admin/job-types/:job_type_id/unarchive', array('controller' => 'job_types_admin', 'action' => 'unarchive'), array('job_type_id' => Router::MATCH_ID));
      Router::map('job_type_delete', 'admin/job-types/:job_type_id/delete', array('controller' => 'job_types_admin', 'action' => 'delete'), array('job_type_id' => Router::MATCH_ID));
      
      Router::map('expense_categories_admin', 'admin/expense-categories', array('controller' => 'expense_categories_admin'));
      Router::map('expense_categories_add', 'admin/expense-categories/add', array('controller' => 'expense_categories_admin', 'action' => 'add'));
      
      Router::map('expense_category', 'admin/expense-categories/:expense_category_id', array('controller' => 'expense_categories_admin', 'action' => 'view'), array('expense_category_id' => Router::MATCH_ID));
      Router::map('expense_category_edit', 'admin/expense-categories/:expense_category_id/edit', array('controller' => 'expense_categories_admin', 'action' => 'edit'), array('expense_category_id' => Router::MATCH_ID));
      Router::map('expense_category_set_as_default', 'admin/expense-categories/:expense_category_id/set-as-default', array('controller' => 'expense_categories_admin', 'action' => 'set_as_default'), array('expense_category_id' => Router::MATCH_ID));
      Router::map('expense_category_delete', 'admin/expense-categories/:expense_category_id/delete', array('controller' => 'expense_categories_admin', 'action' => 'delete'), array('expense_category_id' => Router::MATCH_ID));
      
      // Project
      Router::map('project_hourly_rates', 'projects/:project_slug/hourly-rates', array('controller' => 'project_hourly_rates'));
      Router::map('project_hourly_rate', 'projects/:project_slug/hourly-rates/:job_type_id/edit', array('controller' => 'project_hourly_rates', 'action' => 'edit'), array('job_type_id' => Router::MATCH_ID));
      
      Router::map('project_budget', 'projects/:project_slug/budget', array('controller' => 'project_budget'));
      
       // Invoicing
      if(AngieApplication::isModuleLoaded('invoicing')) {
        AngieApplication::getModule('invoicing')->defineInvoiceRoutesFor('tracking_report', 'tracking-report', 'tracking_reports', TRACKING_MODULE, array('tracking_report_id' => Router::MATCH_ID)); 
      } // if
      
      Router::map('activity_logs_admin_rebuild_tracking', 'admin/indices/activity-logs/rebuild/tracking', array('controller' => 'activity_logs_admin', 'action' => 'rebuild_tracking'));
      Router::map('object_contexts_admin_rebuild_tracking', 'admin/indices/object-contexts/rebuild/tracking', array('controller' => 'object_contexts_admin', 'action' => 'rebuild_tracking'));
    } // defineRoutes
    
    /**
     * Define tracking routes for given context
     *
     * @param string $context
     * @param string $context_path
     * @param string $controller_name
     * @param string $module_name
     * @param array $context_requirements
     */
    function defineTrackingRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
      $time_record_requirements = is_array($context_requirements) ? array_merge($context_requirements, array('time_record_id' => '\d+')) : array('time_record_id' => '\d+');
      $expense_requirements = is_array($context_requirements) ? array_merge($context_requirements, array('expense_id' => '\d+')) : array('expense_id' => '\d+');
      
      Router::map("{$context}_tracking", "$context_path/tracking", array('controller' => $controller_name, 'action' => "{$context}_object_tracking_list", 'module' => $module_name), $context_requirements);
      
      // Estimate
      Router::map("{$context}_tracking_estimates", "$context_path/tracking/estimates", array('controller' => $controller_name, 'action' => "{$context}_object_tracking_estimates", 'module' => $module_name), $context_requirements);
      Router::map("{$context}_tracking_estimate_set", "$context_path/tracking/estimates/set", array('controller' => $controller_name, 'action' => "{$context}_object_tracking_estimate_set", 'module' => $module_name), $context_requirements);
      
      // Time records
      Router::map("{$context}_tracking_time_records_add", "$context_path/tracking/time/add", array('controller' => $controller_name, 'action' => "{$context}_add_time_record", 'module' => $module_name), $context_requirements);
      
      Router::map("{$context}_tracking_time_record", "$context_path/tracking/time/:time_record_id", array('controller' => $controller_name, 'action' => "{$context}_view_time_record", 'module' => $module_name), $time_record_requirements);
      Router::map("{$context}_tracking_time_record_edit", "$context_path/tracking/time/:time_record_id/edit", array('controller' => $controller_name, 'action' => "{$context}_edit_time_record", 'module' => $module_name), $time_record_requirements);
      
      AngieApplication::getModule('environment')->defineStateRoutesFor("{$context}_tracking_time_record", "$context_path/tracking/time/:time_record_id", $controller_name, $module_name, $time_record_requirements);

      if (AngieApplication::isModuleLoaded('footprints')) {
        AngieApplication::getModule('footprints')->defineAccessLogRoutesFor("{$context}_tracking_time_record", "$context_path/tracking/time/:time_record_id", $controller_name, $module_name, $time_record_requirements);
        AngieApplication::getModule('footprints')->defineHistoryOfChangesRoutesFor("{$context}_tracking_time_record", "$context_path/tracking/time/:time_record_id", $controller_name, $module_name, $time_record_requirements);
      } // if

      // Expenses
      Router::map("{$context}_tracking_expenses_add", "$context_path/tracking/expenses/add", array('controller' => $controller_name, 'action' => "{$context}_add_expense", 'module' => $module_name), $context_requirements);
      
      Router::map("{$context}_tracking_expense", "$context_path/tracking/expenses/:expense_id", array('controller' => $controller_name, 'action' => "{$context}_view_expense", 'module' => $module_name), $expense_requirements);
      Router::map("{$context}_tracking_expense_edit", "$context_path/tracking/expenses/:expense_id/edit", array('controller' => $controller_name, 'action' => "{$context}_edit_expense", 'module' => $module_name), $expense_requirements);
      
      AngieApplication::getModule('environment')->defineStateRoutesFor("{$context}_tracking_expense", "$context_path/tracking/expenses/:expense_id", $controller_name, $module_name, $expense_requirements);

      if (AngieApplication::isModuleLoaded('footprints')) {
        AngieApplication::getModule('footprints')->defineAccessLogRoutesFor("{$context}_tracking_expense", "$context_path/tracking/expenses/:expense_id", $controller_name, $module_name, $expense_requirements);
        AngieApplication::getModule('footprints')->defineHistoryOfChangesRoutesFor("{$context}_tracking_expense", "$context_path/tracking/expenses/:expense_id", $controller_name, $module_name, $expense_requirements);
      } // if
    } // defineTrackingRoutesFor
    
    /**
     * Define event handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_project_tabs', 'on_project_tabs');
      EventsManager::listen('on_available_project_tabs', 'on_available_project_tabs');
      EventsManager::listen('on_admin_panel', 'on_admin_panel');
      EventsManager::listen('on_project_export', 'on_project_export');
      EventsManager::listen('on_user_cleanup', 'on_user_cleanup');
      EventsManager::listen('on_project_permissions', 'on_project_permissions');
      EventsManager::listen('on_object_options', 'on_object_options');
      EventsManager::listen('on_reports_panel', 'on_reports_panel');
      EventsManager::listen('on_quick_add', 'on_quick_add');
      EventsManager::listen('on_project_subcontext_permission', 'on_project_subcontext_permission');
      EventsManager::listen('on_rebuild_activity_log_actions', 'on_rebuild_activity_log_actions');
      EventsManager::listen('on_activity_log_callbacks', 'on_activity_log_callbacks');
      EventsManager::listen('on_rebuild_object_contexts_actions', 'on_rebuild_object_contexts_actions');
      EventsManager::listen('on_trash_sections', 'on_trash_sections');
      EventsManager::listen('on_trash_map', 'on_trash_map');
      EventsManager::listen('on_empty_trash', 'on_empty_trash');
      EventsManager::listen('on_homescreen_widget_types', 'on_homescreen_widget_types');
      EventsManager::listen('on_extra_stats', 'on_extra_stats');
    } // defineHandlers

    /**
     * Uninstall module
     */
    function uninstall() {
      try {
        DB::beginWork('Uninstalling tracking module @ ' . __CLASS__);

        parent::uninstall();
        ActivityLogs::deleteByParentTypes(array('Expense', 'TimeRecord', 'Estimate'));
        FwApplicationObjects::cleanUpContextsByParentTypes(array('TimeRecord', 'Expense', 'Estimate'));

        DB::commit('Tracking module uninstalled @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to uninstall tracking module @ ' . __CLASS__);
        throw $e;
      } // try
    } // uninstall
    
    // ---------------------------------------------------
    //  Enable / Disable
    // ---------------------------------------------------
    
    /**
     * This module can't be disabled
     *
     * @param User $user
     * @return boolean
     */
    function canDisable(User $user) {
      return false;
    } // canDisable
    
    // ---------------------------------------------------
    //  Name
    // ---------------------------------------------------
    
    /**
     * Get module display name
     *
     * @return string
     */
    function getDisplayName() {
      return lang('Time and Expense Tracking');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @return string
     */
    function getDescription() {
      return lang('Adds time and expense tracking support to projects');
    } // getDescription
    
    /**
     * Return module uninstallation message
     *
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated. All time and expense records, as well as defined reports will be deleted');
    } // getUninstallMessage

    /**
     * Return object types (class names) that this module is working with
     *
     * @return array
     */
    function getObjectTypes() {
      return array('Estimate', 'TimeRecord', 'Expense');
    } // getObjectTypes
    
  }