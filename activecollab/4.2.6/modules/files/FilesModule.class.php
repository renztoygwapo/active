<?php

  // Include application specific module base
  require_once APPLICATION_PATH . '/resources/ActiveCollabProjectSectionModule.class.php';

  /**
   * Files module definition
   *
   * @package activeCollab.modules.files
   */
  class FilesModule extends ActiveCollabProjectSectionModule {
    
    /**
     * Short module name
     *
     * @var string
     */
    protected $name = 'files';
    
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
    protected $project_object_classes = array('File', 'TextDocument');

    /**
     * Name of category class used by this section
     *
     * @var string
     */
    protected $category_class = 'AssetCategory';
    
    /**
     * Define module routes
     */
    function defineRoutes() {
      Router::map('project_assets', 'projects/:project_slug/files', array('controller' => 'assets', 'action' => 'index'));
      Router::map('project_assets_archive', 'projects/:project_slug/files/archive', array('controller' => 'assets', 'action' => 'archive'));
      Router::map('project_assets_mass_edit', 'projects/:project_slug/files/mass-edit', array('controller' => 'assets', 'action' => 'mass_edit'));
      
      AngieApplication::getModule('categories')->defineCategoriesRoutesFor('project_asset', 'projects/:project_slug/files', 'assets', FILES_MODULE);
      AngieApplication::getModule('categories')->defineCategoryRoutesFor('project_asset', 'projects/:project_slug/files', 'assets', FILES_MODULE);
      
			// Files
			Router::map('project_assets_files', 'projects/:project_slug/files/files', array('controller' => 'files', 'action' => 'index'));
			Router::map('project_assets_files_archive', 'projects/:project_slug/files/files/archive', array('controller' => 'files', 'action' => 'archive'));
			Router::map('project_assets_files_add', 'projects/:project_slug/files/files/add', array('controller' => 'files', 'action' => 'add'), array('asset_id' => Router::MATCH_ID));
			Router::map('project_assets_files_upload_single', 'projects/:project_slug/files/files/upload', array('controller' => 'files', 'action' => 'upload'));
			
			Router::map('project_assets_file', 'projects/:project_slug/files/files/:asset_id', array('controller' => 'files', 'action' => 'view'), array('asset_id' => Router::MATCH_ID));
			Router::map('project_assets_file_upload_compatibility', 'projects/:project_slug/files/files/upload-compatibility', array('controller' => 'files', 'action' => 'upload_compatibility'), array('asset_id' => Router::MATCH_ID));
      Router::map('project_assets_file_edit', 'projects/:project_slug/files/files/:asset_id/edit', array('controller' => 'files', 'action' => 'edit'), array('asset_id' => Router::MATCH_ID));
      Router::map('project_assets_file_preview', 'projects/:project_slug/files/files/:asset_id/preview', array('controller' => 'files', 'action' => 'preview'), array('asset_id' => Router::MATCH_ID));
      
      AngieApplication::getModule('environment')->defineStateRoutesFor('project_assets_file', 'projects/:project_slug/files/:asset_id', 'files', FILES_MODULE, array('asset_id' => Router::MATCH_ID));
      AngieApplication::getModule('comments')->defineCommentsRoutesFor('project_assets_file', 'projects/:project_slug/files/:asset_id', 'files', FILES_MODULE, array('asset_id' => Router::MATCH_ID));
      AngieApplication::getModule('subscriptions')->defineSubscriptionRoutesFor('project_assets_file', 'projects/:project_slug/files/:asset_id', 'files', FILES_MODULE, array('asset_id' => Router::MATCH_ID));
      AngieApplication::getModule('reminders')->defineRemindersRoutesFor('project_assets_file', 'projects/:project_slug/files/:asset_id', 'files', FILES_MODULE, array('asset_id' => Router::MATCH_ID));
      AngieApplication::getModule('attachments')->defineAttachmentsRoutesFor('project_assets_file', 'projects/:project_slug/files/:asset_id', 'files', FILES_MODULE, array('asset_id' => Router::MATCH_ID));
      AngieApplication::getModule('system')->defineMoveToProjectRoutesFor('project_assets_file', 'projects/:project_slug/files/:asset_id', 'files', FILES_MODULE, array('asset_id' => Router::MATCH_ID));

	    // Project assets file footprints
	    if (AngieApplication::isModuleLoaded('footprints')) {
		    AngieApplication::getModule('footprints')->defineAccessLogRoutesFor('project_assets_file', 'projects/:project_slug/files/:asset_id', 'files', FILES_MODULE, array('asset_id' => Router::MATCH_ID));
		    AngieApplication::getModule('footprints')->defineHistoryOfChangesRoutesFor('project_assets_file', 'projects/:project_slug/files/:asset_id', 'files', FILES_MODULE, array('asset_id' => Router::MATCH_ID));
	    } // if

      // sharing
      AngieApplication::getModule('system')->defineSharingRoutesFor('project_assets_file', 'projects/:project_slug/files/:asset_id', 'files', FILES_MODULE, array('asset_id' => Router::MATCH_ID));
      Router::map('shared_file_download', 's/file/:sharing_code/download', array('controller' => 'files_frontend', 'action' => 'download'), array('sharing_code' => Router::MATCH_WORD));
      
      Router::map('project_assets_file_download', 'projects/:project_slug/files/files/:asset_id/download', array('controller' => 'files', 'action' => 'download'), array('asset_id' => Router::MATCH_ID));
      Router::map('project_assets_file_refresh_details', 'projects/:project_slug/files/files/:asset_id/refresh-details', array('controller' => 'files', 'action' => 'refresh_details'), array('asset_id' => Router::MATCH_ID));
      Router::map('project_assets_file_versions_add', 'projects/:project_slug/files/files/:asset_id/versions/add', array('controller' => 'file_versions', 'action' => 'add'), array('asset_id' => Router::MATCH_ID));

      Router::map('project_assets_file_version', 'projects/:project_slug/files/files/:asset_id/versions/:file_version_num', array('controller' => 'file_versions', 'action' => 'view'), array('asset_id' => Router::MATCH_ID, 'file_version_num' => Router::MATCH_ID));
      Router::map('project_assets_file_version_download', 'projects/:project_slug/files/files/:asset_id/versions/:file_version_num/download', array('controller' => 'file_versions', 'action' => 'download'), array('asset_id' => Router::MATCH_ID, 'file_version_num' => Router::MATCH_ID));
      Router::map('project_assets_file_version_delete', 'projects/:project_slug/files/files/:asset_id/versions/:file_version_num/delete', array('controller' => 'file_versions', 'action' => 'delete'), array('asset_id' => Router::MATCH_ID, 'file_version_num' => Router::MATCH_ID));

      // Text Documents
      Router::map('project_assets_text_documents', 'projects/:project_slug/files/text-documents', array('controller' => 'text_documents', 'action' => 'index'));
      Router::map('project_assets_text_documents_archive', 'projects/:project_slug/files/text-documents/archive', array('controller' => 'text_documents', 'action' => 'archive'));
      
      Router::map('project_assets_text_document', 'projects/:project_slug/files/text-documents/:asset_id', array('controller' => 'text_documents', 'action' => 'view'), array('asset_id' => Router::MATCH_ID));
      Router::map('project_assets_text_document_add', 'projects/:project_slug/files/text-documents/add', array('controller' => 'text_documents', 'action' => 'add'));
      Router::map('project_assets_text_document_edit', 'projects/:project_slug/files/text-documents/:asset_id/edit', array('controller' => 'text_documents', 'action' => 'edit'), array('asset_id' => Router::MATCH_ID));
      AngieApplication::getModule('environment')->defineStateRoutesFor('project_assets_text_document', 'projects/:project_slug/files/text-documents/:asset_id', 'text_documents', FILES_MODULE, array('asset_id' => Router::MATCH_ID));
      AngieApplication::getModule('comments')->defineCommentsRoutesFor('project_assets_text_document', 'projects/:project_slug/files/text-documents/:asset_id', 'text_documents', FILES_MODULE, array('asset_id' => Router::MATCH_ID));
      AngieApplication::getModule('subscriptions')->defineSubscriptionRoutesFor('project_assets_text_document', 'projects/:project_slug/files/text-documents/:asset_id', 'text_documents', FILES_MODULE, array('asset_id' => Router::MATCH_ID));
      AngieApplication::getModule('reminders')->defineRemindersRoutesFor('project_assets_text_document', 'projects/:project_slug/files/text-documents/:asset_id', 'text_documents', FILES_MODULE, array('asset_id' => Router::MATCH_ID));
      AngieApplication::getModule('attachments')->defineAttachmentsRoutesFor('project_assets_text_document', 'projects/:project_slug/files/text-documents/:asset_id', 'text_documents', FILES_MODULE, array('asset_id' => Router::MATCH_ID));
      AngieApplication::getModule('system')->defineMoveToProjectRoutesFor('project_assets_text_document', 'projects/:project_slug/files/text-documents/:asset_id', 'text_documents', FILES_MODULE, array('asset_id' => Router::MATCH_ID));
      AngieApplication::getModule('system')->defineSharingRoutesFor('project_assets_text_document', 'projects/:project_slug/files/text-documents/:asset_id', 'text_documents', FILES_MODULE, array('asset_id' => Router::MATCH_ID));

	    // Project assets text document footprints
	    if (AngieApplication::isModuleLoaded('footprints')) {
		    AngieApplication::getModule('footprints')->defineAccessLogRoutesFor('project_assets_text_document', 'projects/:project_slug/files/text-documents/:asset_id', 'text_documents', FILES_MODULE, array('asset_id' => Router::MATCH_ID));
		    AngieApplication::getModule('footprints')->defineHistoryOfChangesRoutesFor('project_assets_text_document', 'projects/:project_slug/files/text-documents/:asset_id', 'text_documents', FILES_MODULE, array('asset_id' => Router::MATCH_ID));
	    } // if

      Router::map('project_assets_text_document_version_revert', 'projects/:project_slug/files/text-documents/:asset_id/revert', array('controller' => 'text_documents', 'action' => 'revert'), array('asset_id' => Router::MATCH_ID));
      Router::map('project_assets_text_document_compare_versions', 'projects/:project_slug/files/text-documents/:asset_id/compare-versions', array('controller' => 'text_documents', 'action' => 'compare_versions'), array('asset_id' => Router::MATCH_ID));
      Router::map('project_assets_text_document_version', 'projects/:project_slug/files/text-documents/:asset_id/versions/:version_num', array('controller' => 'text_document_versions', 'action' => 'view'), array('asset_id' => Router::MATCH_ID, 'version_num' => Router::MATCH_ID));
      Router::map('project_assets_text_document_version_delete', 'projects/:project_slug/files/text-documents/:asset_id/versions/:version_num/delete', array('controller' => 'text_document_versions', 'action' => 'delete'), array('asset_id' => Router::MATCH_ID, 'version_num' => Router::MATCH_ID));

      // Activity log rebuilding
      Router::map('activity_logs_admin_rebuild_files', 'admin/indices/activity-logs/rebuild/files', array('controller' => 'activity_logs_admin', 'action' => 'rebuild_files'));
      Router::map('activity_logs_admin_rebuild_file_versions', 'admin/indices/activity-logs/rebuild/file-versions', array('controller' => 'activity_logs_admin', 'action' => 'rebuild_file_versions'));
      Router::map('activity_logs_admin_rebuild_text_document_versions', 'admin/indices/activity-logs/rebuild/text-document-versions', array('controller' => 'activity_logs_admin', 'action' => 'rebuild_text_document_versions'));
      
      Router::map('object_contexts_admin_rebuild_files', 'admin/indices/object-contexts/rebuild/files', array('controller' => 'object_contexts_admin', 'action' => 'rebuild_files'));

      // Milestone files
      Router::map('milestone_files', 'projects/:project_slug/milestones/:milestone_id/files', array('controller' => 'milestone_files', 'action' => 'index'), array('milestone_id' => Router::MATCH_ID));
    } // defineRoutes
    
    /**
     * Define event handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_master_categories', 'on_master_categories');
      EventsManager::listen('on_project_permissions', 'on_project_permissions');
      EventsManager::listen('on_project_tabs', 'on_project_tabs');
      EventsManager::listen('on_available_project_tabs', 'on_available_project_tabs');
			EventsManager::listen('on_object_inspector', 'on_object_inspector');
      EventsManager::listen('on_quick_add', 'on_quick_add');
      EventsManager::listen('on_build_project_search_index', 'on_build_project_search_index');
      EventsManager::listen('on_build_names_search_index_for_project', 'on_build_names_search_index_for_project');
      EventsManager::listen('on_project_subcontext_permission', 'on_project_subcontext_permission');
      EventsManager::listen('on_rebuild_activity_log_actions', 'on_rebuild_activity_log_actions');
      EventsManager::listen('on_activity_log_callbacks', 'on_activity_log_callbacks');
      EventsManager::listen('on_rebuild_object_contexts_actions', 'on_rebuild_object_contexts_actions');
      EventsManager::listen('on_object_from_notification_context', 'on_object_from_notification_context');
      EventsManager::listen('on_milestone_sections', 'on_milestone_sections');
      EventsManager::listen('on_project_export', 'on_project_export');
      EventsManager::listen('on_used_disk_space', 'on_used_disk_space');
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
      return lang('Files');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @return string
     */
    function getDescription() {
      return lang('Adds files section to projects. Files section can be used to upload files, add text documents, links and more');
    } // getDescription
    
    /**
     * Return module uninstallation message
     *
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated. All files from all projects will be deleted');
    } // getUninstallMessage

    /**
     * Return object types (class names) that this module is working with
     *
     * @return array
     */
    function getObjectTypes() {
      return array('File', 'TextDocument');
    } // getObjectTypes
    
  }