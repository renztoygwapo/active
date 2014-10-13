<?php

  // Include application specific module base
  require_once APPLICATION_PATH . '/resources/ActiveCollabProjectSectionModule.class.php';

  /**
   * Tasks module definition
   *
   * @package activeCollab.modules.tasks
   * @subpackage models
   */
  class TasksModule extends ActiveCollabProjectSectionModule {

    /**
     * Plain module name
     *
     * @var string
     */
    protected $name = 'tasks';

    /**
     * Module version
     *
     * @var string
     */
    protected $version = '4.0';

    /**
     * Name of the project object class (or classes) that this module uses
     *
     * @var string
     */
    protected $project_object_classes = 'Task';

    /**
     * Name of category class used by this section
     *
     * @var string
     */
    protected $category_class = 'TaskCategory';

    // ---------------------------------------------------
    //  Events and Routes
    // ---------------------------------------------------

    /**
     * Define module routes
     */
    function defineRoutes() {

      // My tasks extension
      Router::map('my_tasks_refresh', 'my-tasks/refresh', array('controller' => 'my_tasks', 'action' => 'refresh'));
      Router::map('my_tasks_completed', 'my-tasks/completed', array('controller' => 'my_tasks', 'action' => 'completed'));
      Router::map('my_tasks_unassigned', 'my-tasks/unassigned', array('controller' => 'my_tasks', 'action' => 'unassigned'));
      Router::map('my_tasks_settings', 'my-tasks/settings', array('controller' => 'my_tasks', 'action' => 'settings'));

      // Project tasks
      Router::map('project_tasks', 'projects/:project_slug/tasks', array('controller' => 'tasks', 'action' => 'index'));
      Router::map('project_tasks_archive', 'projects/:project_slug/tasks/archive', array('controller' => 'tasks', 'action' => 'archive'));
      Router::map('project_tasks_mass_edit', 'projects/:project_slug/tasks/mass-edit', array('controller' => 'tasks', 'action' => 'mass_edit'));
      Router::map('project_tasks_reorder', 'projects/:project_slug/tasks/reorder', array('controller' => 'tasks', 'action' => 'reorder'));
      Router::map('project_tasks_clean_up', 'projects/:project_slug/tasks/clean-up', array('controller' => 'tasks', 'action' => 'clean_up'));

      Router::map('project_tasks_add', 'projects/:project_slug/tasks/add', array('controller' => 'tasks', 'action' => 'add'));

      // Single task
      Router::map('project_task', 'projects/:project_slug/tasks/:task_id', array('controller' => 'tasks', 'action' => 'view'), array('task_id' => Router::MATCH_ID));
      Router::map('project_task_edit', 'projects/:project_slug/tasks/:task_id/edit', array('controller' => 'tasks', 'action' => 'edit'), array('task_id' => Router::MATCH_ID));

      AngieApplication::getModule('categories')->defineCategoriesRoutesFor('project_task', 'projects/:project_slug/tasks', 'tasks', TASKS_MODULE, array('task_id' => Router::MATCH_ID));
      AngieApplication::getModule('categories')->defineCategoryRoutesFor('project_task', 'projects/:project_slug/tasks', 'tasks', TASKS_MODULE, array('task_id' => Router::MATCH_ID));
      AngieApplication::getModule('environment')->defineStateRoutesFor('project_task', 'projects/:project_slug/tasks/:task_id', 'tasks', TASKS_MODULE, array('task_id' => Router::MATCH_ID));
      AngieApplication::getModule('complete')->defineChangeStatusRoutesFor('project_task', 'projects/:project_slug/tasks/:task_id', 'tasks', TASKS_MODULE, array('task_id' => Router::MATCH_ID));
      AngieApplication::getModule('complete')->definePriorityRoutesFor('project_task', 'projects/:project_slug/tasks/:task_id', 'tasks', TASKS_MODULE, array('task_id' => Router::MATCH_ID));
      AngieApplication::getModule('subtasks')->defineSubtasksRoutesFor('project_task', 'projects/:project_slug/tasks/:task_id', 'tasks', TASKS_MODULE, array('task_id' => Router::MATCH_ID));
      AngieApplication::getModule('comments')->defineCommentsRoutesFor('project_task', 'projects/:project_slug/tasks/:task_id', 'tasks', TASKS_MODULE, array('task_id' => Router::MATCH_ID));
      AngieApplication::getModule('subscriptions')->defineSubscriptionRoutesFor('project_task', 'projects/:project_slug/tasks/:task_id', 'tasks', TASKS_MODULE, array('task_id' => Router::MATCH_ID));
      AngieApplication::getModule('attachments')->defineAttachmentsRoutesFor('project_task', 'projects/:project_slug/tasks/:task_id', 'tasks', TASKS_MODULE, array('task_id' => Router::MATCH_ID));
      AngieApplication::getModule('reminders')->defineRemindersRoutesFor('project_task', 'projects/:project_slug/tasks/:task_id', 'tasks', TASKS_MODULE, array('task_id' => Router::MATCH_ID));
      AngieApplication::getModule('system')->defineSharingRoutesFor('project_task', 'projects/:project_slug/tasks/:task_id', 'tasks', TASKS_MODULE, array('task_id' => Router::MATCH_ID));
      AngieApplication::getModule('schedule')->defineScheduleRoutesFor('project_task', 'projects/:project_slug/tasks/:task_id', 'tasks', TASKS_MODULE, array('task_id' => Router::MATCH_ID));
      AngieApplication::getModule('system')->defineMoveToProjectRoutesFor('project_task', 'projects/:project_slug/tasks/:task_id', 'tasks', TASKS_MODULE, array('task_id' => Router::MATCH_ID));
      AngieApplication::getModule('assignees')->defineAssigneesRoutesFor('project_task', 'projects/:project_slug/tasks/:task_id', 'tasks', TASKS_MODULE, array('task_id' => Router::MATCH_ID));
      AngieApplication::getModule('labels')->defineLabelsRoutesFor('project_task', 'projects/:project_slug/tasks/:task_id', 'tasks', TASKS_MODULE, array('task_id' => Router::MATCH_ID));

      // Project task footprints
      if (AngieApplication::isModuleLoaded('footprints')) {
        AngieApplication::getModule('footprints')->defineAccessLogRoutesFor('project_task', 'projects/:project_slug/tasks/:task_id', 'tasks', TASKS_MODULE, array('task_id' => Router::MATCH_ID));
        AngieApplication::getModule('footprints')->defineHistoryOfChangesRoutesFor('project_task', 'projects/:project_slug/tasks/:task_id', 'tasks', TASKS_MODULE, array('task_id' => Router::MATCH_ID));
      } // if

      // Related tasks
      Router::map('project_task_related_tasks', 'projects/:project_slug/tasks/:task_id/related', array('controller' => 'related_tasks'), array('task_id' => Router::MATCH_ID));
      Router::map('project_task_related_tasks_add', 'projects/:project_slug/tasks/:task_id/related/add', array('controller' => 'related_tasks', 'action' => 'add_task'), array('task_id' => Router::MATCH_ID));
      Router::map('project_task_related_tasks_remove', 'projects/:project_slug/tasks/:task_id/related/:related_task_id/remove', array('controller' => 'related_tasks', 'action' => 'remove_task'), array('task_id' => Router::MATCH_ID, 'related_task_id' => Router::MATCH_ID));

      // Milestone tasks
      Router::map('milestone_tasks', 'projects/:project_slug/milestones/:milestone_id/tasks', array('controller' => 'milestone_tasks', 'action' => 'index'), array('milestone_id' => Router::MATCH_ID));

      // Public tasks
      Router::map('public_tasks', 'tasks', array('controller' => 'public_tasks', 'action' => 'index'));
      Router::map('public_tasks_check', 'tasks/check', array('controller' => 'public_tasks', 'action' => 'check'));
      Router::map('public_task_form_submit', 'tasks/submit/:public_task_form_slug', array('controller' => 'public_task_forms', 'action' => 'submit'));
      Router::map('public_task_form_success', 'tasks/submit-successful', array('controller' => 'public_task_forms', 'action' => 'success'));

      Router::map('public_task', 'tasks/:task_id', array('controller' => 'public_tasks', 'action' => 'view'), array('task_id' => Router::MATCH_ID));

      // Tasks admin
      Router::map('tasks_admin', 'admin/tasks', array('controller' => 'tasks_admin'));
      Router::map('tasks_admin_settings', 'admin/tasks/settings', array('controller' => 'tasks_admin', 'action' => 'settings'));

      Router::map('tasks_admin_resolve_duplicate_id', 'admin/tasks/resolve-duplicate', array('controller' => 'tasks_admin', 'action' => 'resolve_duplicate_ids'));
      Router::map('tasks_admin_do_resolve_duplicate_id', 'admin/tasks/do-resolve-duplicate', array('controller' => 'tasks_admin', 'action' => 'do_resolve_duplicate_ids'));

      Router::map('public_task_forms_add', 'admin/tasks/forms/add', array('controller' => 'public_task_forms_admin', 'action' => 'add'), array('public_task_form_id' => Router::MATCH_ID));

      Router::map('public_task_form', 'admin/tasks/forms/:public_task_form_id', array('controller' => 'public_task_forms_admin', 'action' => 'view'), array('public_task_form_id' => Router::MATCH_ID));
      Router::map('public_task_form_edit', 'admin/tasks/forms/:public_task_form_id/edit', array('controller' => 'public_task_forms_admin', 'action' => 'edit'), array('public_task_form_id' => Router::MATCH_ID));
      Router::map('public_task_form_enable', 'admin/tasks/forms/:public_task_form_id/enable', array('controller' => 'public_task_forms_admin', 'action' => 'enable'), array('public_task_form_id' => Router::MATCH_ID));
      Router::map('public_task_form_disable', 'admin/tasks/forms/:public_task_form_id/disable', array('controller' => 'public_task_forms_admin', 'action' => 'disable'), array('public_task_form_id' => Router::MATCH_ID));
      Router::map('public_task_form_delete', 'admin/tasks/forms/:public_task_form_id/delete', array('controller' => 'public_task_forms_admin', 'action' => 'delete'), array('public_task_form_id' => Router::MATCH_ID));

      AngieApplication::getModule('subscriptions')->defineSubscriptionRoutesFor('public_task_form', 'admin/tasks/forms/:public_task_form_id/delete', 'public_task_forms_admin', TASKS_MODULE, array('public_task_form_id' => Router::MATCH_ID));

      Router::map('public_task_form_subscribers', 'admin/tasks/forms/project/:project_id/subscribers', array('controller' => 'public_task_forms_admin', 'action' => 'subscribers')); // This is used to reload users when project is changed in new / edit public task form URL

      // Project tasks reports

      Router::map('project_tasks_aggregated_report', 'reports/project-aggregated-tasks', array('controller' => 'tasks_reports', 'action' => 'aggregated_tasks'));
      Router::map('project_tasks_aggregated_report_run', 'reports/project-aggregated-tasks-run', array('controller' => 'tasks_reports', 'action' => 'aggregated_tasks_run'));

      // Tracking
      if(AngieApplication::isModuleLoaded('tracking')) {
        AngieApplication::getModule('tracking')->defineTrackingRoutesFor('project_task', 'projects/:project_slug/tasks/:task_id', 'tasks', TASKS_MODULE, array('task_id' => Router::MATCH_ID));
      } // if

      // Invoicing
      if(AngieApplication::isModuleLoaded('invoicing')) {
        AngieApplication::getModule('invoicing')->defineInvoiceRoutesFor('project_task', 'task/invoicing/:task_id', 'tasks', TASKS_MODULE, array('task_id' => Router::MATCH_ID));
      } // if

      Router::map('activity_logs_admin_rebuild_tasks', 'admin/indices/activity-logs/rebuild/tasks', array('controller' => 'activity_logs_admin', 'action' => 'rebuild_tasks'));
      Router::map('object_contexts_admin_rebuild_tasks', 'admin/indices/object-contexts/rebuild/tasks', array('controller' => 'object_contexts_admin', 'action' => 'rebuild_tasks'));

      // Reports, segments etc
      Router::map('task_segments', 'reports/analyzer/task-segments', array('controller' => 'task_segments'));
      Router::map('task_segments_add', 'reports/analyzer/task-segments/add', array('controller' => 'task_segments', 'action' => 'add'));

      Router::map('task_segment', 'reports/analyzer/task-segments/:task_segment_id', array('controller' => 'task_segments', 'action' => 'view'), array('task_segment_id' => Router::MATCH_ID));
      Router::map('task_segment_edit', 'reports/analyzer/task-segments/:task_segment_id/edit', array('controller' => 'task_segments', 'action' => 'edit'), array('task_segment_id' => Router::MATCH_ID));
      Router::map('task_segment_delete', 'reports/analyzer/task-segments/:task_segment_id/delete', array('controller' => 'task_segments', 'action' => 'delete'), array('task_segment_id' => Router::MATCH_ID));

      AngieApplication::getModule('reports')->defineDataFilterRoutes('open_vs_completed_tasks_report', 'tasks/open-vs-completed', 'open_vs_completed_tasks_reports', TASKS_MODULE);
      AngieApplication::getModule('reports')->defineDataFilterRoutes('weekly_created_tasks_report', 'tasks/weekly-created', 'weekly_created_tasks_reports', TASKS_MODULE);
      AngieApplication::getModule('reports')->defineDataFilterRoutes('weekly_completed_tasks_report', 'tasks/weekly-completed', 'weekly_completed_tasks_reports', TASKS_MODULE);
    } // defineRoutes

    /**
     * Define event handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_admin_panel', 'on_admin_panel');
      EventsManager::listen('on_all_indices', 'on_all_indices');
      EventsManager::listen('on_get_completable_project_object_types', 'on_get_completable_project_object_types');
      EventsManager::listen('on_project_tabs', 'on_project_tabs');
      EventsManager::listen('on_available_project_tabs', 'on_available_project_tabs');
      EventsManager::listen('on_milestone_sections', 'on_milestone_sections');
      EventsManager::listen('on_master_categories', 'on_master_categories');
      EventsManager::listen('on_project_export', 'on_project_export');
      EventsManager::listen('on_project_permissions', 'on_project_permissions');
      EventsManager::listen('on_quick_add', 'on_quick_add');
      EventsManager::listen('on_build_project_search_index', 'on_build_project_search_index');
      EventsManager::listen('on_build_names_search_index_for_project', 'on_build_names_search_index_for_project');
      EventsManager::listen('on_project_subcontext_permission', 'on_project_subcontext_permission');
      EventsManager::listen('on_homescreen_widget_types', 'on_homescreen_widget_types');
      EventsManager::listen('on_rebuild_activity_log_actions', 'on_rebuild_activity_log_actions');
      EventsManager::listen('on_rebuild_all_indices', 'on_rebuild_all_indices');
      EventsManager::listen('on_reports_panel', 'on_reports_panel');
      EventsManager::listen('on_rebuild_object_contexts_actions', 'on_rebuild_object_contexts_actions');
      EventsManager::listen('on_object_from_notification_context', 'on_object_from_notification_context');
      EventsManager::listen('on_object_inspector', 'on_object_inspector');
      EventsManager::listen('on_custom_field_disabled', 'on_custom_field_disabled');
      EventsManager::listen('on_incoming_mail_actions', 'on_incoming_mail_actions');

    } // defineHandlers

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

    /**
     * Tasks module can't be uninstalled
     *
     * @param User $user
     * @return bool
     */
    function canUninstall(User $user) {
      return false;
    } // canUninstall

    // ---------------------------------------------------
    //  Name
    // ---------------------------------------------------

    /**
     * Get module display name
     *
     * @return string
     */
    function getDisplayName() {
      return lang('Tasks');
    } // getDisplayName

    /**
     * Return module description
     *
     * @return string
     */
    function getDescription() {
      return lang('Adds task management to projects');
    } // getDescription

    /**
     * Return module uninstallation message
     *
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated. All tasks from all projects will be deleted');
    } // getUninstallMessage

    /**
     * Return object types (class names) that this module is working with
     *
     * @return array
     */
    function getObjectTypes() {
      return array('Task');
    } // getObjectTypes

    // ---------------------------------------------------
    //  Install / Uninstall
    // ---------------------------------------------------

    /**
     * Uninstall tasks module
     */
    function uninstall() {
      parent::uninstall();

      CustomFields::dropForType('Task');
    } // uninstall

  }