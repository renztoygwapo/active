<?php

  /**
   * Source module initialization file
   *
   * @package activeCollab.modules.source
   */

  // module basics
  define('SOURCE_MODULE', 'source');
  define('SOURCE_MODULE_PATH', APPLICATION_PATH . '/modules/source');
  
  // how many logs to get per async update request for SVN
  if (!defined('SVN_SOURCE_MODULE_LOGS_PER_REQUEST')) {
    define('SVN_SOURCE_MODULE_LOGS_PER_REQUEST', 50);
  } // if
  
  // how many logs to get per async update request for Git
  if (!defined('GIT_SOURCE_MODULE_LOGS_PER_REQUEST')) {
    define('GIT_SOURCE_MODULE_LOGS_PER_REQUEST', 50);
  } // if
  
  // how many logs to get per async update request for Mercurial
  if (!defined('MERCURIAL_SOURCE_MODULE_LOGS_PER_REQUEST')) {
    define('MERCURIAL_SOURCE_MODULE_LOGS_PER_REQUEST', 50);
  } // if
  
  // how many commits is the limit for sending detailed notification
  if (!defined('MAX_UPDATED_COMMITS_TO_SEND_DETAILED_NOTIFICATIONS')) {
    define('MAX_UPDATED_COMMITS_TO_SEND_DETAILED_NOTIFICATIONS', 10);
  } // if

  // how many logs to get per async update request for Mercurial
  if (!defined('FILE_COUNT_IN_DIFF_COMMIT')) {
    define('FILE_COUNT_IN_DIFF_COMMIT', 10);
  } // if

  define('SVN_REPOSITORY', 'SvnRepository');
  define('GIT_REPOSITORY', 'GitRepository');
  define('MERCURIAL_REPOSITORY', 'MercurialRepository');
  
  // repository path for git & mercurial
  if (!defined('GIT_FILES_PATH')) {
    define('GIT_FILES_PATH', WORK_PATH . '/git');
  } // if
  
  if (!defined('MERCURIAL_FILES_PATH')) {
    define('MERCURIAL_FILES_PATH', WORK_PATH . '/hg');
  } // if
  
  AngieApplication::useModel(array(
    'commit_project_objects', 
    'source_users', 
    'source_commits',
    'source_repositories', 
    'source_paths', 
  ), SOURCE_MODULE);
  
  AngieApplication::setForAutoload(array(
    'RepositoryCreatedActivityLog'     => SOURCE_MODULE_PATH . '/models/activity_logs/RepositoryCreatedActivityLog.class.php',
    'RepositoryUpdateActivityLog'      => SOURCE_MODULE_PATH . '/models/activity_logs/RepositoryUpdateActivityLog.class.php',
  
  	'ProjectSourceRepository'   => SOURCE_MODULE_PATH.'/models/project_source_repositories/ProjectSourceRepository.class.php',
  	'ProjectSourceRepositories' => SOURCE_MODULE_PATH.'/models/project_source_repositories/ProjectSourceRepositories.class.php',
  
  	'SvnRepository' => SOURCE_MODULE_PATH.'/models/svn/SvnRepository.class.php',
  	'SvnCommit'     => SOURCE_MODULE_PATH.'/models/svn/SvnCommit.class.php',
  	'SvnCommits'    => SOURCE_MODULE_PATH.'/models/svn/SvnCommits.class.php',
  
  	'GitRepository' => SOURCE_MODULE_PATH.'/models/git/GitRepository.class.php',
  	'GitCommit'     => SOURCE_MODULE_PATH.'/models/git/GitCommit.class.php',
  	'GitCommits'    => SOURCE_MODULE_PATH.'/models/git/GitCommits.class.php',
  
    'MercurialRepository' => SOURCE_MODULE_PATH.'/models/mercurial/MercurialRepository.class.php',
  	'MercurialCommit'     => SOURCE_MODULE_PATH.'/models/mercurial/MercurialCommit.class.php',
  	'MercurialCommits'    => SOURCE_MODULE_PATH.'/models/mercurial/MercurialCommits.class.php',
  
  	'SourceSearchIndex'    => SOURCE_MODULE_PATH.'/models/search/SourceSearchIndex.class.php',
  	'ISourceSearchItemImplementation'    => SOURCE_MODULE_PATH.'/models/search/ISourceSearchItemImplementation.class.php',

    'ISourceActivityLogsImplementation' => SOURCE_MODULE_PATH.'/models/ISourceActivityLogsImplementation.class.php',
  
  	'SourceCommitCommitedByInspectorProperty'    => SOURCE_MODULE_PATH.'/models/SourceCommitCommitedByInspectorProperty.class.php',
    'SourceProjectSourceRepositoryBranchInspectorProperty'    => SOURCE_MODULE_PATH.'/models/SourceProjectSourceRepositoryBranchInspectorProperty.class.php',

    //notification
    'NewCommitsNotification'    => SOURCE_MODULE_PATH.'/notifications/NewCommitsNotification.class.php',
  ));
  
  define('REPOSITORY_UPDATE_FREQUENTLY', 1);
  define('REPOSITORY_UPDATE_HOURLY', 2);
  define('REPOSITORY_UPDATE_DAILY', 3);
  // define('REPOSITORY_UPDATE_HOOK', 4);
  
  /**
   * List of update types
   *
   * @param null
   * @return array
   */
  function source_module_update_types() {
    return array(
      REPOSITORY_UPDATE_FREQUENTLY  => lang('Frequently'),
      REPOSITORY_UPDATE_HOURLY      => lang('Hourly'),
      REPOSITORY_UPDATE_DAILY       => lang('Daily'),
      // REPOSITORY_UPDATE_HOOK        => lang('On Commit Hook'),
      );
  } // source module update types
  
  /**
   * Supported source version control systems
   *
   * @return array
   */
  function source_module_types() {
    return array(
      'SvnRepository'       => 'Subversion',
   	  'GitRepository'       => 'Git',
      'MercurialRepository'	=> 'Mercurial'	
    );
  } // source module types
  
  
  /**
   * Get the URL of source module
   *
   * @param Project $project
   * @return string
   */
  function source_module_url(Project $project) {
    return Router::assemble('project_repositories', array('project_slug' => $project->getSlug()));
  } // source module URL
  
  /**
   * Get the URL to add a repository
   *
   * @param Project $project
   * @return string
   */
  function source_module_add_repository_url(Project $project) {
    return Router::assemble('repository_add_new',array('project_slug' => $project->getSlug()));
  } // source_module_add_repository_url
  
  const SOURCE_MODULE_STATE_ADDED = 'A';
  const SOURCE_MODULE_STATE_DELETED = 'D';
  const SOURCE_MODULE_STATE_IGNORED = 'I';
  const SOURCE_MODULE_STATE_UPDATED = 'U';
  const SOURCE_MODULE_STATE_MODIFIED = 'M';
  const SOURCE_MODULE_STATE_REPLACED = 'R';
  const SOURCE_MODULE_STATE_MERGED = 'G';
  const SOURCE_MODULE_STATE_CONFLICTED = 'C';
  const SOURCE_MODULE_STATE_NOT_VERSIONED = '?';
  const SOURCE_MODULE_STATE_MISSING = '!';
  const SOURCE_MODULE_STATE_BE_MOVED = 'A+';
  
  /**
   * Return descriptive Vesrion Control state
   *
   * @param string $code
   * @return string
   */
  function source_module_get_state_label($code, $language = null) {
    
    if(!$language instanceof Language) {
    	$logged_user = Authentication::getLoggedUser();
    	if ($logged_user instanceof User) {
    		$language = $logged_user->getLanguage();
    	} else {
    		$language = Languages::findDefault();
    	} //if
    }//if
    
    $status_codes = array(
      SOURCE_MODULE_STATE_ADDED   => lang('Added',null,null,$language),
      SOURCE_MODULE_STATE_DELETED   => lang('Deleted',null,null,$language),
      SOURCE_MODULE_STATE_IGNORED   => lang('Ignored',null,null,$language),
      SOURCE_MODULE_STATE_MODIFIED   => lang('Modified',null,null,$language),
      SOURCE_MODULE_STATE_UPDATED   => lang('Updated',null,null,$language),
      SOURCE_MODULE_STATE_REPLACED   => lang('Replaced',null,null,$language),
      SOURCE_MODULE_STATE_MERGED   => lang('Merged into working copy',null,null,$language),
      SOURCE_MODULE_STATE_CONFLICTED   => lang('Conflict',null,null,$language),
      SOURCE_MODULE_STATE_NOT_VERSIONED   => lang('Not under version control',null,null,$language),
      SOURCE_MODULE_STATE_MISSING   => lang('Missing or incomplete',null,null,$language),
      SOURCE_MODULE_STATE_BE_MOVED  => lang('Will be moved after commit',null,null,$language),
    );
    
    $keys = array_keys($status_codes);
    
    if (in_array($code, $keys)) {
      return $status_codes[$code];
    } else {
      return lang('Unknown');
    } // if
  } // source_module_get_state_label
  
  /**
   * get status name
   * 
   * @param string $code
   * @return string
   */
  function source_module_get_state_name($code) {
    $status_codes = array(
      SOURCE_MODULE_STATE_ADDED   => 'added',
      SOURCE_MODULE_STATE_DELETED   => 'deleted',
      SOURCE_MODULE_STATE_IGNORED   => 'ignored',
      SOURCE_MODULE_STATE_MODIFIED   => 'modified',
      SOURCE_MODULE_STATE_UPDATED   => 'updated',
      SOURCE_MODULE_STATE_REPLACED   => 'replaced',
      SOURCE_MODULE_STATE_MERGED   => 'merged',
      SOURCE_MODULE_STATE_CONFLICTED   => 'conflict',
      SOURCE_MODULE_STATE_NOT_VERSIONED   => 'not_versioned',
      SOURCE_MODULE_STATE_MISSING   => 'missing',
      SOURCE_MODULE_STATE_BE_MOVED  => 'will_be_moved',
    );
    
    $keys = array_keys($status_codes);
    
    if (in_array($code, $keys)) {
      return $status_codes[$code];
    } else {
      return 'unknown';
    } // if
  } // source_module_get_state_name
  
  /**
   * Get options for file
   * 
   * @param SourceRepository $repository
   * @param SourceCommit $commit
   * @param string $file
   * @return array
   */
  function source_module_get_file_options($repository, $commit, $file) {
		// prepare options for dropdown
		$file_options = new NamedList();
		$file_options->add('history_and_compare', array(
			'text' => lang('History and Compare'),
			'url' => $repository->getFileDialogFormCompareUrl($file),
			'onclick' => new FlyoutFormCallback()
		));
		$file_options->add('download', array(
			'text' => lang('Download'),
			'url' => $repository->getFileDownloadUrl($commit, $file)
		));
		return $file_options->toArray();
  } // source_module_get_file_options

  DataObjectPool::registerTypeLoader('ProjectSourceRepository', function($ids) {
    return ProjectSourceRepositories::findByIds($ids, STATE_TRASHED, VISIBILITY_PRIVATE);
  });