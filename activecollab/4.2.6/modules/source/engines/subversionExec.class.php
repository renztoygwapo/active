<?php
require_once SOURCE_MODULE_PATH.'/engines/RepositoryEngine.class.php';
require_once(ANGIE_PATH.'/classes/xml/xml2array.php');

/**
 * Subversion exec commands library
 *
 * @package activeCollab.modules.source
 * @subpackage version control engines
 * @author Goran Blazin
 */
class SvnExecRepositoryEngine extends RepositoryEngine {

  /**
   * Eeach output from SVN is stored here as an array since exec() always
   * returns output in that form
   *
   * @var array
   */
  public $output = null;

  /**
   * Path to svn executable
   *
   * @var string
   */
  public $executable_path = '';

  /**
   * SVN config dir
   *
   * @var string
   */
  public $config_dir = '';

  /**
   * SVN trust server certificate
   *
   * @var string
   */
  public $trust_server_cert = '';

  /**
   * Contains the information about the ROOT of the SVN repository
   *
   * @var null
   */
  public $svn_repository_root_path;

  /**
   * Class constructor
   *
   * @param ProjectSourceRepository $repository
   * @param int $project_id
   * @param bool $triggered_by_handler
   */
  function __construct($repository, $project_id = null, $triggered_by_handler = false) {
    // check if we have neccessary resources
    if ($repository instanceof ProjectSourceRepository) {
      $this->project_object_repository = $repository;
      $this->active_repository = $repository->source_repository;
    } elseif ($repository instanceof SourceRepository) {
      $this->active_repository = $repository;
    } //if

    $this->executable_path = with_slash(ConfigOptions::getValue('source_svn_path'));

    $config_dir = ConfigOptions::getValue('source_svn_config_dir');
    $this->config_dir = is_null($config_dir) ? '' : '--config-dir '.$config_dir;

    // ssl certificate flag
    $trust_server_cert = ConfigOptions::getValue('source_svn_trust_server_cert');
    $this->trust_server_cert = $trust_server_cert ? '--trust-server-cert' : '';

    $this->svn_repository_root_path = $this->active_repository->getAdditionalProperty('svn_repository_root_path');

    // if we do not have the root of the SVN repository we need to get it
    if (!$this->svn_repository_root_path) {
      $this->setRepositoryRootPath();
    } //if



    $this->triggerred_by_handler = $triggered_by_handler;
  } // __construct

  /**
   * Check if executable exists (if $path is provided system will check if executable exists in that folder, if not it will check system config option)
   *
   * @param string $svn_path
   * @param string $config_dir
   * @return boolean
   */
  static function executableExists($svn_path = null, $config_dir = null) {
    if (!$svn_path) {
      $svn_path = ConfigOptions::getValue('source_svn_path');
    }

    if (!$config_dir) {
      $config_dir = ConfigOptions::getValue('source_svn_config_dir');
    }

    $svn_path = with_slash($svn_path);
    if (is_windows_server()) {
      $config_dir_insert = is_null($config_dir) ? '' : '--config-dir '.escapeshellarg($config_dir);
      exec(escapeshellarg($svn_path) . 'svn --version --quiet '. $config_dir_insert . ' 2>&1', $output);
    } else {
      $config_dir_insert = is_null($config_dir) ? '' : '--config-dir '.$config_dir;
      exec(escapeshellcmd($svn_path . 'svn --version --quiet '.$config_dir_insert). ' 2>&1', $output);
    }
    $output = first($output);
    return (boolean) version_compare($output, '1.0.0', '>') ? true : $output;
  } // executableExists

  /**
   * Test connection by trying to retrieve head revision
   *
   * @param SourceRepository $active_repository
   * @return bool
   */
  static function testRepositoryConnection(SourceRepository $active_repository) {
    // trust server certificate
    $trust_server_cert = ConfigOptions::getValue('source_svn_trust_server_cert') ? '--trust-server-cert' : '';

    $command = "log -r HEAD --xml --verbose $trust_server_cert ".$active_repository->getRepositoryPathUrl();

    if (trim($active_repository->getUsername()) && trim($active_repository->getPassword())) {
      $authentication = '--username '.$active_repository->getUsername().' --password '.$active_repository->getPassword();
    } else {
      $authentication = '';
    } //if

    $executable_path = ConfigOptions::getValue('source_svn_path');
    $executable_path = empty($executable_path) ? '' : with_slash($executable_path);

    $config_dir = ConfigOptions::getValue('source_svn_config_dir');

    if (is_windows_server()) {
      $config_dir_insert = is_null($config_dir) ? '' : '--config-dir '.escapeshellarg($config_dir);
      $escaped = escapeshellarg($executable_path) . "svn " . escapeshellcmd($authentication." --non-interactive ") . $config_dir_insert . escapeshellcmd(" " . $command)." 2>&1";
    } else {
      $config_dir_insert = is_null($config_dir) ? '' : '--config-dir '.$config_dir;
      $escaped = escapeshellcmd($executable_path."svn ".$authentication." --non-interactive ".$config_dir_insert." $command")." 2>&1";
    } //if
    //var_dump($escaped);
    $output = null;
    exec($escaped, $output);

    $error = SvnExecRepositoryEngine::checkResponse($output);
    return is_null($error) ? true : $error->getMessage();
  } // testRepositoryConnection

  /**
   * Check if SVN is usable
   *
   * @return boolean
   */
  static function isUsable() {
    return (extension_loaded ( 'xml' ) && function_exists('xml_parser_create')) ? true : 'XML parser not found!';
  } // isUsable

  /**
   * Get head revision number
   *
   * @param bool $isAsync
   * @return mixed
   */
  function getHeadRevision($isAsync = false) {
    $this->triggerred_by_handler = $isAsync;
    $info = $this->getInfo();
    if (isset($info['revision']) && $info['revision'] !== false) {
      return $info['revision'];
    } else {
      if (!$this->triggerred_by_handler) {
        return false;
      } else {
        if (!is_error($this->error)) {
          $this->error = new Error(lang('Could not obtain the highest revision number for this repository'));
        } //if
      } // if
    } // if
    return false;
  } // getHeadRevision

  /**
   * Check if path is directory
   *
   * @param string $path
   * @param int $revision
   * @param int $peg_revision
   * @param bool $raw_output
   *
   * @return array
   */
  function getInfo($path = '', $revision = null, $peg_revision = null, $raw_output = false) {
    $revision_info = !is_null($revision) ? '-r '.$revision : '';

    if (intval($revision) !== intval($peg_revision)) {
      $peg = !is_null($peg_revision) ? "@".$peg_revision : '';
    } else {
      $peg = '';
    } // if

    $string = 'info '.$revision_info.' '.$this->getRealPath($path).$peg;
    $this->execute($string);

    if (!$this->output) {
      return false;
    } //if
    $info = array();
    if ($raw_output) {
      $info = implode("\n", $this->output);
    } else {
      for ($i = 0; $i < count($this->output); $i++) {
        // get path
        if (!$info['path']) {
          if (strpos($this->output["$i"], 'Path:') !== false) {
            $info['path'] = $this->active_repository->getRepositoryPathUrl().str_replace('Path: ', '', $this->output["$i"]);
          } else {
            $info['path'] = false;
          } // if
        } //if

        // get type of the item
        if (!$info['type']) {
          if (strpos($this->output["$i"], 'Node Kind:') !== false) {
            $info['type'] = str_replace('Node Kind: ', '', $this->output["$i"]);
            if ($info['type'] == 'directory') {
              $info['type'] = 'dir';
            } //if
          } else {
            $info['type'] = false;
          } // if
        } //if

        // get revision
        if (!$info['revision']) {
          if (strpos($this->output["$i"], 'Revision:') !== false) {
            $info['revision'] = str_replace('Revision: ', '', $this->output["$i"]);
          } else {
            $info['revision'] = false;
          } // if
        } //if

        // get revision
        if (!$info['last_edited_revision']) {
          if (strpos($this->output["$i"], 'Last Changed Rev: ') !== false) {
            $info['last_edited_revision'] = str_replace('Last Changed Rev: ', '', $this->output["$i"]);
          } else {
            $info['last_edited_revision'] = false;
          } // if
        } //if
      }//foreach
    } // if
    return $info;
  } // getInfo

  /**
   * Compare one revision of a file to another revision
   *
   * @param string $path
   * @param int $revision_from
   * @param int $revision_to
   * @return string
   */
  function compareToRevision($path, $revision_from, $revision_to) {
    $string = 'diff -r '.$revision_from.':'.$revision_to.' '.$this->getRealPath($path);
    $this->execute($string);
    if ($this->output) {
      return $this->output;
    } else {
      // Code if file does not exist in some of the revisions
      $file_from_data = $this->getFileContent($revision_from, $path, $revision_from);
      $file_to_data = $this->getFileContent($revision_to, $path, $revision_to);
      if ($file_from_data || $file_to_data) {
        $file_from_data = is_null($file_from_data) ? '' : $file_from_data;
        $file_to_data = is_null($file_to_data) ? '' : $file_to_data;
        $temp_diff = array();
        $temp_diff[] = "Index: $path";
        $temp_diff[] = "===================================================================";
        $temp_diff = array_merge($temp_diff, explode("\n",render_diff($file_from_data, $file_to_data, 'unified')));
        return count($temp_diff) > 0 ? $temp_diff : false;
      } else {
        return false;
      } //if
    } // if
  } // compare to revision

  /**
   * Get file content
   *
   * @param Revision $revision
   * @param string $file
   * @return string
   */
  function getFileContent($revision, $path, $last_edited_revision = null) {
    $last_edited_revision = !is_null($last_edited_revision) ? '@'.$last_edited_revision : '';
    $string = 'cat -r '.$revision.' '.$this->getRealPath($path).$last_edited_revision;
    $this->execute($string);
    return $this->output ? implode("\n",$this->output) : false;
  } // getFileContent


  /**
   * Browse repository
   *
   * @param Revision $revision
   * @param string $path
   * @return array
   */
  function browse($revision, $path = '') {
    $string = 'list -r '.$revision.' --xml '.$this->getRealPath($path);
    $this->execute($string);

    $list_data = xml2array(implode("\n",$this->output), 1, array('entry'));

    $list['current_dir'] = $list_data['lists']['list']['attr']['path'];

    $entries = array();
    $dirs = array();
    $files = array();

    $i=0;
    $j=1;
    foreach ($list_data['lists']['list']['entry'] as $entry) {
      // put dirs and files into separate arrays
      if ($entry['attr']['kind'] == 'dir') {
        $dirs[$i]['kind'] = $entry['attr']['kind'];
        $dirs[$i]['name'] = $entry['name']['value'];
        $dirs[$i]['size'] = $entry['size']['value'];
        $dirs[$i]['revision'] = $entry['commit']['attr']['revision'];
        $dirs[$i]['revision_number'] = $entry['commit']['attr']['revision'];
        $dirs[$i]['author'] = $entry['commit']['author']['value'];
        $dirs[$i]['date'] = new DateTimeValue($entry['commit']['date']['value']);
        $dirs[$i]['url_key'] = $j;
        $j++;
      }
      else {
        $files[$i]['kind'] = $entry['attr']['kind'];
        $files[$i]['name'] = $entry['name']['value'];
        $files[$i]['size'] = format_file_size($entry['size']['value']);
        $files[$i]['revision'] = $entry['commit']['attr']['revision'];
        $files[$i]['revision_number'] = $entry['commit']['attr']['revision'];
        $files[$i]['author'] = $entry['commit']['author']['value'];
        $files[$i]['date'] = new DateTimeValue($entry['commit']['date']['value']);
      } //if
      $i++;
    } //foreach

    // merge dirs and files array into one array with each of them sorted by name, but
    // directories go first
    $list['entries'] = array_merge(array_sort_by_key($dirs, 'name'), array_sort_by_key($files, 'name'));

    return $list;

  } // browse repository


  /**
   * Get log data
   *
   * @param integer $revision_to
   * @param mixed $revision_from
   * @return array
   */
  function getLogs($revision_to,  $revision_from = 'HEAD') {

    // get multiple logs or a single one
    if (!is_null($revision_from)) {
      $r = $revision_from.':'.$revision_to;
    }
    else {
      $r = $revision_to;
      $this->triggerred_by_handler = true;
    } // if

    $string = 'log -r '.$r.' --xml --verbose '.$this->active_repository->getRepositoryPathUrl();
    $this->execute($string);

    if ($this->output === false) {
      return false;
    } //if

    $log_data = xml2array(implode("\n", $this->output), 1, array('path', 'logentry'));

    $insert_data = array();
    $i=1;

    // this is because we get commits from SVN sorted from newest to oldest
    $logs = is_array($log_data['log']['logentry']) ? array_reverse($log_data['log']['logentry']) : array();
    $skipped_commits = 0;

    // loop through array of log entries
    foreach ($logs as $key=>$log_entry) {
      // prevent duplicate entries in case when there are two separate update processes
      // (like, both scheduled task and aC user triggered the update)
      if (SourceCommits::count("`repository_id` = '".$this->active_repository->getId()."' AND `revision_number` = '".$log_entry['attr']['revision']."'") > 0) {
        $skipped_commits++;
        continue;
      } // if
      $paths = array();
      $k=0;
      foreach ($log_entry['paths']['path'] as $path) {
        $paths[$k]['path'] = mysql_real_escape_string($path['value']); // paths can contain file names with characters that can break the query
        $paths[$k]['action'] = $path['attr']['action'];
        $k++;
      } // foreach

      $date = new DateTimeValue($log_entry['date']['value']);
      $log_date = $date->getYear()."-".$date->getMonth().'-'.$date->getDay().' '.$date->getHour().':'.$date->getMinute().':'.$date->getSecond();

      $email_for_analyze = "nobody@site.com";
      $message_body = mysql_real_escape_string(SourceCommit::analyze_message($log_entry['msg']['value'], $log_entry['author']['value'], $email_for_analyze, $log_entry['attr']['revision'], $this->active_repository));

      $message_title = first(explode("\n", $message_body));

      $commiter = mysql_real_escape_string($log_entry['author']['value']);

      $insert_data[$key]['paths'] = serialize($paths);

      $insert_data[$key]['commit'] = array(
        'name' 							=> $log_entry['attr']['revision'],
        'type'							=> 'SvnCommit',
        'revision_number'		=> $log_entry['attr']['revision'],
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

    } //foreach

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
   * Execute SVN command
   *
   * @param string $command
   * @return boolean
   */
  function execute($command) {
    $this->output = null;

    if (trim($this->active_repository->getUsername()) && trim($this->active_repository->getPassword())) {
      $authentication = '--username '.$this->active_repository->getUsername().' --password '.$this->active_repository->getPassword();
    } else {
      $authentication = '';
    } //if

    $executable_path = empty($this->executable_path) ? '' : with_slash($this->executable_path);

    $config_dir = ConfigOptions::getValue('source_svn_config_dir');

    if (is_windows_server()) {
      $config_dir_insert = is_null($config_dir) ? '' : '--config-dir '.escapeshellarg($config_dir);
      $escaped = escapeshellarg($executable_path) . "svn " . escapeshellcmd("$authentication --non-interactive $this->trust_server_cert ") . $config_dir_insert . escapeshellcmd(" " . $command)." 2>&1";
    } else {
      $config_dir_insert = is_null($config_dir) ? '' : '--config-dir '.$config_dir;
      $escaped = escapeshellcmd($executable_path."svn $authentication --non-interactive $this->trust_server_cert $config_dir_insert $command")." 2>&1";
    } //if

    exec($escaped, $this->output);

    $error = $this->checkResponse($this->output);

    if (is_error($error)) {
      $this->error = $error;
      $this->output = false;
      return false;
    } // if

    return true;
  } // execute

  /**
   * Check if there are any error messages in SVN response
   *
   * @param mixed $response
   * @return mixed
   */
  function checkResponse($response) {

    if (is_foreachable($response)) {
      foreach ($response as $key => $response_item) {
        if (strpos($response_item, 'command not found') !== false) {
          return new Error(lang('Unable to execute svn command. Please enter path to svn in Admin settings for Source module'));
        } elseif (strpos($response_item, 'Unable to open') !== false) {
          return new Error(isset($response['1']) ? implode('<br/>', $response) : $response['0']);
        } elseif (strpos($response_item, 'Unable to find repository location for') !== false) {
          return new Error($response_item);
        } elseif (strpos($response_item, 'Unable to connect to a repository') !== false) {
          return new Error($response_item);
        } elseif (strpos($response_item, 'is not a working copy') !== false) {
          return new Error($response_item);
        } elseif (strpos($response_item, 'Repository moved permanently') !== false) {
          return new Error($response_item);
        } elseif (strpos($response_item, "Can't connect to host") !== false) {
          return new Error($response_item);
        } elseif (strpos($response_item, 'File not found') !== false) {
          return new Error($response_item);
        } elseif (strpos($response_item, 'authorization failed') !== false) {
          return new Error($response_item);
        } elseif (strpos($response_item, 'No such revision') !== false) {
          return new Error($response_item);
        } elseif (strpos($response_item, 'No such file or directory') !== false) {
          return new Error($response_item);
        } elseif (strpos($response_item, 'is not under version control') !== false) {
          return new Error($response_item);
        } elseif (strpos($response_item, 'permission denied') !== false) {
          return new Error($response_item);
        } elseif (strpos($response_item, 'invalid option') !== false) {
          return new Error($response_item);
        } elseif (strpos($response_item, 'unexpected return value') !== false) {
          return new Error($response_item);
        } elseif (strpos($response_item, "doesn't accept option") !== false) {
          return new Error($response_item);
        } elseif (strpos($response_item, 'non-existent in that revision') !== false) {
          return new Error(lang("Path does not exist in requested revision"));
        } elseif (strpos($response_item, 'Syntax error') !== false) {
          return new Error(lang('Syntax error in Subversion command'));
        } elseif (strpos($response_item, 'Not a valid URL') !== false) {
          return new Error($response_item);
        } elseif (strpos($response_item, 'path not found') !== false) {
          return new Error($response_item);
        } elseif (strpos($response_item, 'refers to a directory') !== false) {
          return new Error(lang('Selected item is a directory'));
        } elseif (strpos($response_item, 'Host not found') !== false) {
          return new Error(lang("Repository hostname not found"));
        } elseif (strpos($response_item, 'could not connect') !== false) {
          return new Error($response_item);
        } elseif (strpos($response_item, 'Server certificate verification failed') !== false) {
          return new Error($response_item);
        } elseif (strpos($response_item, 'path not found') !== false) {
          return new Error(lang("Path not found"));
        } elseif (strpos($response_item, 'Username not recognized') !== false) {
          return new Error($response_item);
        } elseif (strpos($response_item, 'SSL not supported') !== false) {
          return new Error($response_item);
        } elseif (strpos($response_item, 'The system cannot find the path specified.') !== false) {
          return new Error($response_item);
        } elseif (strpos($response_item, 'authorization failed') !== false) {
          return new Error($response_item);
        } elseif (strpos($response_item, 'Unknown command') !== false) {
          return new Error($response_item);
        } elseif (strpos($response_item, 'Error resolving case of') !== false) {
          return new Error($response_item);
        } elseif (strpos($response_item, 'Unrecognized URL scheme') !== false) {
          return new Error($response_item);
        } elseif (strpos($response_item, "request failed") !== false) {
          return new Error(count($response) > 1 ? implode('; ', $response) : $response['0']); // various "request failed" errors that may exist but we're not aware of
        } // if
        if (2 === $key) {
          break;
        } //if
      } //foreach
    } else {
      return new Error(lang("Invalid response received or no response received at all"));
    } // if
  } // check_response

  /**
   * Returns how much will module logs be updated per request for SVN
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
   * Return valid path for file history query
   *
   * @param string $path
   * @return string
   */
  function slashifyForHistoryQuery($path) {
    return $path[0] === "/" ? $path : "/".$path;
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
   * Concatenating fails in that case and this method takes care of that by
   * prepending the repository's root url to the requested path.
   *
   * @param string $path
   * @return string
   */
  function getRealPath($path) {
    $path = str_replace("//", "/", $path); // weird issue that sometimes happens
    $path = str_replace(":/", "://", $path); // make http work again

    return str_replace(' ', '%20', without_slash($this->active_repository->getRepositoryPathUrl()) . $path);
  } // getRealPath

  /**
   * Method that sets the ROOT of the SVN repository in the SvnRepository object.
   * Returns true for successful retrieval of the ROOT, false for otherwise
   *
   * @return bool
   */
  private function setRepositoryRootPath() {
    $this->execute('info --xml ' . $this->active_repository->getRepositoryPathUrl());
    $data = xml2array(implode("\n", $this->output));
    $svn_root_path = $data['info']['entry']['repository']['root']['value'];
    if (is_string($svn_root_path)) {
      try {
        DB::beginWork('Updating repository @ ' . __CLASS__);
        $this->active_repository->setAdditionalProperty('svn_repository_root_path', $svn_root_path);
        $this->svn_repository_root_path = $svn_root_path;
        $this->active_repository->save();
        DB::commit('Repository updated @ ' . __CLASS__);
      } catch (Exception $e) {
        DB::rollback('Failed to update repository @ ' . __CLASS__);
        throw new Exception($e->getMessage());
      } //try
    } //if
  } //getRepositoryRootPath

} // SvnExecRepositoryEngine