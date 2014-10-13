<?php

  // Include application specific module base
  require_once APPLICATION_PATH . '/resources/ActiveCollabProjectSectionModule.class.php';

  /**
   * Discussions module definition
   *
   * @package activeCollab.modules.discussions
   * @subpackage models
   */
  class DiscussionsModule extends ActiveCollabProjectSectionModule {
    
    /**
     * Plain module name
     *
     * @var string
     */
    protected $name = 'discussions';
    
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
    protected $project_object_classes = 'Discussion';

    /**
     * Name of category class used by this section
     *
     * @var string
     */
    protected $category_class = 'DiscussionCategory';
    
    // ---------------------------------------------------
    //  Events and Routes
    // ---------------------------------------------------
    
    /**
     * Define module routes
     */
    function defineRoutes() {
      Router::map('project_discussions', 'projects/:project_slug/discussions', array('controller' => 'discussions', 'action' => 'index'));
      Router::map('project_discussions_archive', 'projects/:project_slug/discussions/archive', array('controller' => 'discussions', 'action' => 'archive'));
      Router::map('project_discussions_mass_edit', 'projects/:project_slug/discussions/mass-edit', array('controller' => 'discussions', 'action' => 'mass_edit'));
      
      Router::map('project_discussions_add', 'projects/:project_slug/discussions/add', array('controller' => 'discussions', 'action' => 'add'));
      Router::map('project_discussions_export', 'projects/:project_slug/discussions/export', array('controller' => 'discussions', 'action' => 'export'));
       
      // Single discussion
      Router::map('project_discussion', 'projects/:project_slug/discussions/:discussion_id', array('controller' => 'discussions', 'action' => 'view'), array('discussion_id' => Router::MATCH_ID));
      Router::map('project_discussion_edit', 'projects/:project_slug/discussions/:discussion_id/edit', array('controller' => 'discussions', 'action' => 'edit'), array('discussion_id' => Router::MATCH_ID));
      
      Router::map('project_discussion_pin', 'projects/:project_slug/discussions/:discussion_id/pin', array('controller' => 'discussions', 'action' => 'pin'), array('discussion_id' => Router::MATCH_ID));
      Router::map('project_discussion_unpin', 'projects/:project_slug/discussions/:discussion_id/unpin', array('controller' => 'discussions', 'action' => 'unpin'), array('discussion_id' => Router::MATCH_ID));
      
      // Milestone discussions
      Router::map('milestone_discussions', 'projects/:project_slug/milestones/:milestone_id/discussions', array('controller' => 'milestone_discussions', 'action' => 'index'), array('milestone_id' => Router::MATCH_ID));
      
      AngieApplication::getModule('environment')->defineStateRoutesFor('project_discussion', 'projects/:project_slug/discussions/:discussion_id', 'discussions', DISCUSSIONS_MODULE, array('discussion_id' => Router::MATCH_ID));
      AngieApplication::getModule('categories')->defineCategoriesRoutesFor('project_discussion', 'projects/:project_slug/discussions', 'discussions', DISCUSSIONS_MODULE);
      AngieApplication::getModule('categories')->defineCategoryRoutesFor('project_discussion', 'projects/:project_slug/discussions', 'discussions', DISCUSSIONS_MODULE);
      AngieApplication::getModule('comments')->defineCommentsRoutesFor('project_discussion', 'projects/:project_slug/discussions/:discussion_id', 'discussions', DISCUSSIONS_MODULE, array('discussion_id' => Router::MATCH_ID));
      AngieApplication::getModule('subscriptions')->defineSubscriptionRoutesFor('project_discussion', 'projects/:project_slug/discussions/:discussion_id', 'discussions', DISCUSSIONS_MODULE, array('discussion_id' => Router::MATCH_ID));
      AngieApplication::getModule('reminders')->defineRemindersRoutesFor('project_discussion', 'projects/:project_slug/discussions/:discussion_id', 'discussions', DISCUSSIONS_MODULE, array('discussion_id' => Router::MATCH_ID));
			AngieApplication::getModule('attachments')->defineAttachmentsRoutesFor('project_discussion', 'projects/:project_slug/discussions/:discussion_id', 'discussions', DISCUSSIONS_MODULE, array('discussion_id' => Router::MATCH_ID));
			AngieApplication::getModule('system')->defineSharingRoutesFor('project_discussion', 'projects/:project_slug/discussions/:discussion_id', 'discussions', DISCUSSIONS_MODULE, array('discussion_id' => Router::MATCH_ID));
			AngieApplication::getModule('system')->defineMoveToProjectRoutesFor('project_discussion', 'projects/:project_slug/discussions/:discussion_id', 'discussions', DISCUSSIONS_MODULE, array('discussion_id' => Router::MATCH_ID));

	    // Project discussion footprints
	    if (AngieApplication::isModuleLoaded('footprints')) {
		    AngieApplication::getModule('footprints')->defineAccessLogRoutesFor('project_discussion', 'projects/:project_slug/discussions/:discussion_id', 'discussions', DISCUSSIONS_MODULE, array('discussion_id' => Router::MATCH_ID));
		    AngieApplication::getModule('footprints')->defineHistoryOfChangesRoutesFor('project_discussion', 'projects/:project_slug/discussions/:discussion_id', 'discussions', DISCUSSIONS_MODULE, array('discussion_id' => Router::MATCH_ID));
	    } // if
			
			Router::map('activity_logs_admin_rebuild_discussions', 'admin/indices/activity-logs/rebuild/discussions', array('controller' => 'activity_logs_admin', 'action' => 'rebuild_discussions'));
			Router::map('object_contexts_admin_rebuild_discussions', 'admin/indices/object-contexts/rebuild/discussions', array('controller' => 'object_contexts_admin', 'action' => 'rebuild_discussions'));
    } // defineRoutes
    
    /**
     * Define event handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_project_tabs', 'on_project_tabs');
      EventsManager::listen('on_available_project_tabs', 'on_available_project_tabs');
      EventsManager::listen('on_milestone_sections', 'on_milestone_sections');
      EventsManager::listen('on_master_categories', 'on_master_categories');
      EventsManager::listen('on_project_export', 'on_project_export');
      EventsManager::listen('on_user_cleanup', 'on_user_cleanup');
      EventsManager::listen('on_project_permissions', 'on_project_permissions');
      EventsManager::listen('on_mass_edit', 'on_mass_edit');
      EventsManager::listen('on_quick_add', 'on_quick_add');
      EventsManager::listen('on_build_project_search_index', 'on_build_project_search_index');
      EventsManager::listen('on_build_names_search_index_for_project', 'on_build_names_search_index_for_project');
      EventsManager::listen('on_homescreen_widget_types', 'on_homescreen_widget_types');
      EventsManager::listen('on_project_subcontext_permission', 'on_project_subcontext_permission');
      EventsManager::listen('on_rebuild_activity_log_actions', 'on_rebuild_activity_log_actions');
      EventsManager::listen('on_rebuild_object_contexts_actions', 'on_rebuild_object_contexts_actions');
      EventsManager::listen('on_object_from_notification_context', 'on_object_from_notification_context');
      EventsManager::listen('on_incoming_mail_actions', 'on_incoming_mail_actions');
	    EventsManager::listen('on_history_field_renderers', 'on_history_field_renderers');
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
      return lang('Discussions');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @return string
     */
    function getDescription() {
      return lang('Adds discussion boards to projects');
    } // getDescription
    
    /**
     * Return module uninstallation message
     *
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated. All discussions from all projects will be deleted');
    } // getUninstallMessage

    /**
     * Return object types (class names) that this module is working with
     *
     * @return array
     */
    function getObjectTypes() {
      return array('Discussion');
    } // getObjectTypes
    
  }