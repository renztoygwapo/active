<?php

  // Build on top of project controller
  AngieApplication::useController('project', SYSTEM_MODULE);
  
  /**
   * Repository controller
   * 
   * @package activeCollab.modules.source
   * @subpackage controllers
   */
  class RepositoryController extends ProjectController {
  
    /**
     * Active module
     *
     * @var constant
     */
    protected $active_module = SOURCE_MODULE;
  
    /**
     * Active source repository
     *
     * @var SourceRepository
     */
    protected $active_repository = null;
    
    /**
     * Project object repository
     *
     * @var ProjectSourceRepository
     */
    protected $project_object_repository = null;
     
    /**
     * Active file
     *
     * @var string
     */
    protected $active_file = null;
    
    /**
     * Active file basename
     *
     * @var string
     */
    protected $active_file_basename = null;
    
    /**
     * Active revision
     *
     * @var integer
     */
    protected $active_revision = null;
    
    /**
     * Peg revision
     *
     * @var integer
     */
    protected $peg_revision = null;
    
    /**
     * Active commit
     * 
     * @var SourceCommit
     */
    protected $active_commit = null;

    /**
     * Active branch
     *
     * @var string
     */
    protected $active_branch = '';
  
    /**
     * Repository engine
     *
     * @var RepositoryEngine
     */
    protected $repository_engine = null;
    
    /**
     * Subscriptions controller delegate
     *
     * @var SubscriptionsController
     */
    protected $subscriptions_delegate;
    
    /**
     * Reminder controller delegate
     * 
     * @var RemindersController
     */
    protected $reminders_delegate;
    
    /**
     * Move to project delegate controller
     *
     * @var MoveToProjectController
     */
    protected $move_to_project_delegate;
    
    /**
     * Construct controller
     *
     * @param Request $parent
     * @param mixed $context
     */
    function __construct($parent, $context = null) {
      parent::__construct($parent, $context);
      
      if ($this->getControllerName() == 'repository') {
      	$this->subscriptions_delegate = $this->__delegate('subscriptions', SUBSCRIPTIONS_FRAMEWORK_INJECT_INTO, 'project_source_repository');
      	$this->reminders_delegate = $this->__delegate('reminders', REMINDERS_FRAMEWORK_INJECT_INTO, 'project_source_repository');
      	$this->move_to_project_delegate = $this->__delegate('move_to_project', SYSTEM_MODULE, 'project_source_repository');
      } // if
    }
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if(!ProjectSourceRepositories::canAccess($this->logged_user, $this->active_project)) {
        $this->response->forbidden();
      } // if
      
      $source_module_url = source_module_url($this->active_project);
      
      // wireframe
      $this->wireframe->tabs->setCurrentTab('source');
      $this->wireframe->breadcrumbs->add('source', lang('Source'), $source_module_url);
      $this->wireframe->hidePrintButton();
      
      $repository_id = $this->request->get('project_source_repository_id');

      $this->project_object_repository = ProjectSourceRepositories::findById($repository_id);
      if($this->project_object_repository instanceof ProjectSourceRepository) {
        $this->active_repository = $this->project_object_repository->getSourceRepository();


        
        $this->project_object_repository->source_repository = $this->active_repository;
        // load repository engine
        
        if (($error = $this->active_repository->loadEngine()) !== true) {
          throw new Error($error);
        } // if
        if (!$this->repository_engine = $this->active_repository->getEngine($this->active_project->getId(),$this->project_object_repository)) {
          throw new Error(lang('Failed to load repository engine class'));
        } // if

        // active branch
        if ($this->active_repository instanceof SourceRepository && $this->active_repository->hasBranches()) {
          $this->active_branch = $this->project_object_repository->getDefaultBranch($this->logged_user);
          $this->repository_engine->active_branch = $this->active_branch;
        } //if

        $this->active_repository->mapped_users = SourceUsers::findBySourceRepository($this->active_repository);
        

        // active commit
        $this->active_revision = array_var($_GET, 'r') === null ? null : intval(array_var($_GET, 'r'));
        
        $this->active_commit = $this->active_repository->getCommitByRevision($this->active_revision, $this->active_branch);

        //breadcrumbs
        if(!$this->active_repository->isNew()) {
          $this->wireframe->breadcrumbs->add('repository', clean($this->active_repository->getName()), $this->project_object_repository->getHistoryUrl());
          if($this->active_repository->hasBranches()) {
            $this->wireframe->breadcrumbs->add('branch', clean($this->active_branch), $this->project_object_repository->getHistoryUrl());
          } //if
        } // if
      } else {
      	$this->project_object_repository = new ProjectSourceRepository();
      } //if 
      if (!($this->active_commit instanceof SourceCommit) && ($this->active_repository instanceof SourceRepository)) {
        $this->active_commit = $this->active_repository->getNewCommit();
      } // if
      
      // active file
      $this->active_file = urldecode($this->request->get('path'));
      if ($this->repository_engine instanceof RepositoryEngine) {
        $this->active_file = $this->repository_engine->slashifyForHistoryQuery($this->active_file);
      } //if

      $this->peg_revision = urldecode($this->request->get('peg_revision'));
      $path_info = pathinfo($this->active_file);
      $this->active_file_basename = array_var($path_info, 'basename', null);
      // smarty stuff
      $this->smarty->assign(array(
        'request' => $this->request,
        'project_tab' => SOURCE_MODULE,
        'active_repository' => $this->active_repository,
        'project_object_repository' => $this->project_object_repository,
        'active_revision' => $this->active_revision,
        'active_commit' => $this->active_commit,
        'active_file' => $this->active_file,
        'active_file_basename' => $this->active_file_basename,
        'active_project' => $this->active_project,
        'active_branch' => $this->active_branch,
      ));
      if($this->subscriptions_delegate instanceof SubscriptionsController) {
        $this->subscriptions_delegate->__setProperties(array(
          'active_object' => &$this->project_object_repository,
        ));
      } // if
      
      if($this->reminders_delegate instanceof RemindersController) {
        $this->reminders_delegate->__setProperties(array(
          'active_object' => &$this->project_object_repository,
        ));
      } // if
      
      if($this->move_to_project_delegate instanceof MoveToProjectController) {
      	$this->move_to_project_delegate->__setProperties(array(
          'active_project' => &$this->active_project,
          'active_object' => &$this->project_object_repository,
        ));
      } // if
      
    } // __before
    
    /**
     * List repositories
     */
    function index() {
      $can_add_repository = false;
      if(ProjectSourceRepositories::canAdd($this->logged_user, $this->active_project) && !$this->request->isMobileDevice()) {
        $can_add_repository = true;

        $this->wireframe->actions->add('add_existing_repository', lang('Add Existing Repository'), Router::assemble('repository_add_existing',array('project_slug' => $this->active_project->getSlug())), array(
        	'onclick' => new FlyoutFormCallback('repository_created', array('width' => 'narrow')),
          'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),        	
        ));
        
        $this->wireframe->actions->add('create_new_repository', lang('Create New Repository'), Router::assemble('repository_add_new',array('project_slug' => $this->active_project->getSlug())), array(
        	'onclick' => new FlyoutFormCallback('repository_created', array('width' => '600')),
          'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),        	
        ));
      } // if

      $repositories = ProjectSourceRepositories::findByProjectId($this->active_project->getId(), $this->logged_user->getMinVisibility());
      $this->response->assign(array(
        'repositories' => $repositories,
        'can_add_repository' => $can_add_repository
      ));
    } // index
    
    /**
     * View commit history of selected repository
     */
    function history() {
      if($this->project_object_repository->canView($this->logged_user)) {
        if ($this->request->isWebBrowser()) {
          $this->project_object_repository->accessLog()->log($this->logged_user);
          
  	      $this->wireframe->actions->add('browse_repository', lang('Browse Repository'), $this->project_object_repository->getBrowseUrl());

  	      if ($this->project_object_repository->canEdit($this->logged_user) || ($this->project_object_repository->getCreatedById() == $this->logged_user->getId())) {
  	        $this->wireframe->actions->add('update_repository', lang('Update'), $this->project_object_repository->getUpdateUrl(), array(
  	        	'id'				=> 'repository_ajax_update',
  	        	'onclick'		=> new FlyoutCallback(array('width' => 'narrow'))
  	       	));
  	      } //if

          $this->wireframe->setPageObject($this->project_object_repository, $this->logged_user);
        } //if
        $filter_by_author = $this->request->get('filter_by_author');
        $limit = $this->request->isMobileDevice() ? 100 : 30;
        if ($filter_by_author === null) {
          $total_commits = SourceCommits::count(array('repository_id' => $this->active_repository->getId(), 'branch_name' => $this->active_branch));
          $commits_object = SourceCommits::find(array(
                'conditions' => array('repository_id = ? AND branch_name = ?',$this->active_repository->getId(), $this->active_branch),
                'limit'      => $limit,
                'order'	   => 'commited_on DESC'
          ));
        } else {
          $total_commits = SourceCommits::count(array('repository_id' => $this->active_repository->getId(),'commited_by_name' => $filter_by_author, 'branch_name' => $this->active_branch));
          $commits_object = SourceCommits::find(array(
                'conditions' => array('repository_id = ? AND commited_by_name = ? AND branch_name = ?',$this->active_repository->getId(), $filter_by_author, $this->active_branch),
                'limit'      => $limit,
                'order'	   => 'commited_on DESC'
          ));
        } //if
        $commits = array();
        if (is_foreachable($commits_object)) {
          foreach ($commits_object as $key => $commit) {
            $commit->total_paths = $commit->countPaths();
            $commit->grouped_paths = $this->repository_engine->groupPaths(SourcePaths::getPathsForCommit($commit->getId()));
            $commits[] = $commit;
          } // foreach
          if (!is_null($filter_by_author)) {
            $filter_by_author = array();
            $filter_by_author['user_object'] = $commits[0]->getCommitedBy();
            $filter_by_author['user_name'] = $commits[0]->getCommitedByName();
          } // if
        } // if
        
        $commits = group_by_date($commits,null,'getCommitedOn');

        $this->smarty->assign(array(
          'commits'               => $commits,
          'project'               => $this->active_project,
          'total_commits'         => $total_commits,
          'filter_by_author'      => $filter_by_author,
          'show_thirty_more_url'  => Router::assemble('repository_history_show_thirty_more', array('project_slug' => $this->active_project->getSlug(), 'project_source_repository_id' => $this->project_object_repository->getId())),
          'default_avatar_url'    => ROOT_URL . '/avatars/default.16x16.gif',
          'project_repositories_url' => Router::assemble('project_repositories', Array('project_slug' => $this->active_project->getSlug())),
        ));
      } else {
        $this->response->forbidden();
      } // if
    } // history
    
    /**
     * Ajax call function for showing thirty more revisions in history template
     */
    function history_show_thirty_more() {
      
      if(!$this->project_object_repository->canView($this->logged_user)) {
        $this->response->forbidden();
      } // if
      
      $filter_by_author = $this->request->get('filter_by_author');
      $offset_multiplier = $this->request->get('offset');
      if (is_null($filter_by_author)) {
        $commits_object = SourceCommits::find(array(
          'conditions' => array('repository_id = ? AND branch_name = ?',$this->active_repository->getId(), $this->active_branch),
          'limit'      => 30,
          'offset'     => $offset_multiplier * 30,
          'order'	   => 'commited_on DESC'
        ));
      } else {
        $commits_object = SourceCommits::find(array(
          'conditions' => array('repository_id = ? AND commited_by_name = ? AND branch_name = ?',$this->active_repository->getId(), $filter_by_author, $this->active_branch),
          'limit'      => 30,
          'offset'     => $offset_multiplier * 30,
          'order'	   => 'commited_on DESC'
        ));
      } //if
      $commits = array();
      if (is_foreachable($commits_object)) {
        foreach ($commits_object as $key => $commit) {
          $commit->total_paths = $commit->countPaths();
          $commits[] = $commit;
        } // foreach
        if (!is_null($filter_by_author)) {
          $filter_by_author = array();
          $filter_by_author['user_object'] = $commits[0]->getCommitedBy();
          $filter_by_author['user_name'] = $commits[0]->getCommitedByName();
        } // if
      } // if
      if (!is_foreachable($commits) || count($commits) <= 0) {
        die ('empty');
      } //if
      
      $commits = group_by_date($commits,null,'getCommitedOn');
      $this->smarty->assign(array(
        'commits'           => $commits,
        'filter_by_author'  => $filter_by_author,
        'offset_multiplier' => $offset_multiplier,
      	'default_avatar_url'    => ROOT_URL . '/avatars/default.16x16.gif'
      ));
    } // history
    
    /**
     * One commit info
     */
    function one_commit_info() {
      if(!$this->project_object_repository->canView($this->logged_user)) {
        $this->response->forbidden();
      } // if
      $commit_object = SourceCommits::findByRevision($this->active_revision,$this->active_repository, $this->active_branch);
      $commit_object->total_paths = $commit_object->countPaths();
      $grouped_paths = $this->repository_engine->groupPaths(SourcePaths::getPathsForCommit($commit_object->getId()));
      $this->smarty->assign(array(
        'commit'           => $commit_object,
        'grouped_paths'    => $grouped_paths,
      ));
    }
    
    /**
     * Commit diff info
     */
    function commit() {
      if(!$this->project_object_repository->canView($this->logged_user)) {
        $this->response->forbidden();
      } // if

      if (!($this->active_commit instanceof SourceCommit)) {
        $this->response->notFound();
      } // if
  
      $this->wireframe->actions->add('revision_history', lang('Revision History'), $this->project_object_repository->getHistoryUrl());
      $this->wireframe->actions->add('browse_repository', lang('Browse Repository'), $this->project_object_repository->getBrowseUrl());

      $grouped_paths = $this->repository_engine->groupPaths(SourcePaths::getPathsForCommit($this->active_commit->getId()));
      ksort($grouped_paths);
      $previous_revision = $this->repository_engine->previousRevision($this->active_revision);

      if (RepositoryEngine::pathInFolder($this->active_file)) {
      	$diff = $this->repository_engine->compareToRevision($this->active_file, $previous_revision, $this->active_revision);
      	$parsed = $this->repository_engine->parseDiff($diff);
      } else {
	      $diff = $this->active_commit->getDiff();
	      if (!is_array($diff)) {
	        $diff = $this->repository_engine->compareToRevision(null, $previous_revision, $this->active_revision);
	        if (is_array($diff)) {
            // sanity check - don't import into database too big diff
            $serialized_diff = serialize($diff);

            $max_packet_size = DB::getMaxPacketSize();

            if (strlen($serialized_diff) < $max_packet_size) {
	            $this->active_commit->setDiff($serialized_diff);
	            $this->active_commit->save();
            } else {
              $error = array (
                'message' => lang('This diff (:diff_size) is bigger than your MySQL max packet size (:packet_size). Please increase it so that the diff could be shown. Please read more about it ',
                 array(
                   'diff_size' => format_file_size(strlen($serialized_diff)),
                   'packet_size' => format_file_size($max_packet_size)
                 ))
                 .'<a href="https://dev.mysql.com/doc/refman/5.5/en/packet-too-large.html" target="_blank">'.lang('here').'</a>',
                'error'   => true,
                'show_paths' => true
              );
            } //if
	        } else {
            $error = array (
              'message' => lang('Unable to retrieve diff information for selected commit'),
              'error'   => true,
            );
	        } // if
	      } // if
	      
	      $parsed = $this->repository_engine->parseDiff($diff);
      } // if

      if (isset($error) && $error) {
        $this->smarty->assign($error);
      } else {
        $parsed = array_values(SourceCommit::removeNonTextFiles($parsed));
        $this->smarty->assign(array(
          'grouped_paths' => $grouped_paths,
          'diff'          => $parsed,
        ));
      } //if
    } // commit info
    
    /**
     * Render page with commit paths
     */
    function commit_paths() {
      if(!$this->project_object_repository->canView($this->logged_user)) {
        $this->response->forbidden();
      } // if
      
      if (!($this->active_commit instanceof SourceCommit)) {
        $this->response->notFound();
      } // if
      $this->smarty->assign(array(
      	'commit_paths' => $this->repository_engine->groupPaths(SourcePaths::getPathsForCommit($this->active_commit->getId()))
      ));
    } // commit_paths
    
    
    /**
     * Get project objects affected by a commit
     */
    function project_object_commits() {
      if ($this->request->isAsyncCall()) {
        $project_object_id = $this->request->get('object_id');
        $project_object = ProjectObjects::findById($project_object_id);

        if (($project_object instanceof ProjectObject)) {
          $this->wireframe->breadcrumbs->add('object_commits', $project_object->getType(). ' ' . $project_object->getName(), $project_object->getViewUrl());
          $commits = group_by_date(CommitProjectObjects::findCommitsByObject($project_object, $this->active_project), null, 'getCommitedOn');
          $this->smarty->assign(array(
            'commits' => array_reverse($commits),
            'active_object'  => $project_object
          ));
        } else {
          $this->response->notFound();
        }//if
      } else {
        $this->response->badRequest();
      } //if
    } // commit_project_objects
    
    
    /**
     * Browse repository
     */
    function browse() {
      if ($this->project_object_repository->isNew()) {
        $this->response->notFound();
      } // if

      if(!$this->project_object_repository->canView($this->logged_user)) {
        $this->response->forbidden();
      } // if
      if ($this->active_commit->isNew()) {
        $this->active_commit = $this->active_repository->getLastCommit($this->active_branch);
        $revision_number = 'head';
        if ($this->active_commit instanceof SourceCommit) {
          $this->active_revision = $this->active_commit->getRevisionNumber();
        } else {
          $this->active_revision = null;
        } // if
      } else {
        if ($this->active_commit == $this->active_repository->getLastCommit($this->active_branch)) {
          $revision_number = 'head';
        } else {
          $revision_number = $this->active_commit->getName();
        } // if
      } //if

      if ($this->active_revision) {
        // path info
        $path_info = $this->repository_engine->getInfo($this->active_file, $this->active_revision, $this->peg_revision);
        $latest_revision = $path_info['revision'];
        $file_latest_revision = SourceCommits::findByRevision($latest_revision, $this->active_repository, $this->active_branch);
        if (!($file_latest_revision instanceof SourceCommit)) {
          $file_latest_revision = $this->active_commit;
        } // if
        // check if path is directory or file
        if ($path_info['type'] == 'dir') {
          /**** DIRECTORY ****/
          set_time_limit(0);
          $this->wireframe->actions->add('commit_history', lang('Commit History'), $this->project_object_repository->getHistoryUrl());

          $this->wireframe->setPageObject($this->active_commit, $this->logged_user);
          //$this->smarty->assign('list', $this->repository_engine->browse($this->active_revision, $this->active_file));

          // custom template
          $this->setView(get_view_path('browse_directory',$this->getControllerName(),SOURCE_MODULE));
        } else {
          /**** FILE ****/
          $this->wireframe->actions->add('options', lang('Options'), '#', array(
            'subitems' => source_module_get_file_options($this->project_object_repository, $this->active_commit, $this->active_file)
          ));

          $this->wireframe->breadcrumbs->add('repository_browse', lang('Browse'), $this->project_object_repository->getBrowseUrl($this->active_commit));
          $this->wireframe->breadcrumbs->add('current_repository_file', clean($this->active_file_basename), $this->project_object_repository->getBrowseUrl($this->active_commit, $this->active_file, $this->active_commit->getRevisionNumber()));

          $file_type = $this->repository_engine->getFileType($path_info['path']);

          //fetch file source
          $file_source = $this->repository_engine->getFileContent($this->active_revision, $this->active_file, $path_info['last_edited_revision']);
          if (!$file_source) {
            //file does not exist in this revision
            $path_info = false;
          } else {
            // preparing text file
            if ($file_type === 'text') {
              $file_source = explode("\n",$file_source);
              $lines = implode("\n", range(1, count($file_source)));
              $file_source = implode("\n",$file_source);

              // if file is larger than 1MB do not show it
              if (strlen(strlen($file_source) > 1048576) ) {
                $file_type = false;
              } //if
            } else if ($file_type === 'image') {
              $this->smarty->assign(array(
                'image_base64'      => base64_encode($file_source),
                'image_mime_type'   => get_mime_type($path_info['path'], null, false)
              ));
            } //if
          } //if

          $this->smarty->assign(array(
            'path_info'	     => $path_info,
            'navigation'     => $this->active_file,
            'syntax'				 => HyperlightForAngie::getSyntaxForFile($path_info['path']),
            'lines'          => isset($lines) ? $lines : false,
            'source'         => $file_source === false ? false : $file_source,
            'file_type'	     => $file_type,
            'compare_url'    => $this->project_object_repository->getFileCompareUrl($this->active_commit, $this->active_file),
          ));

          // custom template
          $this->setView(get_view_path('browse_file',$this->getControllerName(),SOURCE_MODULE));

        } // if
        // general template vars for both directory and file
        $this->smarty->assign(array(
          'revision_number'     => $revision_number,
          'latest_revision'     => $file_latest_revision,
          'active_commit'       => $this->active_commit,
          'active_revision'     => $this->active_revision,
          'browse_url'          => $this->project_object_repository->getBrowseUrl(null, $this->active_file),
          'change_revision_url' => Router::assemble('repository_browse_change_revision', array('project_slug' => $this->active_project->getSlug(),'project_source_repository_id' => $this->project_object_repository->getId()))
        ));
      } else {
        $this->response->assign('no_data',true);
        $this->setView(get_view_path('browse_directory',$this->getControllerName(),SOURCE_MODULE));
      } //if
    } // browse repository
    
    
    /**
     * Expands browse tree
     *
     * @param null
     * @return void
     */
    function browse_toggle() {
      set_time_limit(0);   
      $path_info = $this->repository_engine->getInfo($this->active_file, $this->active_revision);
      if ($path_info['type'] == 'dir') {
        $list = $this->repository_engine->browse($this->active_revision, $this->active_file);
        if (count($list['entries']) == 0) {
          die();
        } else {
          $this->smarty->assign(array(
            'parent_key'  => $this->request->get('key', null),
            'list'    => $list,
          ));
        }
        // custom template
        $this->setView(get_view_path('browse_tree',$this->getControllerName('RepositoryController'),SOURCE_MODULE));
      } else {
        die();
      }
    }//browse_toggle
    
    /**
     * Download file
     */
    function file_download() {
      if($this->active_file) {
        if($this->project_object_repository->canView($this->logged_user)) {
          $file_source = false;
          
          $path_info = $this->repository_engine->getInfo($this->active_file, $this->active_revision);
          if ($path_info['type'] === 'file') {
            $file_source = ($this->repository_engine->getFileContent($this->active_revision, $this->active_file,$path_info['last_edited_revision']));
          } //if
          
          if ($file_source) {
            $this->response->respondWithContentDownload($file_source, 'application/octet-stream', $this->active_file_basename);
          } else {
            $this->response->badRequest();
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // download file
    
    /**
     * Compare two revisions of a file
     */
    function compare() {
      if($this->project_object_repository->canView($this->logged_user)) {
        $this->wireframe->actions->add('commit_history', lang('Commit History'), $this->project_object_repository->getHistoryUrl());
      
        $compare_from = SourceCommits::findByRevision($this->request->get('rev_compare_from'), $this->active_repository, $this->active_branch);
        $compare_to = SourceCommits::findByRevision($this->request->get('rev_compare_to'), $this->active_repository, $this->active_branch);
        
        if (!($compare_from instanceof SourceCommit) || !($compare_to instanceof SourceCommit)) {
          throw new Error(lang('Revision does not exist'));
        } // if
        // path info
        $info_compare_to = $this->repository_engine->getInfo($this->active_file,$compare_to->getRevisionNumber());
        $info_compare_from = $this->repository_engine->getInfo($this->active_file,$compare_from->getRevisionNumber());
        
        if ($info_compare_to && $info_compare_from) {
          $diff_data = $this->repository_engine->compareToRevision($this->active_file, $compare_to->getRevisionNumber(), $compare_from->getRevisionNumber());
          $diff_changes = $this->repository_engine->parseDiff($diff_data);
        }//if

        $this->smarty->assign(array(
          'navigation'          => $this->active_file,
          'diff'                => $diff_changes,
          'compare_from'        => $compare_from,
          'compare_to'       	  => $compare_to,
          'info_compare_to'     => $info_compare_to,
          'info_compare_from'   => $info_compare_from 
        ));
      } else {
        $this->response->forbidden();
      } // if
    } // compare
    
    /**
     * Popup window for comparing 2 revisions, which fills container with differences
     *
     * @param null
     * @return void
     */
    function compare_dialog_form() {
      if(!$this->project_object_repository->canView($this->logged_user)) {
        $this->response->forbidden();
      } // if
      // wireframe
      $this->wireframe->actions->add('commit_history', lang('Commit History'), $this->project_object_repository->getHistoryUrl());
      $path_info = $this->repository_engine->getInfo($this->active_file,$this->active_repository->getLastCommit($this->active_branch)->getRevisionNumber());
      $commits = array();

      $path = $this->repository_engine->slashifyForHistoryQuery($path_info['path'], true);

      $source_paths = SourcePaths::findSourcePathsForPath($path, $this->active_branch);

      foreach ($source_paths as $source_path) {
        $commit= SourceCommits::findById($source_path->getCommitId());
        if ($commit->getRepositoryId() == $this->active_repository->getId()) {
          $commits[] = $commit;
        }//if
      } //foreach
      $commits = SourceCommits::sortCommitsByDate($commits);
      
      $this->smarty->assign(array(
        'commits'     => $commits,
        'file'        => $this->active_file,
        'revision'    => $this->active_revision,
        'navigation'  => $this->active_file,
      ));
    }
  
    /**
     * Add an existing repository
     */
    function add_existing() {
      if(!ProjectSourceRepositories::canAdd($this->logged_user, $this->active_project)) {
        $this->response->forbidden();
      } // if

      $repository_data = $this->request->post('repository');
      if(!is_array($repository_data)) {
        $repository_data = array(
        'visibility'       => $this->active_project->getDefaultVisibility(),
        );
      } // if
      
      if ($this->request->isSubmitted()) {
      	try {
      	  
          $source_repository_id = null;
          $temp_source_repository_id = $repository_data['source_repository_id'];
          $this->active_repository = SourceRepositories::findById($temp_source_repository_id);
          if ($this->active_repository instanceof SourceRepository) {
            $source_repository_id = $temp_source_repository_id;
          } //if
	        
          $this->project_object_repository->setName($this->active_repository->getName());
          $this->project_object_repository->setParentId($source_repository_id);
          $this->project_object_repository->setVisibility($repository_data['visibility']);
          $this->project_object_repository->setProjectId($this->active_project->getId());
          $this->project_object_repository->setCreatedBy($this->logged_user);
          $this->project_object_repository->setState(STATE_VISIBLE);
          
          $this->project_object_repository->save();
      		
      		$this->response->respondWithData($this->active_repository, array(
      		  'as' => 'repository', 
      		  'detailed' => true, 
      		));
      	} catch (Exception $e) {
      		$this->response->exception($e);
      	} // try
      } // if
      
      $this->smarty->assign(array(
        'existing_repositories' => ProjectSourceRepositories::getForProjectRepositorySelect($this->active_project),
        'repository_add_url' => Router::assemble('repository_add_existing', array('project_slug' => $this->active_project->getSlug())),
        'repository_data' => $repository_data,
        'disable_url_and_type' => false,
        'aid_engine' => '',
      ));
    } // add_existing
  
    
    /**
     * Add new repository
     */
    function add_new() {
      if(!ProjectSourceRepositories::canAdd($this->logged_user, $this->active_project)) {
        $this->response->forbidden();
      } // if
      
      $repository_data = $this->request->post('repository');
      if(!is_array($repository_data)) {
        $repository_data = array(
        'visibility'       => $this->active_project->getDefaultVisibility(),
        );
      } // if
      
      if ($this->request->isSubmitted()) {
      	try {
    	  
      	  DB::beginWork('Begin adding new repository to a project');
    	  $source_repository_id = null;
    	  $repository_data['repository_path_url'] = ProjectSourceRepository::slashifyAtEnd($repository_data['repository_path_url']);
          
    	  $this->active_repository = new $repository_data['type']();
    	  
          $this->active_repository->setAttributes($repository_data);
          $this->active_repository->setCreatedBy($this->logged_user);
          if (($error = $this->active_repository->loadEngine()) !== true) {
            throw new Error(lang('Failed to load repository engine'));
          } // if
          if (!$this->active_repository->engineIsUsable()) {
          	throw new Error(lang('Please configure the SVN extension prior to adding a repository'));
          } // if

          // check validity of repository credentials
          $result = $this->active_repository->testRepositoryConnection();
          if ($result !== true) {
            if ($result === false) {
              $message = 'Please check URL or login parameters.';
            } else {
              $message = $result;
            } //if
            throw new Error(lang('Failed to connect to repository: :message', array('message'=>$message)));
          } //if

          $this->active_repository->save();
					
          $this->project_object_repository->setName($this->active_repository->getName());
          $this->project_object_repository->setParentId($this->active_repository->getId());
          $this->project_object_repository->setVisibility($repository_data['visibility']);
          $this->project_object_repository->setProjectId($this->active_project->getId());
          $this->project_object_repository->setCreatedBy($this->logged_user);
          $this->project_object_repository->setState(STATE_VISIBLE);
          
          $this->project_object_repository->save();
          DB::commit('Successfully added new repository to a project');
                    
          // we need specific repository return format as we need graph rendered on page (which is to complicated to do with javascript)
          $this->response->respondWithData($this->project_object_repository);
      	} catch (Exception $e) {
      		DB::rollback('Failed to add repository');
      		$this->response->exception($e);
      	} // try
      } // if
      
      $this->smarty->assign(array(
        'types' => source_module_types(),
        'update_types' => source_module_update_types(),
        'repository_add_url' => Router::assemble('repository_add_new', array('project_slug' => $this->active_project->getSlug())),
        'repository_data' => $repository_data,
        'disable_url_and_type' => false,
        'aid_engine' => '',
        'repository_test_connection_url'	=> Router::assemble('repository_test_connection', array('project_slug' => $this->active_project->getSlug()))
      ));
    } // add_new
  
    /**
     * Edit repository
     */
    function edit() {
      if(!$this->project_object_repository->canEdit($this->logged_user)) {
        $this->response->forbidden();
      } // if
      
      $repository_data = $this->request->post('repository');
      if (!is_array($repository_data)) {
        $repository_data = array(
	        'name'            		=> $this->active_repository->getName(),
	        'repository_path_url'	=> $this->active_repository->getRepositoryPathUrl(),
	        'username'        		=> $this->active_repository->getUsername(),
	        'password'        		=> $this->active_repository->getPassword(),
	        'repositorytype'  		=> $this->active_repository->getType(),
	        'updatetype'      		=> $this->active_repository->getUpdateType(),
	        'visibility'      		=> $this->project_object_repository->getVisibility()
        );
      } // if
  
      if ($this->request->isSubmitted()) {
        try {
          DB::beginWork('Updating repository @ ' . __CLASS__);
          $repository_data['url'] = ProjectSourceRepository::slashifyAtEnd($repository_data['url']);
          $this->active_repository->setAttributes($repository_data);
          $this->project_object_repository->setVisibility($repository_data['visibility']);
          $this->project_object_repository->setName($repository_data['name']);

          if (($error = $this->active_repository->loadEngine()) !== true) {
            throw new Error(lang('Failed to load engine.'));
          } // if
          $this->repository_engine = $this->active_repository->getEngine($this->active_project->getId(),$this->project_object_repository);

          if (!$this->repository_engine->isUsable()) {
            throw new Error(lang('Please configure the SVN extension before prior to editing a repository'));
          } // if

          $this->repository_engine->triggerred_by_handler = true;

          $result = $this->active_repository->testRepositoryConnection();
          // check validity of repository credentials
          if ($result !== true) {
            if ($result === false) {
              $message = 'Please check URL or login parameters.';
            } else {
              $message = $result;
            } //if
            throw new Error(lang('Failed to connect to repository: :message', array('message'=>$message)));
          } //if

          $this->active_repository->save();
          $this->project_object_repository->save();

          DB::commit('Repository updated @ ' . __CLASS__);

          if($this->request->isPageCall()) {
            $this->response->redirectToUrl($this->project_object_repository->getViewUrl());
          } else {
            $this->response->respondWithData($this->project_object_repository, array(
              'as' => 'project_object_repository',
              'detailed' => true,
            ));
          } // if
        } catch (Exception $e) {
          DB::rollback('Failed to update repository @ ' . __CLASS__);
          if($this->request->isPageCall()) {
            $this->response->assign('errors', $e);
          } else {
            $this->response->exception($e);
          } // if
        } //try

      } // if

      $test_connection_url = Router::assemble('repository_test_connection', array('project_slug' => $this->active_project->getSlug()));
  
      $this->smarty->assign(array(
        'types'               => $this->active_repository->types, 
        'update_types'        => $this->active_repository->update_types,
        'repository_data'     => $repository_data,
        'active_repository'   => $this->active_repository,
        'disable_url_and_type'  => true,
        'aid_url'               => lang('The path to the existing repository cannot be changed'),
        'aid_engine'            => lang('Repository type cannot be changed'),
      	'repository_test_connection_url' => $test_connection_url,
      ));

    } // edit repository
  
    /**
     * Delete repository
     */
    function remove_from_project() {
      if($this->request->isSubmitted()) {
        if($this->project_object_repository->canDelete($this->logged_user)) {
          try {
            $this->project_object_repository->delete();

            if($this->request->isApiCall()) {
              $this->response->ok();
            } else {
              $this->response->respondWithData($this->active_repository, array(
                'detailed' => true
              ));
            } // if
          } catch(Exception $e) {
            DB::rollback('Failed to delete repository');
      			$this->response->exception($e);
          } // try
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // delete
  
    /**
     * Update a repository
     */
    function update() {
      if ($this->request->isAsyncCall()) {
        if ($this->project_object_repository->canEdit($this->logged_user)) {
          if (!is_error($this->repository_engine->error)) {
            $last_commit = $this->active_repository->getLastCommit($this->active_branch);

            $revision_to = $last_commit instanceof SourceCommit ? $last_commit->getRevisionNumber() : 1;
            $latest_revision = $last_commit instanceof SourceCommit ? $last_commit->getRevisionNumber() : ($this->repository_engine->getZeroRevision() - 1);
            $head_revision = $this->repository_engine->getHeadRevision($this->request->isAsyncCall());

            if (is_numeric($head_revision)) {
              // simple mass update
                if (!is_null($this->repository_engine->error)) {
                  die($this->repository_engine->error);
                } // if
                $revision = array_var($_GET, 'r') === null ? null : intval(array_var($_GET, 'r'));
                $finished = array_var($_GET, 'finished');
                if (is_int($revision)) {
                  $this->repository_engine->update($revision,$head_revision,$latest_revision);
                  die();
                } // if

                if ($finished) {
                  $total_commits = $this->project_object_repository->last_update_commits_count = $this->request->get('finished');
                  if ($total_commits <= MAX_UPDATED_COMMITS_TO_SEND_DETAILED_NOTIFICATIONS) {
                    $this->project_object_repository->detailed_notifications = true;
                  } //if
                  ProjectSourceRepositories::sendCommitNotificationsToSubscribers($this->project_object_repository);
                  $this->project_object_repository->createActivityLog($total_commits);
                  die('success');
                } // if
                $uptodate = intval($head_revision == $latest_revision);
                $this->smarty->assign(array(
                  'uptodate'      => $uptodate,
                  'head_revision' => $head_revision,
                  'last_revision' => $latest_revision,
                  'repository_update_url'    => $this->project_object_repository->getUpdateUrl(),
                  'indicator_ok'  => AngieApplication::getImageUrl('layout/bits/indicator-ok.png', ENVIRONMENT_FRAMEWORK),
                  'logs_per_request' => $this->repository_engine->getModuleLogsPerRequest(),
                ));
            } else {
              $this->response->assign(array(
                'error_message' =>  is_error($this->repository_engine->error) ? $this->repository_engine->error->getMessage() : $this->repository_engine->error
              ));
            }//if
          } else {
            $this->response->assign(array(
              'error_message' =>  is_error($this->repository_engine->error) ? $this->repository_engine->error->getMessage() : $this->repository_engine->error
            ));
          } //if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } //if
    } // update repository

    /**
     * Open dialog window for changing the branch
     */
    function change_branch() {
      if ($this->project_object_repository->canView($this->logged_user)) {
        $this->smarty->assign(array(
          'all_branches' => $this->repository_engine->getBranches(),
          'active_branch' => $this->active_branch
        ));
      } else {
        $this->response->forbidden();
      } // if
    } // change_branch

    /**
     * Test repository connection (async)
     */
    function test_repository_connection() {
    	
      if (!(array_var($_GET, 'url'))) {
      	die(lang('Please fill in all the connection parameters'));     
      } //if
      if (! $this->active_repository instanceof SourceRepository) {
        $repository_class_name = array_var($_GET, 'engine');
        $this->active_repository = new $repository_class_name();
      } //if
      $this->active_repository->setRepositoryPathUrl(array_var($_GET, 'url'));
      $this->active_repository->setUsername(array_var($_GET, 'user'));
      $this->active_repository->setPassword(array_var($_GET, 'password'));
      $this->active_repository->setType(array_var($_GET, 'engine'));
      
      if (!$this->active_repository->loadEngine()) {
        die(lang('Failed to load repository engine'));
      }//if
      
      if (($error = $this->active_repository->loadEngine()) !== true) {
        die($error);
      } // if
      
      $result = $this->active_repository->testRepositoryConnection();
      if ($result !== true) {
        if ($result === false) {
          die('Please check URL or login parameters.');
        } else {
          echo ($result);
          die();
        } //if
      } else {
        die('ok');
      } // if
    } // test_repository_connection

    /**
     * Async method that changes default branch for the selected user
     */
    function do_change_branch() {
      if($this->request->isAsyncCall()) {
        $branch = $this->request->post('branch');
        if (isset($branch) && $branch) {
          $this->project_object_repository->setDefaultBranch($this->logged_user, $branch);
        } //if
        $this->response->ok();
      } else {
        $this->response->badRequest();
      } //if
    } //change_branch
    
    /**
     * Returns revision number by revision name
     */
    function find_revision_number() {
      $revision_name = array_var($_GET, 'revision_name');
      
      if(!empty($revision_name)) {
        $revision_number = $this->active_repository->getRevisionNumber($revision_name);
        if(is_int($revision_number)) {
          echo "$revision_number";
          die();
        } // if
      } // if
      
      die('false');
    } // find_revision_number
    
  }