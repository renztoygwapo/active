<?php

  /**
   * Documents module definition
   *
   * @package activeCollab.modules.documents
   * @subpackage models
   */
  class DocumentsModule extends AngieModule {
    
    /**
     * Plain module name
     *
     * @var string
     */
    protected $name = 'documents';
    
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
      Router::map('documents', 'documents', array('controller' => 'documents', 'action' => 'index'));
      Router::map('documents_mass_edit', 'documents/mass-edit', array('controller' => 'documents', 'action' => 'mass_edit'));
      Router::map('documents_add_text', 'documents/add-text', array('controller' => 'documents', 'action' => 'add_text'));
      Router::map('documents_upload_file', 'documents/upload-file', array('controller' => 'documents', 'action' => 'upload_file'));
      Router::map('documents_archive', 'documents/archive', array('controller' => 'documents', 'action' => 'archive'));
      
      // Document categories
      AngieApplication::getModule('categories')->defineCategoriesRoutesFor('document', 'documents', 'documents', DOCUMENTS_MODULE, array('document_id' => Router::MATCH_ID));
      AngieApplication::getModule('categories')->defineCategoryRoutesFor('document', 'documents', 'documents', DOCUMENTS_MODULE, array('document_id' => Router::MATCH_ID));
      AngieApplication::getModule('environment')->defineStateRoutesFor('document', 'document/:document_id', 'documents', DOCUMENTS_MODULE, array('document_id' => Router::MATCH_ID));
      AngieApplication::getModule('attachments')->defineAttachmentsRoutesFor('document', 'document/:document_id', 'documents', DOCUMENTS_MODULE, array('document_id' => Router::MATCH_ID));
      AngieApplication::getModule('subscriptions')->defineSubscriptionRoutesFor('document', 'document/:document_id', 'documents', DOCUMENTS_MODULE, array('document_id' => Router::MATCH_ID));

	    // Document footprints
	    if (AngieApplication::isModuleLoaded('footprints')) {
		    AngieApplication::getModule('footprints')->defineHistoryOfChangesRoutesFor('document', 'document/:document_id', 'documents', DOCUMENTS_MODULE, array('document_id' => Router::MATCH_ID));
	    } // if

      // Single document
      Router::map('document', 'documents/:document_id', array('controller' => 'documents', 'action' => 'view'), array('category_id' => '\d+', 'document_id' => '\d+'));
      Router::map('document_download', 'documents/:document_id/download', array('controller' => 'documents', 'action' => 'download'), array('document_id' => '\d+'));
      Router::map('document_edit', 'documents/:document_id/edit', array('controller' => 'documents', 'action' => 'edit'), array('document_id' => '\d+'));
      Router::map('document_pin', 'documents/:document_id/pin', array('controller' => 'documents', 'action' => 'pin'), array('document_id' => '\d+'));
      Router::map('document_unpin', 'documents/:document_id/unpin', array('controller' => 'documents', 'action' => 'unpin'), array('document_id' => '\d+'));
      Router::map('document_delete', 'documents/:document_id/delete', array('controller' => 'documents', 'action' => 'delete'), array('document_id' => '\d+'));
      
      // Search index
      Router::map('documents_search_index_admin_build', 'admin/indices/search/documents/build', array('controller' => 'documents_search_index_admin', 'action' => 'build', 'search_index_name' => 'documents'));
      Router::map('document_names_search_index_admin_build', 'admin/indices/search/names/build/documents', array('controller' => 'document_names_search_index_admin', 'action' => 'build', 'search_index_name' => 'names'));
      
      Router::map('activity_logs_admin_rebuild_documents', 'admin/indices/activity-logs/rebuild/documents', array('controller' => 'activity_logs_admin', 'action' => 'rebuild_documents'));
      Router::map('object_contexts_admin_rebuild_documents', 'admin/indices/object-contexts/rebuild/documents', array('controller' => 'object_contexts_admin', 'action' => 'rebuild_documents'));
    } // defineRoutes
    
    /**
     * Define event handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_main_menu', 'on_main_menu');
      EventsManager::listen('on_visible_contexts', 'on_visible_contexts');
      EventsManager::listen('on_search_indices', 'on_search_indices');
      EventsManager::listen('on_rebuild_names_search_index_steps', 'on_rebuild_names_search_index_steps');
      EventsManager::listen('on_names_search_index_contexts', 'on_names_search_index_contexts');
      EventsManager::listen('on_custom_user_permissions', 'on_custom_user_permissions');
      EventsManager::listen('on_notification_inspector', 'on_notification_inspector');
      EventsManager::listen('on_rebuild_activity_log_actions', 'on_rebuild_activity_log_actions');
      EventsManager::listen('on_rebuild_object_contexts_actions', 'on_rebuild_object_contexts_actions');
      EventsManager::listen('on_trash_sections', 'on_trash_sections');
      EventsManager::listen('on_trash_map', 'on_trash_map');
      EventsManager::listen('on_empty_trash', 'on_empty_trash');
      EventsManager::listen('on_used_disk_space', 'on_used_disk_space');
    } // defineHandlers
    
    // ---------------------------------------------------
    //  Install
    // ---------------------------------------------------
    
    /**
     * Install this module
     * 
     * $bulk is true when this module is installed as part of a larger module 
     * installation call (like system installation)
     *
     * @param integer $position
     * @param boolean $bulk
     * @return boolean
     */
    function install($position = null, $bulk = false) {
      parent::install($position, $bulk);
      
      if(empty($bulk)) {
        $provider = Search::getProvider();
        
        if($provider instanceof SearchProvider) {
          require_once APPLICATION_PATH . '/modules/documents/models/search/DocumentsSearchIndex.class.php';
        
          $index = new DocumentsSearchIndex($provider);
          $index->initialize();
        } // if
      } // if
    } // install
    
    /**
     * Uninstall and clean up
     */
    function uninstall() {
      try {
        DB::beginWork('Uninstalling Documents module @ ' . __CLASS__);
        
        $file_names = DB::execute('SELECT body FROM ' . TABLE_PREFIX . 'documents WHERE type = ?', 'file');
      
        parent::uninstall();
        
        Categories::deleteByType('DocumentCategory');
        
        if($file_names) {
          foreach($file_names as $file_name) {
            if($file_name && is_file(UPLOAD_PATH . '/' . $file_name)) {
              @unlink(UPLOAD_PATH . '/' . $file_name);
            } // if
          } // foreach
        } // if

        FwApplicationObjects::cleanUpContextsByParentTypes(array('Document'));
        
        DB::commit('Documents module uninstalled @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to uninstall Documents module @ ' . __CLASS__);
        throw $e;
      } // try
      
      $index = Search::getIndex('documents');
    	
    	if($index instanceof DocumentsSearchIndex) {
    	  $index->tearDown();
    	} // if
    } // uninstall
    
    /**
     * Get module display name
     *
     * @return string
     */
    function getDisplayName() {
      return lang('Documents');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @return string
     */
    function getDescription() {
      return lang('Adds global document management system');
    } // getDescription
    
    /**
     * Return module uninstallation message
     *
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated. All data generated using it will be deleted');
    } // getUninstallMessage

    /**
     * Return object types (class names) that this module is working with
     *
     * @return array
     */
    function getObjectTypes() {
      return array('Document');
    } // getObjectTypes
    
  }