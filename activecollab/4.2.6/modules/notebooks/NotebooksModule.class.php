<?php

  // Include application specific module base
  require_once APPLICATION_PATH . '/resources/ActiveCollabProjectSectionModule.class.php';

  /**
   * Notebooks module definition
   *
   * @package activeCollab.modules.notebooks
   * @subpackage models
   */
  class NotebooksModule extends ActiveCollabProjectSectionModule {
    
    /**
     * Plain module name
     *
     * @var string
     */
    protected $name = 'notebooks';
    
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
    protected $project_object_classes = 'Notebook';
    
    // ---------------------------------------------------
    //  Events and Routes
    // ---------------------------------------------------
    
    /**
     * Define module routes
     */
    function defineRoutes() {
      Router::map('project_notebooks', 'projects/:project_slug/notebooks', array('controller' => 'notebooks', 'action' => 'index'));
      Router::map('project_notebooks_archive', 'projects/:project_slug/notebooks/archive', array('controller' => 'notebooks', 'action' => 'archive'));
      Router::map('project_notebooks_add', 'projects/:project_slug/notebooks/add', array('controller' => 'notebooks', 'action' => 'add'));
      Router::map('project_notebooks_reorder', 'projects/:project_slug/notebooks/add/reorder', array('controller' => 'notebooks', 'action' => 'reorder'));
      
      Router::map('project_notebook', 'projects/:project_slug/notebooks/:notebook_id', array('controller' => 'notebooks', 'action' => 'view'), array('notebook_id' => '\d+'));
      Router::map('project_notebook_mass_edit', 'projects/:project_slug/notebooks/:notebook_id/mass-edit', array('controller' => 'notebook_pages', 'action' => 'mass_edit'), array('notebook_id' => '\d+'));
      Router::map('project_notebook_edit', 'projects/:project_slug/notebooks/:notebook_id/edit', array('controller' => 'notebooks', 'action' => 'edit'), array('notebook_id' => '\d+'));
      
      AngieApplication::getModule('environment')->defineStateRoutesFor('project_notebook', 'projects/:project_slug/notebooks/:notebook_id', 'notebooks', NOTEBOOKS_MODULE, array('notebook_id' => '\d+'));
      AngieApplication::getModule('avatar')->defineAvatarRoutesFor('project_notebook', 'projects/:project_slug/notebooks/:notebook_id', 'notebooks', NOTEBOOKS_MODULE, array('notebook_id' => '\d+'));
      AngieApplication::getModule('subscriptions')->defineSubscriptionRoutesFor('project_notebook', 'projects/:project_slug/notebooks/:notebook_id', 'notebooks', NOTEBOOKS_MODULE, array('notebook_id' => '\d+'));
      AngieApplication::getModule('attachments')->defineAttachmentsRoutesFor('project_notebook', 'projects/:project_slug/notebooks/:notebook_id', 'notebooks', NOTEBOOKS_MODULE, array('notebook_id' => '\d+'));
      AngieApplication::getModule('reminders')->defineRemindersRoutesFor('project_notebook', 'projects/:project_slug/notebooks/:notebook_id', 'notebooks', NOTEBOOKS_MODULE, array('notebook_id' => '\d+'));
      AngieApplication::getModule('system')->defineMoveToProjectRoutesFor('project_notebook', 'projects/:project_slug/notebooks/:notebook_id', 'notebooks', NOTEBOOKS_MODULE, array('notebook_id' => '\d+'));
      AngieApplication::getModule('system')->defineSharingRoutesFor('project_notebook', 'projects/:project_slug/notebooks/:notebook_id', 'notebooks', NOTEBOOKS_MODULE, array('notebook_id' => Router::MATCH_ID));

	    // Project notebbok footprints
	    if (AngieApplication::isModuleLoaded('footprints')) {
		    AngieApplication::getModule('footprints')->defineAccessLogRoutesFor('project_notebook', 'projects/:project_slug/notebooks/:notebook_id', 'notebooks', NOTEBOOKS_MODULE, array('notebook_id' => Router::MATCH_ID));
		    AngieApplication::getModule('footprints')->defineHistoryOfChangesRoutesFor('project_notebook', 'projects/:project_slug/notebooks/:notebook_id', 'notebooks', NOTEBOOKS_MODULE, array('notebook_id' => Router::MATCH_ID));
	    } // if

			Router::map('project_notebook_pages_archive', 'projects/:project_slug/notebooks/:notebook_id/pages/archive', array('controller' => 'notebook_pages', 'action' => 'archive'), array('notebook_id' => '\d+'));
      Router::map('project_notebook_pages_add', 'projects/:project_slug/notebooks/:notebook_id/pages/add', array('controller' => 'notebook_pages', 'action' => 'add'), array('notebook_id' => '\d+'));
      Router::map('project_notebook_pages_reorder', 'projects/:project_slug/notebooks/:notebook_id/pages/reorder', array('controller' => 'notebook_pages', 'action' => 'reorder'), array('notebook_id' => '\d+'));
      
      Router::map('project_notebook_page', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id', array('controller' => 'notebook_pages', 'action' => 'view'), array('notebook_id' => '\d+', 'notebook_page_id' => '\d+'));
      Router::map('project_notebook_page_edit', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/edit', array('controller' => 'notebook_pages', 'action' => 'edit'), array('notebook_id' => '\d+', 'notebook_page_id' => '\d+'));
      Router::map('project_notebook_page_revert', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/revert/:to', array('controller' => 'notebook_pages', 'action' => 'revert'), array('notebook_id' => '\d+', 'notebook_page_id' => '\d+'));
      Router::map('project_notebook_page_compare_versions', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/compare-versions', array('controller' => 'notebook_pages', 'action' => 'compare_versions'), array('notebook_id' => '\d+', 'notebook_page_id' => '\d+'));
      Router::map('project_notebook_page_lock', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/lock', array('controller' => 'notebook_pages', 'action' => 'lock'), array('notebook_id' => '\d+', 'notebook_page_id' => '\d+'));
      Router::map('project_notebook_page_unlock', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/unlock', array('controller' => 'notebook_pages', 'action' => 'unlock'), array('notebook_id' => '\d+', 'notebook_page_id' => '\d+'));
      Router::map('project_notebook_page_move', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/move', array('controller' => 'notebook_pages', 'action' => 'move'), array('notebook_id' => '\d+', 'notebook_page_id' => '\d+'));
      
      AngieApplication::getModule('environment')->defineStateRoutesFor('project_notebook_page', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id', 'notebook_pages', NOTEBOOKS_MODULE, array('notebook_id' => '\d+', 'notebook_page_id' => '\d+'));
      AngieApplication::getModule('comments')->defineCommentsRoutesFor('project_notebook_page', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id', 'notebook_pages', NOTEBOOKS_MODULE, array('notebook_id' => '\d+', 'notebook_page_id' => '\d+'));
      AngieApplication::getModule('subscriptions')->defineSubscriptionRoutesFor('project_notebook_page', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id', 'notebook_pages', NOTEBOOKS_MODULE, array('notebook_id' => '\d+', 'notebook_page_id' => '\d+'));
      AngieApplication::getModule('attachments')->defineAttachmentsRoutesFor('project_notebook_page', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id', 'notebook_pages', NOTEBOOKS_MODULE, array('notebook_id' => '\d+', 'notebook_page_id' => '\d+'));

	    // Project notebook page footprints
	    if (AngieApplication::isModuleLoaded('footprints')) {
		    AngieApplication::getModule('footprints')->defineAccessLogRoutesFor('project_notebook_page', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id', 'notebook_pages', NOTEBOOKS_MODULE, array('notebook_id' => '\d+', 'notebook_page_id' => '\d+'));
		    AngieApplication::getModule('footprints')->defineHistoryOfChangesRoutesFor('project_notebook_page', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id', 'notebook_pages', NOTEBOOKS_MODULE, array('notebook_id' => '\d+', 'notebook_page_id' => '\d+'));
	    } // if

      Router::map('project_notebook_page_version', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/versions/:version', array('controller' => 'notebook_page_versions', 'action' => 'view'), array('notebook_id' => '\d+', 'notebook_page_id' => '\d+', 'version' => '\d+'));
      Router::map('project_notebook_page_version_delete', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/versions/:version/delete', array('controller' => 'notebook_page_versions', 'action' => 'delete'), array('notebook_id' => '\d+', 'notebook_page_id' => '\d+', 'version' => '\d+'));

      Router::map('activity_logs_admin_rebuild_notebooks', 'admin/indices/activity-logs/rebuild/notebooks', array('controller' => 'activity_logs_admin', 'action' => 'rebuild_notebooks'));
      Router::map('activity_logs_admin_rebuild_notbook_pages', 'admin/indices/activity-logs/rebuild/notebook-page-versions', array('controller' => 'activity_logs_admin', 'action' => 'rebuild_notbook_pages'));
      
      Router::map('object_contexts_admin_rebuild_notebooks', 'admin/indices/object-contexts/rebuild/notebooks', array('controller' => 'object_contexts_admin', 'action' => 'rebuild_notebooks'));

      // Shared object
      Router::map('shared_notebook', 's/notebook/:sharing_code', array('controller' => 'notebooks_frontend', 'action' => 'default_view_shared_object'), array('sharing_code' => Router::MATCH_WORD));
      Router::map('shared_notebook_page', 's/notebook/:sharing_code/page/:notebook_page_id', array('controller' => 'notebooks_frontend', 'action' => 'notebook_page'), array('sharing_code' => Router::MATCH_WORD, 'notebook_page_id' => Router::MATCH_ID));

      // Milestone notebooks
      Router::map('milestone_notebooks', 'projects/:project_slug/milestones/:milestone_id/notebooks', array('controller' => 'milestone_notebooks', 'action' => 'index'), array('milestone_id' => Router::MATCH_ID));

    } // defineRoutes
    
    /**
     * Define event handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_project_tabs', 'on_project_tabs');
      EventsManager::listen('on_available_project_tabs', 'on_available_project_tabs');
      EventsManager::listen('on_new_revision', 'on_new_revision');
      EventsManager::listen('on_project_export', 'on_project_export');
      EventsManager::listen('on_project_permissions', 'on_project_permissions');
      EventsManager::listen('on_object_inspector', 'on_object_inspector');
      EventsManager::listen('on_quick_add', 'on_quick_add');
      EventsManager::listen('on_build_project_search_index', 'on_build_project_search_index');
      EventsManager::listen('on_build_names_search_index_for_project', 'on_build_names_search_index_for_project');
      EventsManager::listen('on_project_subcontext_permission', 'on_project_subcontext_permission');
      EventsManager::listen('on_rebuild_activity_log_actions', 'on_rebuild_activity_log_actions');
      EventsManager::listen('on_activity_log_callbacks', 'on_activity_log_callbacks');
      EventsManager::listen('on_rebuild_object_contexts_actions', 'on_rebuild_object_contexts_actions');
      EventsManager::listen('on_object_from_notification_context', 'on_object_from_notification_context');
      EventsManager::listen('on_trash_map', 'on_trash_map');
      EventsManager::listen('on_trash_sections', 'on_trash_sections');
      EventsManager::listen('on_empty_trash', 'on_empty_trash');
      EventsManager::listen('on_milestone_sections', 'on_milestone_sections');
      EventsManager::listen('on_notification_inspector', 'on_notification_inspector');
      EventsManager::listen('on_extend_project_items_type_id_map', 'on_extend_project_items_type_id_map');
      EventsManager::listen('on_extra_stats', 'on_extra_stats');
    } // defineHandlers

    /**
     * Uninstall the module
     */
    function uninstall() {
      parent::uninstall();

      ActivityLogs::deleteByParentTypes('NotebookPage');
      Comments::deleteByParentTypes('NotebookPage');
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
      return lang('Notebooks');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @return string
     */
    function getDescription() {
      return lang('Adds collaborative writing tool to projects');
    } // getDescription
    
    /**
     * Return module un-installation message
     *
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated. All notebooks from all projects will be deleted');
    } // getUninstallMessage

    /**
     * Return object types (class names) that this module is working with
     *
     * @return array
     */
    function getObjectTypes() {
      return array('Notebook', 'NotebookPage');
    } // getObjectTypes
    
  }