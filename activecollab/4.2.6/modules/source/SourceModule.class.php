<?php

  // Include applicaiton specific module base
  require_once APPLICATION_PATH . '/resources/ActiveCollabProjectSectionModule.class.php';

  /**
   * Source module definition
   *
   * @package activeCollab.modules.source
   * @subpackage models
   */
  class SourceModule extends ActiveCollabProjectSectionModule {
    
    /**
     * Plain module name
     *
     * @var string
     */
    protected $name = 'source';
    
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
    protected $project_object_classes = 'ProjectSourceRepository';
    
    // ---------------------------------------------------
    //  Events and Routes
    // ---------------------------------------------------
    
    /**
     * Define module routes
     */
    function defineRoutes() {

      // Home
      Router::map('project_repositories', '/projects/:project_slug/repositories', array('controller'=>'repository', 'action'=>'index'));
      
      // Repositories
      Router::map('repository_add_existing', '/projects/:project_slug/repositories/add-existing', array('controller'=>'repository', 'action'=>'add_existing'));
      Router::map('repository_add_new', '/projects/:project_slug/repositories/add-new', array('controller'=>'repository', 'action'=>'add_new'));
      Router::map('repository_edit', '/projects/:project_slug/repositories/:project_source_repository_id/edit', array('controller'=>'repository', 'action'=>'edit'), array('project_source_repository_id'=>Router::MATCH_ID));
      Router::map('repository_remove_from_project', '/projects/:project_slug/repositories/:project_source_repository_id/remove_from_project', array('controller'=>'repository', 'action'=>'remove_from_project'), array('project_source_repository_id'=>Router::MATCH_ID));
      Router::map('repository_test_connection', '/projects/:project_slug/repositories/test_connection', array('controller'=>'repository', 'action'=>'test_repository_connection'));
      
      Router::map('repository_history', '/projects/:project_slug/repositories/:project_source_repository_id', array('controller'=>'repository', 'action'=>'history'), array('project_source_repository_id'=>Router::MATCH_ID));
      Router::map('repository_history_show_thirty_more', '/projects/:project_slug/repositories/:project_source_repository_id/history_show_thirty_more', array('controller'=>'repository', 'action'=>'history_show_thirty_more'), array('project_source_repository_id'=>Router::MATCH_ID));
      Router::map('repository_one_commit_info','/projects/:project_slug/repositories/:project_source_repository_id/revision/:r/one_commit_info',array('controller'=>'repository', 'action'=>'one_commit_info'), array('project_source_repository_id'=>Router::MATCH_ID, 'r'=>Router::MATCH_ID));
      Router::map('repository_update', '/projects/:project_slug/repositories/:project_source_repository_id/update', array('controller'=>'repository', 'action'=>'update'), array('project_source_repository_id'=>Router::MATCH_ID));
      Router::map('repository_browse', '/projects/:project_slug/repositories/:project_source_repository_id/browse', array('controller'=>'repository', 'action'=>'browse'), array('project_source_repository_id'=>Router::MATCH_ID));
      Router::map('repository_browse_toggle','/projects/:project_slug/repositories/:project_source_repository_id/browse_toggle', array('controller'=>'repository', 'action'=>'browse_toggle'),array('project_source_repository_id'=>Router::MATCH_ID));
      Router::map('repository_browse_change_revision','/projects/:project_slug/repositories/:project_source_repository_id/browse_change_revision', array('controller'=>'repository', 'action'=>'find_revision_number'),array('project_source_repository_id'=>Router::MATCH_ID));
      Router::map('repository_compare', '/projects/:project_slug/repositories/:project_source_repository_id/compare', array('controller'=>'repository', 'action'=>'compare'), array('project_source_repository_id'=>Router::MATCH_ID));
      Router::map('repository_dialog_form_compare', '/projects/:project_slug/repositories/:project_source_repository_id/compare_form_dialog', array('controller'=>'repository', 'action'=>'compare_dialog_form'), array('project_source_repository_id'=>Router::MATCH_ID));
      Router::map('repository_change_branch', '/projects/:project_slug/repositories/:project_source_repository_id/change_branch', array('controller'=>'repository', 'action'=>'change_branch'), array('project_source_repository_id'=>Router::MATCH_ID));
      Router::map('repository_do_change_branch', '/projects/:project_slug/repositories/:project_source_repository_id/do_change_branch', array('controller'=>'repository', 'action'=>'do_change_branch'), array('project_source_repository_id'=>Router::MATCH_ID));

      // fix it???
      Router::map('project_project_source_repository', '/projects/:project_slug/repositories/:project_source_repository_id', array('controller'=>'repository', 'action'=>'history'), array('project_source_repository_id'=>Router::MATCH_ID));

      AngieApplication::getModule('environment')->defineStateRoutesFor('project_source_repository', '/projects/:project_slug/repositories/:project_source_repository_id', 'repository', SOURCE_MODULE, array('project_source_repository_id' => Router::MATCH_ID));
      AngieApplication::getModule('subscriptions')->defineSubscriptionRoutesFor('project_source_repository', '/projects/:project_slug/repositories/:project_source_repository_id', 'repository', SOURCE_MODULE, array('project_source_repository_id' => Router::MATCH_ID));
      AngieApplication::getModule('reminders')->defineRemindersRoutesFor('project_source_repository', '/projects/:project_slug/repositories/:project_source_repository_id', 'repository', SOURCE_MODULE, array('project_source_repository_id' => Router::MATCH_ID));
      AngieApplication::getModule('system')->defineMoveToProjectRoutesFor('project_source_repository', '/projects/:project_slug/repositories/:project_source_repository_id', 'repository', SOURCE_MODULE, array('project_source_repository_id' => Router::MATCH_ID));

      // Project repository footprints
      if (AngieApplication::isModuleLoaded('footprints')) {
        AngieApplication::getModule('footprints')->defineAccessLogRoutesFor('project_source_repository', 'projects/:project_slug/repositories/:project_source_repository_id', 'repository', SOURCE_MODULE, array('project_source_repository_id' => Router::MATCH_ID));
        AngieApplication::getModule('footprints')->defineHistoryOfChangesRoutesFor('project_source_repository', 'projects/:project_slug/repositories/:project_source_repository_id', 'repository', SOURCE_MODULE, array('project_source_repository_id' => Router::MATCH_ID));
      } // if
      
      Router::map('repository_commit', '/projects/:project_slug/repositories/:project_source_repository_id/revision/:r', array('controller'=>'repository', 'action'=>'commit'), array('project_source_repository_id'=>Router::MATCH_ID, 'r'=>Router::MATCH_ID));
      Router::map('repository_commit_paths', '/projects/:project_slug/repositories/:project_source_repository_id/revision/:r/paths', array('controller'=>'repository', 'action'=>'commit_paths'), array('project_source_repository_id'=>Router::MATCH_ID, 'r'=>Router::MATCH_ID));
      
      Router::map('repository_item_info', '/projects/:project_slug/repositories/:project_source_repository_id/info', array('controller'=>'repository', 'action'=>'info'), array('project_source_repository_id'=>Router::MATCH_ID));
      Router::map('repository_file_download', '/projects/:project_slug/repositories/:project_source_repository_id/file_download', array('controller'=>'repository', 'action'=>'file_download'), array('project_source_repository_id'=>Router::MATCH_ID));
      Router::map('repository_project_object_commits', '/projects/:project_slug/project-object-commits/:object_id', array('controller'=>'repository', 'action'=>'project_object_commits'),  array('object_id'=>Router::MATCH_ID));
      
      // Admin
      Router::map('admin_source', '/admin/tools/source', array('controller'=>'source_admin', 'action'=>'index'));
      
      Router::map('admin_source_settings', '/admin/tools/source/source_settings', array('controller'=>'source_admin', 'action'=>'settings'));
      
      Router::map('repository_users', '/source/repositories/:source_repository_id/users', array('controller' => 'repository_users', 'action' => 'index'), array('source_repository_id' => Router::MATCH_ID));
      Router::map('repository_user_add', '/source/repositories/:source_repository_id/users/add', array('controller' => 'repository_users', 'action' => 'add'), array('source_repository_id' => Router::MATCH_ID));
      Router::map('repository_user_delete', '/source/repositories/:source_repository_id/users/delete', array('controller' => 'repository_users', 'action' => 'delete'), array('source_repository_id' => Router::MATCH_ID));


      // SVN
      Router::map('admin_source_svn_settings', '/admin/tools/source/svn-settings', array('controller' => 'svn_source_admin', 'action' => 'settings'));
      Router::map('admin_source_svn_repositories', '/admin/tools/source/svn-repositories', array('controller' => 'svn_source_admin', 'action' => 'index'));
      Router::map('admin_source_svn_repositories_add', '/admin/tools/source/svn-repositories/add', array('controller' => 'svn_source_admin', 'action' => 'add'));
      Router::map('admin_source_svn_repository_test_connection', '/admin/tools/source/svn-repositories/test_connection', array('controller'=>'svn_source_admin', 'action'=>'test_repository_connection'));
      Router::map('admin_source_svn_test', '/admin/tools/source/svn-repositories/test', array('controller'=>'svn_source_admin', 'action'=>'test_svn'));
      
      Router::map('admin_source_svn_repository', '/admin/tools/source/svn-repositories/:source_repository_id', array('controller' => 'svn_source_admin', 'action' => 'view'), array('source_repository_id'=>Router::MATCH_ID));
      Router::map('admin_source_svn_repository_edit', '/admin/tools/source/svn-repositories/:source_repository_id/edit', array('controller' => 'svn_source_admin', 'action' => 'edit'), array('source_repository_id'=>Router::MATCH_ID));
      Router::map('admin_source_svn_repository_delete', '/admin/tools/source/svn-repositories/:source_repository_id/delete', array('controller' => 'svn_source_admin', 'action' => 'delete'), array('source_repository_id'=>Router::MATCH_ID));
      Router::map('admin_source_svn_repository_usage', '/admin/tools/source/svn-repositories/:source_repository_id/usage', array('controller'=>'svn_source_admin', 'action'=>'usage'), array('source_repository_id'=>Router::MATCH_ID));
      
      
      // GIT
      Router::map('admin_source_git_repositories', '/admin/tools/source/git-repositories', array('controller' => 'git_source_admin', 'action' => 'index'));
      Router::map('admin_source_git_repositories_add', '/admin/tools/source/git-repositories/add', array('controller' => 'git_source_admin', 'action' => 'add'));
      Router::map('admin_source_git_repository_test_connection', '/admin/tools/source/git-repositories/test_connection', array('controller'=>'git_source_admin', 'action'=>'test_repository_connection'));
      
      Router::map('admin_source_git_repository', '/admin/tools/source/git-repositories/:source_repository_id', array('controller' => 'git_source_admin', 'action' => 'view'), array('source_repository_id'=>Router::MATCH_ID));
      Router::map('admin_source_git_repository_edit', '/admin/tools/source/git-repositories/:source_repository_id/edit', array('controller' => 'git_source_admin', 'action' => 'edit'), array('source_repository_id'=>Router::MATCH_ID));
      Router::map('admin_source_git_repository_delete', '/admin/tools/source/git-repositories/:source_repository_id/delete', array('controller' => 'git_source_admin', 'action' => 'delete'), array('source_repository_id'=>Router::MATCH_ID));
      Router::map('admin_source_git_repository_usage', '/admin/tools/source/git-repositories/:source_repository_id/usage', array('controller'=>'git_source_admin', 'action'=>'usage'), array('source_repository_id'=>Router::MATCH_ID));
      
      //Mercurial
      Router::map('admin_source_mercurial_settings', '/admin/tools/source/mercurial-settings', array('controller' => 'mercurial_source_admin', 'action' => 'settings'));
      Router::map('admin_source_mercurial_repositories', '/admin/tools/source/mercurial-repositories', array('controller' => 'mercurial_source_admin', 'action' => 'index'));
      Router::map('admin_source_mercurial_repositories_add', '/admin/tools/source/mercurial-repositories/add', array('controller' => 'mercurial_source_admin', 'action' => 'add'));
      Router::map('admin_source_mercurial_repository_test_connection', '/admin/tools/source/mercurial-repositories/test_connection', array('controller'=>'mercurial_source_admin', 'action'=>'test_repository_connection'));
      Router::map('admin_source_mercurial_test', '/admin/tools/source/mercurial-repositories/test', array('controller'=>'mercurial_source_admin', 'action'=>'test_mercurial'));
      
      Router::map('admin_source_mercurial_repository', '/admin/tools/source/mercurial-repositories/:source_repository_id', array('controller' => 'mercurial_source_admin', 'action' => 'view'), array('source_repository_id'=>Router::MATCH_ID));
      Router::map('admin_source_mercurial_repository_edit', '/admin/tools/source/mercurial-repositories/:source_repository_id/edit', array('controller' => 'mercurial_source_admin', 'action' => 'edit'), array('source_repository_id'=>Router::MATCH_ID));
      Router::map('admin_source_mercurial_repository_delete', '/admin/tools/source/mercurial-repositories/:source_repository_id/delete', array('controller' => 'mercurial_source_admin', 'action' => 'delete'), array('source_repository_id'=>Router::MATCH_ID));
      Router::map('admin_source_mercurial_repository_usage', '/admin/tools/source/mercurial-repositories/:source_repository_id/usage', array('controller'=>'mercurial_source_admin', 'action'=>'usage'), array('source_repository_id'=>Router::MATCH_ID));
      
      // Search
      Router::map('source_search_index_admin_build', 'admin/search/source/build', array('controller' => 'source_search_index_admin', 'action' => 'build', 'search_index_name' => 'source'));
      
      Router::map('activity_logs_admin_rebuild_source', 'admin/indices/activity-logs/rebuild/source', array('controller' => 'activity_logs_admin', 'action' => 'rebuild_source'));
      
      Router::map('object_contexts_admin_rebuild_source', 'admin/indices/object-contexts/rebuild/source', array('controller' => 'object_contexts_admin', 'action' => 'rebuild_source'));
    } // defineRoutes
    
    /**
     * Define event handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_project_tabs', 'on_project_tabs');
      EventsManager::listen('on_available_project_tabs', 'on_available_project_tabs');
      EventsManager::listen('on_project_permissions', 'on_project_permissions');
      EventsManager::listen('on_object_inspector', 'on_object_inspector');
      EventsManager::listen('on_object_options', 'on_object_options');
      EventsManager::listen('on_notification_inspector', 'on_notification_inspector');
      EventsManager::listen('on_hourly', 'on_hourly');
      EventsManager::listen('on_daily', 'on_daily');
      EventsManager::listen('on_frequently', 'on_frequently');
      EventsManager::listen('on_admin_panel', 'on_admin_panel');
      EventsManager::listen('on_object_deleted', 'on_object_deleted');
      EventsManager::listen('on_search_indices', 'on_search_indices');
      EventsManager::listen('on_rebuild_activity_log_actions', 'on_rebuild_activity_log_actions');
      EventsManager::listen('on_rebuild_object_contexts_actions', 'on_rebuild_object_contexts_actions');
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
    
    // ---------------------------------------------------
    //  Name
    // ---------------------------------------------------
    
    /**
     * Get module display name
     *
     * @return string
     */
    function getDisplayName() {
      return lang('Source');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @return string
     */
    function getDescription() {
      return lang('Adds source version control functionality to projects');
    } // getDescription
    
    /**
     * Return module uninstallation message
     *
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated. Data received using this module will be removed from local database, but the original content from repositories used by this module will be left intact');
    } // getUninstallMessage

    /**
     * Return object types (class names) that this module is working with
     *
     * @return array
     */
    function getObjectTypes() {
      return array('GitRepository', 'GitCOmmit', 'MercurialRepository', 'MercurialCommit', 'SvnRepository', 'SvnCommit');
    } // getObjectTypes

    // ---------------------------------------------------
    //  Install / Uninstall
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
          require_once APPLICATION_PATH . '/modules/source/models/search/SourceSearchIndex.class.php';
        
          $index = new SourceSearchIndex($provider);
          $index->initialize();
        } // if
      } // if
    } // install
    
    /**
     * Uninstall source module
     */
    function uninstall() {
    	parent::uninstall();
    	
    	$index = Search::getIndex('source');
    	
    	if($index instanceof SourceSearchIndex) {
    	  $index->tearDown();
    	} // if

      ActivityLogs::deleteByParentTypes(array("SvnRepository", "GitRepository", "MercurialRepository"));
      FwApplicationObjects::cleanUpContextsByParentTypes(array('GitRepository', 'MercurialRepository', 'SvnRepository', 'ProjectSourceRepository'));
    } //uninstall
    
  }