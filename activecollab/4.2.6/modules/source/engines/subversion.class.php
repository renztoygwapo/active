<?php
require_once SOURCE_MODULE_PATH.'/engines/RepositoryEngine.class.php';
/**
 * Subversion commands library
 *
 * @package activeCollab.modules.source
 * @subpackage version control engines
 * @author Goran Blazin
 */
 class SvnRepositoryEngine extends RepositoryEngine {

   /**
    * Contains the information about the ROOT of the SVN repository
    *
    * @var null
    */
   public $svn_repository_root_path;

  /**
   * Class constructor
   * 
   * @param ProjectSourceRepository|SourceRepository $repository
   * @param int $project_id
   * @param bool $triggered_by_handler
   */ 
  function __construct($repository,$project_id = null,$triggered_by_handler = false) {
    // check if we have necessary resources
    if ($repository instanceof ProjectSourceRepository) {
      $this->project_object_repository = $repository;
      $this->active_repository = $repository->source_repository;
    } elseif ($repository instanceof SourceRepository) {
    	$this->active_repository = $repository;
    } //if

    svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_USERNAME, $this->active_repository->getUsername());
    svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_PASSWORD, $this->active_repository->getPassword());

    // Important for certificate issues!
    svn_auth_set_parameter(PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS, true);
    svn_auth_set_parameter(SVN_AUTH_PARAM_NON_INTERACTIVE,              true);
    svn_auth_set_parameter(SVN_AUTH_PARAM_NO_AUTH_CACHE,                true);

    $this->svn_repository_root_path = $this->active_repository->getAdditionalProperty('svn_repository_root_path');

    // if we do not have the root of the SVN repository we need to get it
    if (!$this->svn_repository_root_path) {
      $this->setRepositoryRootPath();
    } //if
    
    $this->triggerred_by_handler = $triggered_by_handler;
  } // __construct

  /**
   * Check if SVN is usable
   *
   * @return boolean
   */
  static function isUsable() {
    return extension_loaded ( 'svn' ) ? true : 'Svn extension not found!';
  } // isUsable
  
  /**
   * Test connection by trying to retrieve list of last revision
   *
   * @param SourceRepository $active_repository
   * @return bool
   */
  static function testRepositoryConnection(SourceRepository $active_repository) {
    svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_USERNAME, $active_repository->getUsername());
    svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_PASSWORD, $active_repository->getPassword());

    // Important for certificate issues!
    svn_auth_set_parameter(PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS, true);
    svn_auth_set_parameter(SVN_AUTH_PARAM_NON_INTERACTIVE,              true);
    svn_auth_set_parameter(SVN_AUTH_PARAM_NO_AUTH_CACHE,                true);

    return svn_ls($active_repository->getRepositoryPathUrl()) ? true : 'Please check your parameters or make sure that repository URL is trusted';
  } //testRepositoryConnection
  
  /**
   * Get head revision number
   *
   * @param boolean
   * @return integer
   */
  function getHeadRevision($isAsync = false) {
    $this->triggerred_by_handler = $isAsync;
    $info = svn_ls($this->active_repository->getRepositoryPathUrl());
    $head_rev = -1;
    foreach($info as $element) {
      if ($element['created_rev'] > $head_rev) {
        $head_rev = $element['created_rev'];
      } //if
    } // foreach
    if ($head_rev > -1) {
        return $head_rev;
    } else {
      $error_message = lang('Could not obtain the highest revision number for this repository');
      if (!$this->triggerred_by_handler) {
        return false;
      } else {
        $this->error = $error_message;
      } // if
    } // if
    
  }//getHeadRevision
  
  /**
   * Returns information (revision,path and type) of repository URL with secified revision (gets HEAD revision for default)
   *
   * @param string $path
   * @param int $revision
   * @return array $info
   */
  function getInfo($path = null, $revision = SVN_REVISION_HEAD) {
    $path = $this->getRealPath($path);
    $real_path = ($path) ? $this->active_repository->getRepositoryPathUrl().$path : $this->active_repository->getRepositoryPathUrl();
    $ls_info = svn_ls($real_path,$revision);

    if ($ls_info === false) {
      return false;
    } //if
    $info = array();
    $info['path'] = '/'.str_replace($this->active_repository->getRepositoryPathUrl(),'',$path);
    if (count($ls_info) !== 1 OR $info['path'] === '/') {
      $info['type'] = 'dir';
    } else {
      $path_array = array_values(array_filter(explode('/',$info['path'])));
      $parent = '/';
      for ($i = 0; $i < count($path_array) -1; $i++) {
        $parent .= $path_array[$i].'/';
      } //for
      $ls_parent_info = svn_ls($this->active_repository->getRepositoryPathUrl().$parent,$revision);
      foreach ($ls_parent_info as $key => $item) {
        if ($key == end($path_array)) {
          $info['type'] = $item['type'];
          $info['last_edited_revision'] = $item['created_rev'];
          break;
        } //if
      } //foreach
    } //if

    return $info;
  } //getInfo
  
  /**
   * Get log data
   *
   * @param integer $revision_to
   * @param mixed $revision_from
   * @return array
   */
  function getLogs($revision_to, $revision_from = SVN_REVISION_HEAD) {
    // get multiple logs or a single one
    if (is_null($revision_from)) {
      $log_data = svn_log($this->active_repository->getRepositoryPathUrl(),$revision_to);
      $this->triggerred_by_handler = true;
    } else {
      $log_data = svn_log($this->active_repository->getRepositoryPathUrl(),$revision_from,$revision_to);
    } //if
    
    $insert_data = array();
    
    $i=1;
    // this is because we get commits from SVN sorted from newest to oldest
    $logs = is_array($log_data) ? array_reverse($log_data) : array();
    $skipped_commits = 0;
    

    // loop through array of log entries
    foreach ($logs as $key=>$log_entry) {
      
      // prevent duplicate entries in case when there are two separate update processes
      // (like, both scheduled task and aC user triggered the update)
      if (SourceCommits::count("`repository_id` = '".$this->active_repository->getId()."' AND `revision_number` = '".$log_entry['rev']."'") > 0) {
      	$skipped_commits++;
        continue;
      } // if
      $paths = array();
      $k = 0;
      foreach ($log_entry['paths'] as $path) {
        $paths[$k]['path'] = mysql_real_escape_string($path['path']); // paths can contain file names with characters that can break the query
        $paths[$k]['action'] = $path['action'];
        $k++;
      } // foreach
      $date = new DateTimeValue($log_entry['date']);
      $log_date = $date->getYear()."-".$date->getMonth().'-'.$date->getDay().' '.$date->getHour().':'.$date->getMinute().':'.$date->getSecond();
      
      $email_for_analyze = "nobody@site.com";
      $message_body = mysql_real_escape_string(SourceCommit::analyze_message($log_entry['msg'], $log_entry['author'], $email_for_analyze, $log_entry['rev'], $this->active_repository));

      $message_title = first(explode("\n", $message_body));
      $commiter = mysql_real_escape_string($log_entry['author']);
      
      $insert_data[$key]['paths'] = serialize($paths);
      
      $insert_data[$key]['commit'] = array(
          	'name' 							=> $log_entry['rev'],
          	'type'							=> 'SvnCommit',
          	'revision_number'		=> $log_entry['rev'],
          	'repository_id'			=> $this->active_repository->getId(),
          	'message_title'			=> $message_title,
          	'message_body'			=> $message_body,
          	'authored_on'				=> null,
          	'authored_by_name'	=> null,
          	'authored_by_email'	=> null,
          	'commited_on'				=> $log_date,
          	'commited_by_name'	=> $commiter,
          	'commited_by_email'	=> null,	
      );
      
    } // foreach
    return array('data'=>$insert_data, 'total'=>count($logs), 'skipped_commits' => $skipped_commits);
  } // get logs
  
  /**
   * Update async
   *
   * @param mixed $revision_to
   * @param mixed $revision_from
   * @return array
   */
    function update($revision, $head_revision,$latest_revision) {
      $difference = $head_revision - $latest_revision;
      $more_logs = true;
      if ($difference > SVN_SOURCE_MODULE_LOGS_PER_REQUEST) {
        $revision_from = ($revision + SVN_SOURCE_MODULE_LOGS_PER_REQUEST)-1;
      } else {
        $revision_from = $head_revision;
        $more_logs = false;
      } // if

      $logs = $this->getLogs($revision,$revision_from);
      if (is_null($this->error)) {
        $this->active_repository->update($logs['data'], $this->active_branch);
        $return_array = array (
         	'finished' => $more_logs ? 0 : 1,
         	'skipped_commits' => $logs['skipped_commits']
        );
        die(json_encode($return_array));
      } else {
        $return = json_encode(array(
          'error' => true,
          'message' => $this->error->getMessage()
        ));
        die($return);
      } // if
    } //update
  
  /**
   * Get file content
   *
   * @param Revision $revision
   * @param string $file
   * @return string
   */
  function getFileContent($revision, $path, $last_edited_revision = null) {
    $path = $this->getRealPath($path);
    $last_edited_revision = !is_null($last_edited_revision) ? '@'.$last_edited_revision : '';
    $real_path = $this->active_repository->getRepositoryPathUrl().$path.$last_edited_revision;
    $file_content = svn_cat($real_path,$revision);
    return $file_content ? $file_content : false;
  } //getFileContent
  
  /**
   * Browse repository
   *
   * @param Revision $revision
   * @param string $path
   * @return array 
   */
  function browse($revision, $path = '') {
    $path = $this->getRealPath($path);
    $real_path = $this->active_repository->getRepositoryPathUrl().$path;
    $list_data = svn_ls($real_path,$revision);

    $list['current_dir'] = $path;
    $dirs = array();
    $files = array();

    $i=0;
    $j=1;
    foreach ($list_data as $key => $entry) {
      // put dirs and files into separate arrays
      if ($entry['type'] == 'dir') {
        $dirs[$i]['kind'] = $entry['type'];
        $dirs[$i]['name'] = $entry['name'];
        $dirs[$i]['size'] = $entry['size'];
        $dirs[$i]['revision'] = $entry['created_rev'];
        $dirs[$i]['revision_number'] = $entry['created_rev'];
        $dirs[$i]['author'] = $entry['last_author'];
        $dirs[$i]['date'] = new DateTimeValue($entry['time_t']);
        $dirs[$i]['url_key'] = $j;
        $j++;
      }
      else {
        $files[$i]['kind'] = $entry['type'];
        $files[$i]['name'] = $entry['name'];
        $files[$i]['size'] = format_file_size($entry['size']);
        $files[$i]['revision'] = $entry['created_rev'];
        $files[$i]['revision_number'] = $entry['created_rev'];
        $files[$i]['author'] = $entry['last_author'];
        $files[$i]['date'] = new DateTimeValue($entry['time_t']);
      } //if
      $i++;
    } //foreach
    // merge dirs and files array into one array with each of them sorted by name, but
    // directories go first
    $list['entries'] = array_merge(array_sort_by_key($dirs, 'name'), array_sort_by_key($files, 'name'));
    return $list;

  } // browse repository
  
  /**
   * Compare one revision of a file to another revision
   *
   * @param string $path
   * @param int $revision_from
   * @param int $revision_to
   * @return string
   */
  function compareToRevision($path = '', $revision_from, $revision_to) {
    $path = $this->getRealPath($path);
    $real_path = $this->active_repository->getRepositoryPathUrl().$path;
    $diff = svn_diff($real_path,$revision_from,$real_path,$revision_to);
    if ($diff) {
      return explode("\n",stream_get_contents($diff[0]));
    } else {
      // Code if file does not exist in some of the revisions
    	$file_from_data = $this->getFileContent($revision_from, $path, $revision_from);
    	$file_to_data = $this->getFileContent($revision_to, $path, $revision_to);
    	if ($file_from_data || $file_to_data) {
	      $file_from_data = ($file_from_data === false) ? '' : $file_from_data;
	      $file_to_data = ($file_to_data === false) ? '' : $file_to_data;
	      $temp_diff = array();
        $temp_diff[] = "Index: $path";
        $temp_diff[] = "===================================================================";
        $temp_diff = array_merge($temp_diff, explode("\n",render_diff($file_from_data, $file_to_data, 'unified')));
        return count($temp_diff) > 0 ? $temp_diff : false;
    	} else {
    	  return false;
    	} //if
    } //if
  }//compareToRevision
  
  /**
   * Retruns how much will module logs be updated per request for SVN
   *
   * @return int
   */
  function getModuleLogsPerRequest() {
    return SVN_SOURCE_MODULE_LOGS_PER_REQUEST;
  } //getModuleLogsPerRequest
  
  /**
   * Return latest previous revision
   *
   * @param int $revision
   * @return int
   */
  function previousRevision($revision) {
    return ($revision - 1);
  } //previousRevision
  
  /**
   * Return valid path for file history query.
   * If full path is provided the function will return full path from the beginning of the repository root.
   *
   * @param string $path
   * @param bool $full_path
   * @return string
   */
  function slashifyForHistoryQuery($path, $full_path = false) {
    $path = $path[0] === "/" ? $path : "/".$path;
    if ($full_path) {
      $path = without_slash($this->active_repository->getRepositoryPathUrl()) . $path;
      $path = str_replace(($this->svn_repository_root_path), '', $path);
    } //if
    $path = str_replace("//", "/", $path); // weird issue that sometimes happens
    return $path;
  } //slashifyForHistoryQuery
  
  /**
   * Returns a starting number of Repository
   * 
   * @return int
   */
  function getZeroRevision() {
    return 1;
  } //getZeroRevision
  
  /**
   * Paths returned at the logs are relative to repository's ROOT url and
   * the repository needs to be queried with such paths, which makes the mess
   * in case that repository's root url added to activeCollab is actually
   * pointing to a subdirectory.
   * 
   * Contacatenating fails in that case and this method takes care of that by
   * prepending the repository's root url to the requested path.
   *
   * @param string $path
   * @return string
   */
  function getRealPath($path) {
    $path = str_replace("//", "/", $path); // weird issue that sometimes happens
    $path = str_replace(":/", "://", $path); // make http work again

    $path = str_replace(' ', '%20', without_slash($path));

    $subfolder_path = str_replace(without_slash($this->svn_repository_root_path), '', with_slash($this->active_repository->getRepositoryPathUrl()));

    $pos = strpos($path, $subfolder_path);
    if (is_int($pos)) {
      $real_path = substr($path, $pos + strlen($subfolder_path));
    } else {
      $real_path = $path;
    } //if

    return $real_path;
  } // getRealPath


   /**
    * Method that sets the ROOT of the SVN repository in the SvnRepository object.
    * Returns true for successful retrieval of the ROOT, false for otherwise
    *
    * @throw Exception $e
    * @return bool
    */
   private function setRepositoryRootPath() {
     $svn_info = svn_info($this->active_repository->getRepositoryPathUrl(),false);
     if (is_string($svn_info[0]['repos'])) {
       try {
         DB::beginWork('Updating repository @ ' . __CLASS__);
         $this->active_repository->setAdditionalProperty('svn_repository_root_path', $svn_info[0]['repos']);
         $this->svn_repository_root_path = $svn_info[0]['repos'];
         $this->active_repository->save();
         DB::commit('Repository updated @ ' . __CLASS__);
       } catch (Exception $e) {
         DB::rollback('Failed to update repository @ ' . __CLASS__);
         throw new Exception($e->getMessage());
       } //try
     } //if
     return true;
   } //getRepositoryRootPath

} // RepositoryEngine