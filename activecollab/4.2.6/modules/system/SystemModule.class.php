<?php

  /**
   * System module definition
   *
   * @package activeCollab.modules.system
   */
  class SystemModule extends AngieModule {
    
    /**
     * Plain module name
     *
     * @var string
     */
    protected $name = 'system';
    
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
      Router::map('application_update', 'admin/update', array('controller' => 'update', 'action' => 'index'));
      Router::map('application_update_check_password', 'admin/update/check-password', array('controller' => 'update', 'action' => 'check_password'));
      Router::map('application_update_download_package', 'admin/update/download-update-package', array('controller' => 'update', 'action' => 'download_update_package'));
      Router::map('application_update_check_download_progress', 'admin/update/check-download-progress', array('controller' => 'update', 'action' => 'check_download_progress'));
      Router::map('application_unpack_download_package', 'admin/update/unpack-update-package', array('controller' => 'update', 'action' => 'unpack_update_package'));
      Router::map('application_update_get_upgrade_steps', 'admin/update/get-upgrade-steps', array('controller' => 'update', 'action' => 'get_upgrade_steps'));
      Router::map('application_update_install_new_modules', 'admin/update/install-new-modules', array('controller' => 'update', 'action' => 'install_new_modules'));

      Router::map('new_version_details', 'admin/new-version', array('controller' => 'update', 'action' => 'check_for_new_version'));
      Router::map('save_license_details', 'admin/save-license-details', array('controller' => 'update', 'action' => 'save_license_details'));

      // API specific
      Router::map('project_roles_info', 'info/roles/project', array('controller' => 'roles_info', 'action' => 'project_roles'));
      Router::map('project_role_info', 'info/roles/project/:role_id', array('controller' => 'roles_info', 'action' => 'project_role'), array('role_id' => Router::MATCH_ID));
      
      // Dashboard
      Router::map('my_tasks', 'my-tasks', array('controller' => 'backend', 'action' => 'my_tasks'));
      Router::map('whats_new', 'whats-new', array('controller' => 'backend', 'action' => 'whats_new'));
      Router::map('custom_tab', 'custom-tab/:homescreen_tab_id', array('controller' => 'backend', 'action' => 'custom_tab'), array('homescreen_tab_id' => Router::MATCH_ID));

      // Search
      Router::map('quick_backend_search', 'search/quick', array('controller' => 'backend_search', 'action' => 'quick_search'));
      
      // People
      Router::map('people', 'people', array('controller' => 'people', 'action' => 'index'));
      Router::map('people_printable', 'people/printable', array('controller' => 'people', 'action' => 'index_printable'));
      Router::map('people_import_vcard', 'people/import-vcard', array('controller' => 'people', 'action' => 'import_vcard'));
//      Router::map('people_export_vcard', 'people/export-vcard', array('controller' => 'people', 'action' => 'export_vcard'));
//      Router::map('people_export_individual_vcards', 'people/export-individual-vcard', array('controller' => 'people', 'action' => 'export_individual_vcards'));
      Router::map('people_invite', 'people/invite', array('controller' => 'people', 'action' => 'invite'));
      Router::map('people_archive', 'people/archive', array('controller' => 'people', 'action' => 'archive'));
      Router::map('people_archive_printable', 'people/archive/printable', array('controller' => 'people', 'action' => 'archive_printable'));
      
      Router::map('people_mass_edit', 'people/mass-edit', array('controller' => 'people', 'action' => 'mass_edit'));
     
      Router::map('people_companies_add', 'people/add-company', array('controller' => 'companies', 'action' => 'add'));
      Router::map('people_company', 'people/:company_id', array('controller' => 'companies', 'action' => 'view'), array('company_id' => Router::MATCH_ID));
      
      Router::map('people_company_details', 'people/company_details', array('controller' => 'companies', 'action' => 'company_details'));
      Router::map('people_company_edit', 'people/:company_id/edit', array('controller' => 'companies', 'action' => 'edit'), array('company_id' => Router::MATCH_ID));
      Router::map('people_company_export_vcard', 'people/:company_id/export-vcard', array('controller' => 'companies', 'action' => 'export_vcard'), array('company_id' => Router::MATCH_ID));
      Router::map('people_company_delete', 'people/:company_id/delete', array('controller' => 'companies', 'action' => 'delete'), array('company_id' => Router::MATCH_ID));
      Router::map('people_company_edit_logo', 'people/:company_id/edit-logo', array('controller' => 'companies', 'action' => 'edit_logo'), array('company_id' => Router::MATCH_ID));
      Router::map('people_company_delete_logo', 'people/:company_id/delete-logo', array('controller' => 'companies', 'action' => 'delete_logo'), array('company_id' => Router::MATCH_ID));
      
      AngieApplication::getModule('environment')->defineStateRoutesFor('people_company', 'people/:company_id', 'companies', SYSTEM_MODULE, array('company_id' => Router::MATCH_ID));
      AngieApplication::getModule('avatar')->defineAvatarRoutesFor('people_company', 'people/:company_id', 'companies', SYSTEM_MODULE, array('company_id' => Router::MATCH_ID));

	    // People company footprints
	    if (AngieApplication::isModuleLoaded('footprints')) {
		    AngieApplication::getModule('footprints')->defineAccessLogRoutesFor('people_company', 'people/:company_id', 'companies', SYSTEM_MODULE, array('company_id' => Router::MATCH_ID));
		    AngieApplication::getModule('footprints')->defineHistoryOfChangesRoutesFor('people_company', 'people/:company_id', 'companies', SYSTEM_MODULE, array('company_id' => Router::MATCH_ID));
	    } // if
      
      Router::map('people_company_projects', 'people/:company_id/projects', array('controller' => 'company_projects', 'action' => 'index'), array('company_id' => Router::MATCH_ID));
      Router::map('people_company_projects_archive', 'people/:company_id/projects/archive', array('controller' => 'company_projects', 'action' => 'archive'), array('company_id' => Router::MATCH_ID));

      Router::map('people_company_project_requests', 'people/:company_id/project-requests', array('controller' => 'company_project_requests', 'action' => 'index'), array('company_id' => Router::MATCH_ID));
      
      Router::map('people_company_users', 'people/:company_id/users', array('controller' => 'users'), array('company_id' => Router::MATCH_ID));
      Router::map('people_company_users_archive', 'people/:company_id/users/archive', array('controller' => 'users', 'action' => 'archive'), array('company_id' => Router::MATCH_ID));
      Router::map('people_company_users_with_permissions', 'people/:company_id/users/with-permissions', array('controller' => 'users', 'action' => 'users_with_permissions'), array('company_id' => Router::MATCH_ID));
      
      Router::map('people_company_user', 'people/:company_id/users/:user_id', array('controller' => 'users', 'action' => 'view'), array('company_id' => Router::MATCH_ID, 'user_id' => Router::MATCH_ID));
      Router::map('people_company_user_add', 'people/:company_id/add-user', array('controller' => 'users', 'action' => 'add'), array('company_id' => Router::MATCH_ID));
      Router::map('people_company_user_edit', 'people/:company_id/users/:user_id/edit', array('controller' => 'users', 'action' => 'edit'), array('company_id' => Router::MATCH_ID, 'user_id' => Router::MATCH_ID));
      Router::map('people_company_user_edit_profile', 'people/:company_id/users/:user_id/edit-profile', array('controller' => 'users', 'action' => 'edit_profile'), array('company_id' => Router::MATCH_ID, 'user_id' => Router::MATCH_ID));
      Router::map('people_company_user_edit_settings', 'people/:company_id/users/:user_id/edit-settings', array('controller' => 'users', 'action' => 'edit_settings'), array('company_id' => Router::MATCH_ID, 'user_id' => Router::MATCH_ID));
      Router::map('people_company_user_edit_company_and_role', 'people/:company_id/users/:user_id/edit-company-and-role', array('controller' => 'users', 'action' => 'edit_company_and_role'), array('company_id' => Router::MATCH_ID, 'user_id' => Router::MATCH_ID));
      Router::map('people_company_user_export_vcard', 'people/:company_id/users/:user_id/export-vcard', array('controller' => 'users', 'action' => 'export_vcard'), array('company_id' => Router::MATCH_ID, 'user_id' => Router::MATCH_ID));
      Router::map('people_company_user_delete', 'people/:company_id/users/:user_id/delete', array('controller' => 'users', 'action' => 'delete'), array('company_id' => Router::MATCH_ID, 'user_id' => Router::MATCH_ID));
      Router::map('people_company_user_edit_password', 'people/:company_id/users/:user_id/edit-password', array('controller' => 'users', 'action' => 'edit_password'), array('company_id' => Router::MATCH_ID, 'user_id' => Router::MATCH_ID));
      
      Router::map('people_company_user_recent_activities', 'people/:company_id/users/:user_id/recent-activities', array('controller' => 'users', 'action' => 'recent_activities'), array('company_id' => Router::MATCH_ID, 'user_id' => Router::MATCH_ID));
      Router::map('people_company_user_send_welcome_message', 'people/:company_id/users/:user_id/send-welcome-message', array('controller' => 'users', 'action' => 'send_welcome_message'), array('company_id' => Router::MATCH_ID, 'user_id' => Router::MATCH_ID));
      Router::map('people_company_user_set_as_invited', 'people/:company_id/users/:user_id/set-as-invited', array('controller' => 'users', 'action' => 'set_as_invited'), array('company_id' => Router::MATCH_ID, 'user_id' => Router::MATCH_ID));
      Router::map('people_company_user_login_as', 'people/:company_id/users/:user_id/login-as', array('controller' => 'users', 'action' => 'login_as'), array('company_id' => Router::MATCH_ID, 'user_id' => Router::MATCH_ID));
      
      // activeCollab specific add to / remove from favorites routes
     	Router::map('people_company_user_favorites', 'people/:company_id/users/:user_id/favorites', array('controller' => 'favorites'), array('user_id' => Router::MATCH_ID), array('company_id' => Router::MATCH_ID, 'user_id' => Router::MATCH_ID));
      Router::map('people_company_user_add_to_favorites', 'people/:company_id/users/:user_id/favorites/add', array('controller' => 'favorites', 'action' => 'add_to_favorites'), array('company_id' => Router::MATCH_ID, 'user_id' => Router::MATCH_ID));
      Router::map('people_company_user_remove_from_favorites', 'people/:company_id/users/:user_id/favorites/remove', array('controller' => 'favorites', 'action' => 'remove_from_favorites'), array('company_id' => Router::MATCH_ID, 'user_id' => Router::MATCH_ID));
      
      AngieApplication::getModule('authentication')->defineApiClientSubscriptionsRoutesFor('people_company_user', 'people/:company_id/users/:user_id', 'users', SYSTEM_MODULE, array('company_id' => Router::MATCH_ID, 'user_id' => Router::MATCH_ID));
      AngieApplication::getModule('environment')->defineStateRoutesFor('people_company_user', 'people/:company_id/users/:user_id', 'users', SYSTEM_MODULE, array('company_id' => Router::MATCH_ID, 'user_id' => Router::MATCH_ID));
      AngieApplication::getModule('avatar')->defineAvatarRoutesFor('people_company_user', 'people/:company_id/users/:user_id', 'users', SYSTEM_MODULE, array('company_id' => Router::MATCH_ID, 'user_id' => Router::MATCH_ID));
      AngieApplication::getModule('homescreens')->defineHomescreenRoutesFor('people_company_user', 'people/:company_id/users/:user_id', 'users', SYSTEM_MODULE, array('company_id' => Router::MATCH_ID, 'user_id' => Router::MATCH_ID));
      AngieApplication::getModule('reminders')->defineUserRemindersRoutesFor('people_company_user', 'people/:company_id/users/:user_id', 'users', SYSTEM_MODULE, array('company_id' => Router::MATCH_ID, 'user_id' => Router::MATCH_ID));
      AngieApplication::getModule('activity_logs')->defineActivityLogsRoutesFor('people_company_user', 'people/:company_id/users/:user_id', 'users', SYSTEM_MODULE, array('company_id' => Router::MATCH_ID, 'user_id' => Router::MATCH_ID));

      // User projects
      Router::map('people_company_user_projects', 'people/:company_id/users/:user_id/projects', array('controller' => 'user_projects', 'action' => 'index'), array('company_id' => Router::MATCH_ID, 'user_id' => Router::MATCH_ID));
      Router::map('people_company_user_projects_archive', 'people/:company_id/users/:user_id/projects/archive', array('controller' => 'user_projects', 'action' => 'archive'), array('company_id' => Router::MATCH_ID, 'user_id' => Router::MATCH_ID));
      Router::map('people_company_user_add_to_projects', 'people/:company_id/users/:user_id/add-to-projects', array('controller' => 'user_projects', 'action' => 'add_to_projects'), array('company_id' => Router::MATCH_ID, 'user_id' => Router::MATCH_ID));
      
      // Projects
      Router::map('projects', 'projects', array('controller' => 'projects'));
      Router::map('projects_mass_edit', 'projects/multiple/mass-edit', array('controller' => 'projects', 'action' => 'mass_edit'));
      Router::map('project', 'projects/:project_slug', array('controller' => 'project', 'action' => 'index')); // Defined here, so other global /projects actions can override it
      Router::map('project_mail_to_project_learn_more', 'projects/:project_slug/m2p/learn_more', array('controller' => 'project', 'action' => 'mail_to_project_learn_more'), array('project_slug' => Router::MATCH_SLUG));

      AngieApplication::getModule('complete')->defineChangeStatusRoutesFor('project', 'projects/:project_slug', 'project_complete', SYSTEM_MODULE);
      AngieApplication::getModule('avatar')->defineAvatarRoutesFor('project', 'project/:project_slug', 'project', SYSTEM_MODULE);
      
      Router::map('projects_add', 'projects/add', array('controller' => 'project', 'action' => 'add'));
      Router::map('projects_archive', 'projects/archive', array('controller' => 'projects', 'action' => 'archive'));
      Router::map('projects_what_to_sync', 'projects/what-to-sync', array('controller' => 'projects', 'action' => 'what_to_sync'));
      Router::map('project_labels', 'info/labels/project', array('controller' => 'projects', 'action' => 'labels'));
      
      // Project categories
      AngieApplication::getModule('categories')->defineCategoriesRoutesFor('project', 'projects', 'projects', SYSTEM_MODULE);
      AngieApplication::getModule('categories')->defineCategoryRoutesFor('project', 'projects/:project_slug', 'project', SYSTEM_MODULE);
      AngieApplication::getModule('environment')->defineStateRoutesFor('project', 'projects/:project_slug', 'project', SYSTEM_MODULE);
      AngieApplication::getModule('complete')->defineChangeStatusRoutesFor('project', 'projects/:project_slug', 'project', SYSTEM_MODULE);
      AngieApplication::getModule('labels')->defineLabelsRoutesFor('project', 'projects/:project_slug', 'project', SYSTEM_MODULE);

	    // Project footprints
	    if (AngieApplication::isModuleLoaded('footprints')) {
		    AngieApplication::getModule('footprints')->defineAccessLogRoutesFor('project', 'projects/:project_slug', 'project', SYSTEM_MODULE);
		    AngieApplication::getModule('footprints')->defineHistoryOfChangesRoutesFor('project', 'projects/:project_slug', 'project', SYSTEM_MODULE);
	    } // if
      
      // Project tracking
      if(AngieApplication::isModuleLoaded('tracking')) {
        AngieApplication::getModule('tracking')->defineTrackingRoutesFor('project', 'projects/:project_slug', 'project', SYSTEM_MODULE);
      } // if
      
      // Invoicing
      if(AngieApplication::isModuleLoaded('invoicing')) {
        AngieApplication::getModule('invoicing')->defineInvoiceRoutesFor('project', 'project/:project_slug', 'project', SYSTEM_MODULE, array('project_slug' => Router::MATCH_SLUG));
        AngieApplication::getModule('invoicing')->defineInvoiceRoutesFor('project_milestone', 'milestone', 'milestones', SYSTEM_MODULE);
      } // if
      
      // Single project
      Router::map('project_user_tasks', 'projects/:project_slug/user-tasks', array('controller' => 'project', 'action' => 'user_tasks'));
      Router::map('project_user_subscriptions', 'projects/:project_slug/user-subscriptions', array('controller' => 'project', 'action' => 'user_subscriptions'));
      Router::map('project_user_subscriptions_mass_unsubscribe', 'projects/:project_slug/user-subscriptions-mass-unsubscribe', array('controller' => 'project', 'action' => 'user_subscriptions_mass_unsubscribe'));
      Router::map('project_comments', 'projects/:project_slug/comments', array('controller' => 'project', 'action' => 'comments'));
      Router::map('project_attachments', 'projects/:project_slug/attachments', array('controller' => 'project', 'action' => 'attachments'));
      Router::map('project_subtasks', 'projects/:project_slug/subtasks', array('controller' => 'project', 'action' => 'subtasks'));
      Router::map('project_ical', 'projects/:project_slug/ical', array('controller' => 'project', 'action' => 'ical'));
      Router::map('project_ical_subscribe', 'projects/:project_slug/ical-subscribe', array('controller' => 'project', 'action' => 'ical_subscribe'));
      Router::map('project_edit', 'projects/:project_slug/edit', array('controller' => 'project', 'action' => 'edit'));

      AngieApplication::getModule('activity_logs')->defineActivityLogsRoutesFor('project', 'projects/:project_slug', 'project', SYSTEM_MODULE);
      
      Router::map('project_delete', 'projects/:project_slug/delete', array('controller' => 'project', 'action' => 'delete'));
      Router::map('project_pin', 'projects/:project_slug/pin', array('controller' => 'project', 'action' => 'pin'));
      Router::map('project_unpin', 'projects/:project_slug/unpin', array('controller' => 'project', 'action' => 'unpin'));
      Router::map('project_export', 'projects/:project_slug/export', array('controller' => 'project', 'action' => 'export'));
      Router::map('project_export_as_file', 'projects/:project_slug/export-as-file', array('controller' => 'project', 'action' => 'export_as_file'));

      Router::map('project_sync_lock', 'projects/:project_slug/sync-lock', array('controller' => 'project', 'action' => 'sync_lock'));
      Router::map('project_sync_unlock', 'projects/:project_slug/sync-unlock', array('controller' => 'project', 'action' => 'sync_unlock'));
      
      Router::map('project_edit_icon', 'projects/:project_slug/icon/edit', array('controller' => 'project_icon', 'action' => 'edit'));
      Router::map('project_delete_icon', 'projects/:project_slug/icon/delete', array('controller' => 'project_icon', 'action' => 'delete'));
      
      Router::map('project_settings', 'projects/:project_slug/settings', array('controller' => 'project', 'action' => 'settings'));
      
      // Project people
      Router::map('project_people', 'projects/:project_slug/people', array('controller' => 'project_people', 'action' => 'index'));
      Router::map('project_people_add', 'projects/:project_slug/people/add', array('controller' => 'project_people', 'action' => 'add_people'));
      Router::map('project_replace_user', 'projects/:project_slug/people/:user_id/replace', array('controller' => 'project_people', 'action' => 'replace_user'), array('user_id' => Router::MATCH_ID));
      Router::map('project_remove_user', 'projects/:project_slug/people/:user_id/remove-from-project', array('controller' => 'project_people', 'action' => 'remove_user'), array('user_id' => Router::MATCH_ID));
      Router::map('project_user_permissions', 'projects/:project_slug/people/:user_id/change-permissions', array('controller' => 'project_people', 'action' => 'user_permissions'), array('user_id' => Router::MATCH_ID));
      
      // Project milestones
      Router::map('project_milestones', 'projects/:project_slug/milestones', array('controller' => 'milestones', 'action' => 'index'));
      Router::map('project_milestones_archive', 'projects/:project_slug/milestones/archive', array('controller' => 'milestones', 'action' => 'archive'));
      Router::map('project_milestones_reorder', 'projects/:project_slug/milestones/reorder', array('controller' => 'milestones', 'action' => 'reorder'));
      Router::map('project_milestones_add', 'projects/:project_slug/milestones/add', array('controller' => 'milestones', 'action' => 'add'));
      Router::map('project_milestones_export', 'projects/:project_slug/milestones/export', array('controller' => 'milestones', 'action' => 'export'));
      
      Router::map('project_milestone', 'projects/:project_slug/milestones/:milestone_id', array('controller' => 'milestones', 'action' => 'view'), array('milestone_id' => Router::MATCH_ID));
      Router::map('project_milestone_edit', 'projects/:project_slug/milestones/:milestone_id/edit', array('controller' => 'milestones', 'action' => 'edit'), array('milestone_id' => Router::MATCH_ID));
      Router::map('project_milestone_comments', 'projects/:project_slug/milestones/:milestone_id/comments', array('controller' => 'milestones', 'action' => 'comments'), array('milestone_id' => Router::MATCH_ID));
      
      AngieApplication::getModule('complete')->defineChangeStatusRoutesFor('project_milestone', 'projects/:project_slug/milestones/:milestone_id', 'milestones', SYSTEM_MODULE, array('milestone_id' => Router::MATCH_ID));
      AngieApplication::getModule('complete')->definePriorityRoutesFor('project_milestone', 'projects/:project_slug/milestones/:milestone_id', 'milestones', SYSTEM_MODULE, array('milestone_id' => Router::MATCH_ID));
      AngieApplication::getModule('environment')->defineStateRoutesFor('project_milestone', 'projects/:project_slug/milestones/:milestone_id', 'milestones', SYSTEM_MODULE, array('milestone_id' => Router::MATCH_ID));
      AngieApplication::getModule('comments')->defineCommentsRoutesFor('project_milestone', 'projects/:project_slug/milestones/:milestone_id', 'milestones', SYSTEM_MODULE, array('milestone_id' => Router::MATCH_ID));
      AngieApplication::getModule('subscriptions')->defineSubscriptionRoutesFor('project_milestone', 'projects/:project_slug/milestones/:milestone_id', 'milestones', SYSTEM_MODULE, array('milestone_id' => Router::MATCH_ID));
      AngieApplication::getModule('reminders')->defineRemindersRoutesFor('project_milestone', 'projects/:project_slug/milestones/:milestone_id', 'milestones', SYSTEM_MODULE, array('milestone_id' => Router::MATCH_ID));
      AngieApplication::getModule('assignees')->defineAssigneesRoutesFor('project_milestone', 'projects/:project_slug/milestones/:milestone_id', 'milestones', SYSTEM_MODULE, array('milestone_id' => Router::MATCH_ID));
      AngieApplication::getModule('attachments')->defineAttachmentsRoutesFor('project_milestone', 'projects/:project_slug/milestones/:milestone_id', 'milestones', SYSTEM_MODULE, array('milestone_id' => Router::MATCH_ID));

	    // Project milestone footprints
	    if (AngieApplication::isModuleLoaded('footprints')) {
		    AngieApplication::getModule('footprints')->defineAccessLogRoutesFor('project_milestone', 'projects/:project_slug/milestones/:milestone_id', 'milestones', SYSTEM_MODULE, array('milestone_id' => Router::MATCH_ID));
		    AngieApplication::getModule('footprints')->defineHistoryOfChangesRoutesFor('project_milestone', 'projects/:project_slug/milestones/:milestone_id', 'milestones', SYSTEM_MODULE, array('milestone_id' => Router::MATCH_ID));
	    } // if
      
      // Project outline
      Router::map('project_outline', 'projects/:project_slug/outline', array('controller' => 'project_outline', 'action' => 'index'));
      Router::map('project_outline_shortcuts', 'projects/:project_slug/outline/shortcuts', array('controller' => 'project_outline', 'action' => 'shortcuts'));
      Router::map('project_outline_subobjects', 'projects/:project_slug/outline/:object_type/:object_id/subobjects', array('controller' => 'project_outline', 'action' => 'subobjects'));
      Router::map('project_outline_reorder', 'projects/:project_slug/outline/:object_type/:object_id/reorder', array('controller' => 'project_outline', 'action' => 'reorder'));
      Router::map('project_outline_mass_edit', 'projects/:project_slug/outline/mass_edit', array('controller' => 'project_outline', 'action' => 'mass_edit', array('parent_id' => Router::MATCH_ID, 'subtask_id' => Router::MATCH_ID)));
      
      // Project objects (generic, this stuff is usually overriden)
      Router::map('project_objects', 'projects/:project_slug/objects', array('controller' => 'projects', 'action' => 'overview'));
      Router::map('project_object_move', 'projects/:project_slug/objects/:object_id/move', array('controller' => 'project_objects', 'action' => 'move'), array('object_id' => Router::MATCH_ID));
      Router::map('project_object_copy', 'projects/:project_slug/objects/:object_id/copy', array('controller' => 'project_objects', 'action' => 'copy'), array('object_id' => Router::MATCH_ID));
      Router::map('project_object_update_milestone', 'projects/:project_slug/objects/:object_id/update-milestone', array('controller' => 'milestones', 'action' => 'update_milestone'), array('object_id' => Router::MATCH_ID));

      AngieApplication::getModule('reports')->defineDataFilterRoutes('assignment_filter', 'assignments', 'assignment_filters', SYSTEM_MODULE);
      
      // Workload report
      if(AngieApplication::isModuleLoaded('tasks')) {
        AngieApplication::getModule('reports')->defineDataFilterRoutes('workload_report', 'workload', 'workload_reports', SYSTEM_MODULE);
      } // if
      
      // Project requests
      Router::map('project_requests', 'projects/requests', array('controller' => 'project_requests'));
      Router::map('project_requests_archive', 'projects/requests/archive', array('controller' => 'project_requests', 'action' => 'archive'));
      Router::map('project_requests_mass_edit', 'projects/requests/mass-edit', array('controller' => 'project_requests', 'action' => 'mass_edit'));
      Router::map('project_requests_add', 'projects/requests/add', array('controller' => 'project_requests', 'action' => 'add'));
      
      Router::map('project_request', 'projects/requests/:project_request_id', array('controller' => 'project_requests', 'action' => 'view'), array('project_request_id' => Router::MATCH_ID));
      Router::map('project_request_edit', 'projects/requests/:project_request_id/edit', array('controller' => 'project_requests', 'action' => 'edit'), array('project_request_id' => Router::MATCH_ID));
      Router::map('project_request_open', 'projects/requests/:project_request_id/open', array('controller' => 'project_requests', 'action' => 'open'), array('project_request_id' => Router::MATCH_ID));
      Router::map('project_request_close', 'projects/requests/:project_request_id/close', array('controller' => 'project_requests', 'action' => 'close'), array('project_request_id' => Router::MATCH_ID));
      Router::map('project_request_take', 'projects/requests/:project_request_id/take', array('controller' => 'project_requests', 'action' => 'take'), array('project_request_id' => Router::MATCH_ID));
      Router::map('project_request_create_project', 'projects/requests/:project_request_id/create-project', array('controller' => 'project_requests', 'action' => 'create_project'), array('project_request_id' => Router::MATCH_ID));
      Router::map('project_request_create_quote', 'projects/requests/:project_request_id/create-quote', array('controller' => 'project_requests', 'action' => 'create_quote'), array('project_request_id' => Router::MATCH_ID));
      Router::map('project_request_delete', 'projects/requests/:project_request_id/delete', array('controller' => 'project_requests', 'action' => 'delete'), array('project_request_id' => Router::MATCH_ID));
      Router::map('project_request_save_client', 'projects/requests/:project_request_id/save-client', array('controller' => 'project_requests', 'action' => 'save_client'), array('project_request_id' => Router::MATCH_ID));

      AngieApplication::getModule('attachments')->defineAttachmentsRoutesFor('project_request', 'project-requests/:project_request_id', 'project_requests', SYSTEM_MODULE, array('project_request_id' => Router::MATCH_ID));
      
      // Comments
      AngieApplication::getModule('comments')->defineCommentsRoutesFor('project_request', 'project-requests/:project_request_id', 'project_requests', SYSTEM_MODULE, array('project_request_id' => Router::MATCH_ID));
      
      // Subscriptions
      AngieApplication::getModule('subscriptions')->defineSubscriptionRoutesFor('project_request', 'project-requests/:project_request_id', 'project_requests', SYSTEM_MODULE, array('project_request_id' => Router::MATCH_ID));

	    // Project request footprints
	    if (AngieApplication::isModuleLoaded('footprints')) {
	      AngieApplication::getModule('footprints')->defineAccessLogRoutesFor('project_request', 'project-requests/:project_request_id', 'project_requests', SYSTEM_MODULE, array('project_request_id' => Router::MATCH_ID));
		    AngieApplication::getModule('footprints')->defineHistoryOfChangesRoutesFor('project_request', 'project-requests/:project_request_id', 'project_requests', SYSTEM_MODULE, array('project_request_id' => Router::MATCH_ID));
	    } // if
      
      // Public part of project requests
      Router::map('project_request_submit', 'project-requests/submit', array('controller' => 'project_requests_public', 'action' => 'index'));
      Router::map('project_request_check', 'project-requests/check/:project_request_public_id', array('controller' => 'project_requests_public', 'action' => 'view'), array('project_request_public_id' => Router::MATCH_WORD));

      // Project Templates
      Router::map('project_templates', 'projects/templates', array('controller' => 'project_templates', 'action' => 'index'));
	    Router::map('project_template', 'projects/templates/:template_id', array('controller' => 'project_templates', 'action' => 'view'));
	    Router::map('project_template_build', 'projects/templates/:template_id/build', array('controller' => 'project_templates', 'action' => 'build'));
	    Router::map('project_templates_add', 'projects/templates/add', array('controller' => 'project_templates', 'action' => 'add'));
	    Router::map('project_template_edit', 'projects/templates/:template_id/edit', array('controller' => 'project_templates', 'action' => 'edit'));
	    Router::map('project_template_delete', 'projects/templates/:template_id/delete', array('controller' => 'project_templates', 'action' => 'delete'));
	    Router::map('project_templates_reorder', 'projects/templates/reorder', array('controller' => 'project_templates', 'action' => 'reorder'));
			// Project Object Templater
	    Router::map('project_object_template', 'projects/templates/:template_id/:object_type/:object_id', array('controller' => 'project_object_templates', 'action' => 'view'), array('object_id' => Router::MATCH_ID));
	    Router::map('project_object_template_add', 'projects/templates/:template_id/:object_type/add', array('controller' => 'project_object_templates', 'action' => 'add'));
	    Router::map('project_template_file_add', 'projects/templates/:template_id/files/add', array('controller' => 'project_object_templates', 'action' => 'files_add'));
	    Router::map('project_object_template_edit', 'projects/templates/:template_id/:object_type/:object_id/edit', array('controller' => 'project_object_templates', 'action' => 'edit'), array('object_id' => Router::MATCH_ID));
	    Router::map('project_object_template_delete', 'projects/templates/:template_id/:object_type/:object_id/delete', array('controller' => 'project_object_templates', 'action' => 'delete'), array('object_id' => Router::MATCH_ID));
	    Router::map('project_object_template_subobjects', 'projects/templates/:template_id/:object_type/:object_id/subobjects', array('controller' => 'project_object_templates', 'action' => 'subobjects'), array('object_id' => Router::MATCH_ID));
	    Router::map('project_object_template_reorder', 'projects/templates/:template_id/:object_type/:object_id/reorder', array('controller' => 'project_object_templates', 'action' => 'reorder'), array('object_id' => Router::MATCH_ID));
	    Router::map('project_object_template_mass_edit', 'projects/templates/:template_id/mass_edit', array('controller' => 'project_object_templates', 'action' => 'mass_edit', array('parent_id' => Router::MATCH_ID, 'subtask_id' => Router::MATCH_ID)));
	    Router::map('project_object_template_shortcuts', 'projects/templates/:template_id/shortcuts', array('controller' => 'project_object_templates', 'action' => 'shortcuts'));
			// Project File Template Upload
	    Router::map('project_file_template_upload_compatibility', 'projects/templates/:template_id/:object_type/upload-compatibility', array('controller' => 'project_object_templates', 'action' => 'upload_compatibility'));
	    // Project Template Positions
	    Router::map('project_template_positions', 'projects/templates/:template_id/positions', array('controller' => 'project_templates', 'action' => 'positions'));
	    Router::map('project_template_min_data', 'projects/templates/:template_id/min-data', array('controller' => 'project_templates', 'action' => 'min_data'));

	    AngieApplication::getModule('avatar')->defineAvatarRoutesFor('project_template', 'projects/templates/:template_id', 'project_template', SYSTEM_MODULE);

	    // Shared object
      Router::map('shared_object', 's/:sharing_context/:sharing_code', array('controller' => 'frontend', 'action' => 'default_view_shared_object'), array('sharing_code' => Router::MATCH_WORD));

	    // Projects Timeline
	    Router::map('project_reschedule', 'projects/:project_slug/reschedule', array('controller' => 'project', 'action' => 'reschedule'));
	    Router::map('projects_timeline', 'projects/timeline', array('controller' => 'projects_timeline', 'action' => 'index'));

      AngieApplication::getModule('schedule')->defineScheduleRoutesFor('project_milestone', 'projects/:project_slug/milestones/:milestone_id', 'milestones', SYSTEM_MODULE, array('milestone_id' => Router::MATCH_ID));
			$this->defineMoveToProjectRoutesFor('project_milestone', 'projects/:project_slug/milestones/:milestone_id', 'milestones', SYSTEM_MODULE, array('milestone_id' => Router::MATCH_ID));
      
      // ---------------------------------------------------
      //  Administration
      // ---------------------------------------------------
      
      // Project roles
      Router::map('admin_project_roles', 'admin/roles/project', array('controller' => 'project_roles_admin', 'action' => 'index'));
      Router::map('admin_project_roles_add', 'admin/roles/project/add', array('controller' => 'project_roles_admin', 'action' => 'add'));
      
      Router::map('admin_project_role', 'admin/roles/project/:role_id', array('controller' => 'project_roles_admin', 'action' => 'view'), array('role_id' => Router::MATCH_ID));
      Router::map('admin_project_role_edit', 'admin/roles/project/:role_id/edit', array('controller' => 'project_roles_admin', 'action' => 'edit'), array('role_id' => Router::MATCH_ID));
      Router::map('admin_project_role_delete', 'admin/roles/project/:role_id/delete', array('controller' => 'project_roles_admin', 'action' => 'delete'), array('role_id' => Router::MATCH_ID));
      Router::map('admin_project_role_set_as_default', 'admin/roles/project/:role_id/set-as-default', array('controller' => 'project_roles_admin', 'action' => 'set_as_default'), array('role_id' => Router::MATCH_ID));
      
      // Project labels
      LabelsFramework::defineLabelsAdminRoutesFor('projects_admin', 'admin/project-labels', 'project_labels_admin', SYSTEM_MODULE);
      
      // Settings
      Router::map('admin_settings', 'admin', array('controller' => 'admin'));
      Router::map('admin_settings_general', 'admin/settings/general', array('controller' => 'settings', 'action' => 'general'));
      Router::map('admin_settings_categories', 'admin/settings/categories', array('controller' => 'categories_admin'));
      
      Router::map('identity_admin', 'admin/identity', array('controller' => 'identity_admin'));

      //Repsite
      Router::map('repsite_admin', 'admin/repsite', array('controller' => 'repsite_admin'));
      Router::map('repsite_admin_get_page', 'admin/repsite/get_page', array('controller' => 'repsite_admin', 'action' => 'get_page'));
      Router::map('repsite_admin_add_new_page', 'admin/repsite/add-new-page', array('controller' => 'repsite_admin', 'action' => 'add_new_page'));
      Router::map('repsite_admin_edit_repsite_domain', 'admin/repsite/edit-domain', array('controller' => 'repsite_admin', 'action' => 'edit_repsite_domain'));
      Router::map('repsite_admin_delete_page', 'admin/repsite/:page_id/delete', array('controller' => 'repsite_admin', 'action' => 'delete'), array('page_id' => Router::MATCH_ID));
      Router::map('repsite_admin_edit_page', 'admin/repsite/:page_id/edit', array('controller' => 'repsite_admin', 'action' => 'edit'), array('page_id' => Router::MATCH_ID));

      Router::map('identity_admin_revert', 'admin/identity/revert', array('controller' => 'identity_admin', 'action' => 'revert'));
      
      // Projects
      Router::map('admin_projects', 'admin/projects', array('controller' => 'projects_admin'));
      
      // Project requests
      Router::map('admin_project_requests', 'admin/project-requests', array('controller' => 'project_requests_admin'));

      // Projects data cleanup
      Router::map('admin_projects_data_cleanup', 'admin/projects-data-cleanup', array('controller' => 'projects_data_cleanup_admin'));
      Router::map('admin_projects_data_cleanup_permanently_delete_project', 'admin/projects-data-cleanup/:project_slug/permanently_delete', array('controller' => 'projects_data_cleanup_admin', 'action' => 'permanently_delete_project'));
      
      // Search index
      Router::map('projects_search_index_admin_build', 'admin/search/projects/build', array('controller' => 'projects_search_index_admin', 'action' => 'build', 'search_index_name' => 'projects'));
      Router::map('project_objects_search_index_admin_build', 'admin/search/project-objects/build', array('controller' => 'project_objects_search_index_admin', 'action' => 'build', 'search_index_name' => 'project_objects'));
      Router::map('names_search_index_admin_build', 'admin/search/names/build/:action', array('controller' => 'names_search_index_admin', 'search_index_name' => 'names'));
      
      // Activity
      Router::map('activity_logs_admin_rebuild_people', 'admin/indices/activity-logs/rebuild/people', array('controller' => 'activity_logs_admin', 'action' => 'rebuild_people'));
      Router::map('activity_logs_admin_rebuild_projects', 'admin/indices/activity-logs/rebuild/projects', array('controller' => 'activity_logs_admin', 'action' => 'rebuild_projects'));
      Router::map('activity_logs_admin_rebuild_milestones', 'admin/indices/activity-logs/rebuild/milestones', array('controller' => 'activity_logs_admin', 'action' => 'rebuild_milestones'));
      
      Router::map('object_contexts_admin_rebuild_people', 'admin/indices/object-contexts/rebuild/people', array('controller' => 'object_contexts_admin', 'action' => 'rebuild_people'));
      Router::map('object_contexts_admin_rebuild_projects', 'admin/indices/object-contexts/rebuild/projects', array('controller' => 'object_contexts_admin', 'action' => 'rebuild_projects'));
      Router::map('object_contexts_admin_rebuild_milestones', 'admin/indices/object-contexts/rebuild/milestones', array('controller' => 'object_contexts_admin', 'action' => 'rebuild_milestones'));
    
       // Incoming mail actions
      Router::map('project_action_project_changed', 'project_action/project_changed', array('controller' => 'project_action', 'action' => 'project_change'), array('project_id' => Router::MATCH_ID));

	    // Convert to a template
	    Router::map('project_convert_to_a_template', 'projects/:project_slug/convert-to-a-template', array('controller' => 'project', 'action' => 'convert_to_a_template'));

      // Send morning paper
      Router::map('paper', 'paper', array('controller' => 'scheduled_tasks', 'action' => 'paper', 'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO));
    } // defineRoutes
    
    /**
     * Define sharing routes for given context
     *
     * @param string $context
     * @param string $context_path
     * @param string $controller_name
     * @param string $module_name
     * @param array $context_requirements
     */                            
    function defineSharingRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
      Router::map("{$context}_sharing_settings", "$context_path/sharing", array('controller' => $controller_name, 'action' => "{$context}_sharing_settings", 'module' => $module_name), $context_requirements);
    } // defineSharingRoutesFor

	  /**
	   * Define project template add routes for given context
	   *
	   * @param string $context
	   * @param string $context_path
	   * @param string $controller_name
     * @param string $module_name
	   * @param array $context_requirements
	   */
	  function defineTamplateRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
		  Router::map("{$context}_add", "$context_path/add", array('controller' => $controller_name, 'action' => "{$context}_add", 'module' => $module_name), $context_requirements);
		  Router::map("{$context}_edit", "$context_path/edit", array('controller' => $controller_name, 'action' => "{$context}_edit", 'module' => $module_name), $context_requirements);
	  } // defineTamplateAddRoutesFor
    
    /**
     * Define schedule routes for given context
     *
     * @param string $context
     * @param string $context_path
     * @param string $controller_name
     * @param string $module_name
     * @param array $context_requirements
     */
    function defineMoveToProjectRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
      Router::map("{$context}_move_to_project", "$context_path/move-to-project", array('controller' => $controller_name, 'action' => "{$context}_move_to_project", 'module' => $module_name), $context_requirements);
      Router::map("{$context}_copy_to_project", "$context_path/copy-to-project", array('controller' => $controller_name, 'action' => "{$context}_copy_to_project", 'module' => $module_name), $context_requirements);
    } // defineMoveToProjectRoutesFor
    
    /**
     * Define event handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_visible_contexts', 'on_visible_contexts');
      
      EventsManager::listen('on_predefined_homescreen_tabs', 'on_predefined_homescreen_tabs');
      EventsManager::listen('on_homescreen_tab_types', 'on_homescreen_tab_types');
      EventsManager::listen('on_homescreen_widget_types', 'on_homescreen_widget_types');
      
      EventsManager::listen('on_initial_javascript_assign', 'on_initial_javascript_assign');

      EventsManager::listen('on_hourly', 'on_hourly');
      EventsManager::listen('on_daily', 'on_daily');

      EventsManager::listen('on_project_export', 'on_project_export');
      EventsManager::listen('on_project_overview_sidebars', 'on_project_overview_sidebars');
      EventsManager::listen('on_projects_tabs', 'on_projects_tabs');
      EventsManager::listen('on_available_project_tabs', 'on_available_project_tabs');
      EventsManager::listen('on_project_brief_stats', 'on_project_brief_stats');
      
			EventsManager::listen('on_inline_tabs', 'on_inline_tabs');
      
      EventsManager::listen('on_settings_sections', 'on_settings_sections');
      EventsManager::listen('on_admin_panel', 'on_admin_panel');

      EventsManager::listen('on_custom_user_permissions', 'on_custom_user_permissions');
      EventsManager::listen('on_user_type_changed', 'on_user_type_changed');
      EventsManager::listen('on_label_types', 'on_label_types');
      EventsManager::listen('on_user_cleanup', 'on_user_cleanup');
      
      EventsManager::listen(array(
        'on_get_completable_project_object_types', 
        'on_get_day_project_object_types', 
      ), 'register_milestone_type');
      
      EventsManager::listen('on_project_permissions', 'on_project_permissions');
      EventsManager::listen('on_object_inspector', 'on_object_inspector');
      EventsManager::listen('on_notification_inspector', 'on_notification_inspector');
            
      EventsManager::listen('on_quick_add', 'on_quick_add');
      EventsManager::listen('on_search_indices', 'on_search_indices');
      
      EventsManager::listen('on_rebuild_activity_log_actions', 'on_rebuild_activity_log_actions');
      EventsManager::listen('on_rebuild_object_contexts_actions', 'on_rebuild_object_contexts_actions');

      EventsManager::listen('on_notification_context_view_url', 'on_notification_context_view_url');
      EventsManager::listen('on_object_from_notification_context', 'on_object_from_notification_context');

      // update objects that have anonymous client saved to People section
      EventsManager::listen('on_client_saved', 'on_client_saved');

      EventsManager::listen('on_load_control_tower', 'on_load_control_tower');
      EventsManager::listen('on_load_control_tower_badge', 'on_load_control_tower_badge');

      EventsManager::listen('on_custom_field_disabled', 'on_custom_field_disabled');

      EventsManager::listen('on_extra_stats', 'on_extra_stats');

      EventsManager::listen('on_incoming_mail_interceptors', 'on_incoming_mail_interceptors');

      // Drop project progress cache
      // 
      // - state changes drop cache complete
      // - copy and move between projects clear cache on project object class 
      //   level, so no need for external cache clearning is needed
      EventsManager::listen(array(
        'on_object_inserted', 
        'on_object_completed', 
        'on_object_opened', 
      ), 'drop_project_progress_cache');

	    EventsManager::listen('on_used_disk_space', 'on_used_disk_space');

	    EventsManager::listen('on_calendar_groups', 'on_calendar_groups');
	    EventsManager::listen('on_calendar_events', 'on_calendar_events');

	    EventsManager::listen('on_calendar_share_types', 'on_calendar_share_types');

      // Morning paper related listeners
	    EventsManager::listen('on_available_scheduled_tasks', 'on_available_scheduled_tasks');
	    EventsManager::listen('on_handle_public_subscribe', 'on_handle_public_subscribe');
	    EventsManager::listen('on_handle_public_unsubscribe', 'on_handle_public_unsubscribe');
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
      return lang('System');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @return string
     */
    function getDescription() {
      return lang('activeCollab foundation');
    } // getDescription
    
  }