<?php

  require_once SOURCE_MODULE_PATH.'/engines/RepositoryEngine.class.php';
  require_once(ANGIE_PATH.'/classes/xml/xml2array.php');
  /**
   * Mercurial commands library
   *
   * @package activeCollab.modules.source
   * @subpackage version control engines
   * @author Goran Blazin
   */
  class MercurialRepositoryEngine extends RepositoryEngine {

    /**
     * Action is triggered by handler
     *
     * @var boolean
     */
    public $triggerred_by_handler = false;

      /**
     * Eeach output from HG is stored here as an array since exec() always
     * returns output in that form
     *
     * @var array
     */
    public $output = null;

    /**
     * Path to hg executable
     *
     * @var string
     */
    public $executable_path = '';

    /**
     * Class constructor
     *
     * @param ProjectSourceRepository $project_object_repository
     * @param int $project_id
     *
     */
    function __construct($repository, $project_id = null, $triggered_by_handler = false) {

      // check if we have neccessary resources
        if ($repository instanceof ProjectSourceRepository) {
          $this->project_object_repository = $repository;
          $this->active_repository = $repository->source_repository;
        } elseif ($repository instanceof SourceRepository) {
        	$this->active_repository = $repository;
        } //if

      $this->executable_path = with_slash(ConfigOptions::getValue('source_mercurial_path'));
      $this->triggerred_by_handler = $triggered_by_handler;


    } // __construct

      /**
     * See if the version control is usable
     *
     * @param void
     * @return boolean
     */
    static function isUsable() {
      return (extension_loaded ( 'xml' ) && function_exists('xml_parser_create')) ? true : 'XML parser not found!';
    } //isUsable

    /**
     * Test connection to the repository
     *
     * @param SourceRepository $active_repository
     * @return boolean
     */
    static function testRepositoryConnection(SourceRepository $active_repository) {
      $command = 'log -l 1 --cwd '. with_slash(MERCURIAL_FILES_PATH) . $active_repository->getRepositoryPathUrl();

      $executable_path = ConfigOptions::getValue('source_mercurial_path');
      $executable_path = empty($executable_path) ? '' : with_slash($executable_path);

      $escaped = escapeshellcmd($executable_path."hg $command")." 2>&1";

      $output = null;

      exec($escaped, $output);
      $output = self::letsTrustEveryone($output);
      $error = MercurialRepositoryEngine::checkResponse($output);
      return is_null($error) ? true : 'Please check your parameters';
    } //testRepositoryConnection

    /**
   * Check if executable exists (if $path is provided system will check if executable exists in that folder, if not it will check system config option
   *
   * @param string $path
   * @return boolean
   */
  static function executableExists($path = null) {
    $mercurial_path = '';
    if (!$path) {
      $mercurial_path = ConfigOptions::getValue('source_mercurial_path');
    } else {
      $mercurial_path = $path;
    } // if

    $mercurial_path = with_slash($mercurial_path);
    exec(escapeshellcmd($mercurial_path . 'hg --version --quiet'). " 2>&1", $output);
    $output = first($output);
    $position = strpos($output, "version");
    $version = str_replace("version ","",substr($output,$position,13));
    if ((boolean) version_compare($version, '1.0.0', '>')) {
      return true;
    } else {
      return $output;
    } // if
  } // if

    /**
     * Get head revision
     *
     * @param boolean
     * @return mixed
     */
    function getHeadRevision($isAsync = false) {
      $this->triggerred_by_handler = $isAsync;
      $command = 'log -l 1 -b "' . $this->active_branch . '" --removed';
      $this->execute($command);

      $temp = explode(':',$this->output[0]);

      $head_revision = trim($temp[1]);


      if (isset($head_revision) && $head_revision !== false && !is_error($this->error)) {
        return intval($head_revision);
      } else {
        $error_message = lang('Could not obtain the highest revision number for this repository').' : '.$this->error->getMessage();
        if (!$this->triggerred_by_handler) {
          return false;
        } else {
          $this->error = $error_message;
          return false;
        } // if
      } // if
    } //getHeadRevision

    /**
     * Gets information of repository URL with secified revision (gets HEAD revision for default)
     *
     * @param string $path
     * @param mixed $revision
     * @return array $info
     */
    function getInfo($path = '', $revision = null) {
      $path = $this->getRealPath($this->slashifyForHistoryQuery($path));
      $info = array();
      $info['path'] = $path;
      $revision = is_null($revision) ? $this->getHeadRevision() : $revision;
      $rev_string = ($revision == $this->getHeadRevision()) ? "" : "-r $revision";
      $command = "locate $path $rev_string";
      $this->execute($command);
      if (is_array($this->output) && ($this->output[0] === $path)) {
        $info['type'] = 'file';
        //getting last edited changeset
        $command = "log $path --style=xml";
        $this->execute($command);
        $log_xml = xml2array(implode("\n", $this->output));
        $logentry_array = $log_xml['log']['logentry']['attr'] ? array($log_xml['log']['logentry']) : $log_xml['log']['logentry'];
        $logentry = $logentry_array[count($logentry_array)-1];
        foreach ($logentry_array as $logentry_temp) {
         if ($logentry_temp['attr']['revision'] <= $revision &&  $logentry_temp['attr']['revision'] > $logentry['attr']['revision']) {
         	$logentry = $logentry_temp;
         } //if
         if ($logentry['attr']['revision'] == $revision) {
         	 break;
         } //if
        } //foreach
        $info['last_edited_revision_number'] = $logentry['attr']['revision'];
        $info['last_edited_revision_hex_name'] = $logentry['attr']['node'];
        $info['author'] = $logentry['author']['value'];
        $info['date'] = $this->getUMTDate($logentry['date']['value']);
        //getting size
        $info['size'] = strlen($this->getFileContent($revision,$path));
      } else {
         $command = "locate $path** $rev_string";
         $this->execute($command);
         if (is_array($this->output)) {
           $info['type'] = 'dir';
         } else {
           return false;
         } //if
      } //if
      return $info;

    } //getInfo

    /**
   * Get log data
   *
   * @param mixed $revision_to
   * @param mixed $revision_from
   * @return array
   */
    function getLogs($revision_to, $revision_from) {
      // get multiple logs or a single one
      if (!is_null($revision_from)) {
        $r = $revision_from.':'.$revision_to;
      }
      else {
        $r = $revision_to;
        $this->triggerred_by_handler = true;
      } // if

      $command = 'log -r ' . $r . ' -b "' . $this->active_branch . '" -v --removed --style=xml';

      $this->execute($command);

      if ($this->output === false) {
        return false;
      } //if

      $log_data = xml2array(implode("\n", $this->output), 1, array('path', 'logentry'));

      $insert_data = array();
      $i=1;
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
          $paths[$k]['action'] = $path['attr']['action'] === "R" ? "D" : $path['attr']['action'];
          $k++;
        } // foreach

        $date = new DateTimeValue($this->getUMTDate($log_entry['date']['value']));
        $log_date = $date->getYear()."-".$date->getMonth().'-'.$date->getDay().' '.$date->getHour().':'.$date->getMinute().':'.$date->getSecond();

        $email_for_analyze = is_valid_email(mysql_real_escape_string($log_entry['author']['attr']['email'])) ? $log_entry['author']['attr']['email'] : "nobody@site.com";
        $message_body = mysql_real_escape_string(SourceCommit::analyze_message($log_entry['msg']['value'], $log_entry['author']['value'], $email_for_analyze, $log_entry['attr']['revision'], $this->active_repository, $this->active_branch));

        $message_title = first(explode("\n", $message_body));

        $commiter = mysql_real_escape_string($log_entry['author']['value']);
        $created_by_email = mysql_real_escape_string(is_valid_email(mysql_real_escape_string($log_entry['author']['attr']['email'])) ? $log_entry['author']['attr']['email'] : "");

        SourceCommit::analyze_email($this->active_repository->getId(),$commiter,$created_by_email);

        $insert_data[$key]['paths'] = serialize($paths);

        $insert_data[$key]['commit'] = array(
          	'name' 							=> $log_entry['attr']['node'],
          	'type'							=> 'MercurialCommit',
          	'revision_number'		=> $log_entry['attr']['revision'],
          	'repository_id'			=> $this->active_repository->getId(),
          	'message_title'			=> $message_title,
          	'message_body'			=> $message_body,
          	'authored_on'				=> null,
          	'authored_by_name'	=> null,
          	'authored_by_email'	=> null,
          	'commited_on'				=> $log_date,
          	'commited_by_name'	=> $commiter,
          	'commited_by_email'	=> $created_by_email,
        );
      } //foreach

    return array('data'=>$insert_data, 'total'=>count($logs), 'skipped_commits' => $skipped_commits);
    } //getLogs

   /**
   * Update asycn
   *
   * @param mixed $revision_to
   * @param mixed $revision_from
   * @return array
   */
    function update($revision, $head_revision,$latest_revision) {
      $difference = $head_revision - $latest_revision;
      $more_logs = true;
      if ($difference > MERCURIAL_SOURCE_MODULE_LOGS_PER_REQUEST) {
        $revision_from = ($revision + MERCURIAL_SOURCE_MODULE_LOGS_PER_REQUEST)-1;
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
     * @param mixed $revision
     * @param string $file
     * @return string
     */
    function getFileContent($revision, $path) {
      $path = $this->slashifyForHistoryQuery($path);
      $command = 'cat -r '.$revision.' '.$this->getRealPath($path);
      $this->execute($command);
      return $this->output ? implode("\n",$this->output) : false;
    } //getFileContent

    /**
     * Browse repository
     *
     * @param mixed $revision
     * @param string $path
     * @return array
     */
    function browse($revision = null, $path = '') {
      $path = $this->slashifyForHistoryQuery($this->getRealPath($path),true);
      $rev_string = (is_null($revision) || $revision == $this->getHeadRevision()) ? "" : "-r $revision";
      $command = "locate \"$path**\" $rev_string";
      $this->execute($command,false);
      $paths = array();
      foreach ($this->output as $whole_path) {
        $small_path = str_replace($path,"",$whole_path);
        $small_path_array = explode("/",$small_path);
        $small_path = $small_path_array[0];
        if (!is_null($small_path_array[1])) {
          $small_path .= "/";
        }
        if (!in_array($small_path,$paths)) {
         $paths[] = $small_path;
        }
      } //foreach
      $list['current_dir'] = $path;
      $dirs = array();
      $files = array();
      $i=0;
      $j=1;
      foreach ($paths as $sub_item) {
        // put dirs and files into separate arrays
        $info = $this->getInfo($path.$sub_item, $revision);
        $sub_module = false;
        if ($info === false) {
            $info = $this->getInfo();
            $sub_module = true;
        }
        if ($info['type'] === "dir") {
          $dirs[$i]['kind'] = 'dir';
          $dirs[$i]['name'] = substr($sub_item,0,strlen($sub_item)-1);
          $dirs[$i]['size'] = $sub_module ? lang('Submodule') : lang('Folder');
          $dirs[$i]['revision'] = "N/A";
          $dirs[$i]['revision_number'] = "N/A";
          $dirs[$i]['author'] = "N/A";
          $dirs[$i]['date'] = false;
          $dirs[$i]['url_key'] = $j;
          $j++;
        } else {
          $files[$i]['kind'] = 'file';
          $files[$i]['name'] = $sub_item;
          $files[$i]['size'] = format_file_size($info['size']);
          $files[$i]['revision'] = $info['last_edited_revision_hex_name'];
          $files[$i]['revision_number'] = $info['last_edited_revision_number'];
          $files[$i]['author'] = $info['author'];
          $files[$i]['date'] = new DateTimeValue($info['date']);
        } //if
        $i++;
      } //foreach

      // merge dirs and files array into one array with each of them sorted by name, but
      // directories go first
      $list['entries'] = array_merge(array_sort_by_key($dirs, 'name'), array_sort_by_key($files, 'name'));
      return $list;
    } //browse

    /**
     * Compare one revision of a file to another revision
     *
     * @param string $path
     * @param int $revision_from
     * @param int $revision_to
     * @return string
     */
    function compareToRevision($path, $revision_from, $revision_to) {
      if (!is_null($path)) {
        // for some reason when comparing two revision for single files they need to be reverse so it would be like on BitBucket
        list($revision_from,$revision_to) = array($revision_to,$revision_from);
      } //if
      $string = 'diff -r '.$revision_from.' -r '.$revision_to.' '.$this->slashifyForHistoryQuery($this->getRealPath($path));
      $this->execute($string);
      // changing output for unified diff string
      foreach ($this->output as $key => $diff_line) {
        if (substr_compare($diff_line,"diff",0,4) === 0) {
          $line_array = explode(" ",$diff_line);
          $this->output[$key] = "Index: ". $line_array[count($line_array) - 1];

          // do not show diffs bigger than 5 MB
          if (strlen(serialize($this->output)) > 5242880) {
            break;
          } //if
        } //if
      } //foreach
      return $this->output;
    } //compareToRevision

    /**
     * Retruns how much will module logs be updated per request
     *
     * @return int
     */
    function getModuleLogsPerRequest() {
      return MERCURIAL_SOURCE_MODULE_LOGS_PER_REQUEST;
    } //getModuleLogsPerRequest

    /**
     * Return valid path for file history query
     *
     * @param string $path
     * @param booelan $dir
     * @return string
     */
    function slashifyForHistoryQuery($path, $dir = false) {
      if (str_starts_with($path,"/")) {
        $path = substr($path,1);
      } //if
      if ($dir && !str_ends_with($path,"/") && $path!="") {
        $path = $path."/";
      }
      return $path;

    } //slashifyForHistoryQuery

    /**
     * Return latest previous revision
     *
     * @param int $revision
     * @return array()
     */
    function previousRevision($revision) {
    	$command = "parents -r $revision --style=xml";
    	$this->execute($command);
    	$parents_xml = xml2array(implode("\n", $this->output));
      if ($parents_xml) {
        return ($parents_xml['log']['logentry']['attr']['revision']) ? $parents_xml['log']['logentry']['attr']['revision'] : $parents_xml['log']['logentry'][0]['attr']['revision'];
      } else {
        return 0;
      } //if
    } //previousRevision

    /**
     * Returns a starting number of Repository
     *
     * @return int
     */
    function getZeroRevision() {
      $command = 'log -q -b "' . $this->active_branch . '" --removed';
      $this->execute($command);
      if ($this->output === false) {
        return false;
      } else {
        $first_commit = explode(':',array_pop($this->output));
        return $first_commit[0];
      } //if
    } //getZeroRevision

    /**
     * Returns all branches from repository
     *
     * @return array;
     */
    function getBranches() {
      $command = "branches -q";
      $this->execute($command);
      return $this->output;

    } //getBranches

    /**
     * Check if there are any error messages in Mercurial response
     *
     * @param array $response
     * @return mixed
     */
      private function checkResponse($response) {
        if (is_foreachable($response)) {
          foreach ($response as $key => $response_item) {
          if (strpos($response_item, 'command not found') !== false) {
            return new Error(lang('Unable to execute hg command. Please enter path to mercurial in Admin settings for Source module'));
          } elseif (strpos($response_item, 'command not found') !== false) {
            return new Error($response_item);
          } elseif (strpos($response_item, 'command not found') !== false) {
            return new Error($response_item);
          } elseif (strpos($response_item, 'command not found') !== false) {
            return new Error($response_item);
          } elseif (strpos($response_item, 'command not found') !== false) {
            return new Error($response_item);
          } elseif (strpos($response_item, 'command not found') !== false) {
            return new Error($response_item);
          } elseif (strpos($response_item, 'abort: style not found: xml') !== false) {
            return new Error($response_item);
          } elseif (strpos($response_item, 'abort: no repository found') !== false) {
            return new Error($response_item);
          } elseif (strpos($response_item, 'unknown revision') !== false) {
            return new Error($response_item);
          } elseif (strpos($response_item, 'syntax error') !== false) {
            return new Error($response_item);
          } elseif (strpos($response_item, 'no such file in rev') !== false) {
            return new Error($response_item);
          } elseif (strpos($response_item, 'not found in manifest') !== false) {
            return new Error($response_item);
          } elseif (strpos($response_item, 'Not trusting file') !== false) {
            return new Error($response_item);
          } elseif (strpos($response_item, 'not trusting file') !== false) {
            return new Error($response_item);
          } elseif (strpos($response_item, 'There is no Mercurial repository here') !== false) {
            return new Error($response_item);
          } elseif (strpos($response_item, 'File not found') !== false) {
            return new Error($response_item);
          } elseif (strpos($response_item, 'authorization failed') !== false) {
            return new Error($response_item);
          } elseif (strpos($response_item, 'No such file or directory') !== false) {
            return new Error($response_item);
          } elseif (strpos($response_item, 'permission denied') !== false) {
            return new Error($response_item);
          } elseif (strpos($response_item, 'Syntax error') !== false) {
            return new Error($response_item);
          } elseif (strpos($response_item, 'path not found') !== false) {
            return new Error($response_item);
          } elseif (strpos($response_item, 'path not found') !== false) {
            return new Error($response_item);
          } elseif (strpos($response_item, 'The system cannot find the path specified.') !== false) {
            return new Error($response_item);
          } elseif (strpos($response_item, 'Unknown command') !== false) {
            return new Error($response_item);
          } elseif (strpos($response_item, "request failed") !== false) {
            return new Error(count($response) > 1 ? implode('; ', $response) : $response['0']); // various "request failed" errors that may exist but we're not aware of
          } // if
          if (3 === $key) {
            break;
          } //if
        } //foreach
      } else {
        return new Error(lang("Invalid response received or no response received at all"));
      } // if
    }  //checkResponse

    /**
    * Execute HG command. If $scape is true, it will escape it
    *
    * @param string $command
    * @param boolean $escape
    * @return boolean
    */
    function execute($command, $escape = true) {
      $this->output = null;

      $command = "$command --cwd ". with_slash(MERCURIAL_FILES_PATH) . $this->active_repository->getRepositoryPathUrl();

      $executable_path = ConfigOptions::getValue('source_mercurial_path');
      $executable_path = empty($executable_path) ? '' : with_slash($executable_path);
      $escaped = $escape === true ? escapeshellcmd($executable_path."hg $command") : ($executable_path."hg $command");

      $escaped = $escaped." 2>&1";
      exec($escaped, $this->output);
      $this->output = self::letsTrustEveryone($this->output);
      $error = $this->checkResponse($this->output);

      if (is_error($error)) {
        $this->error = $error;
        $this->output = false;
        return false;
      } // if

      return true;
    } // execute

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

      return $path;
    } // getRealPath

    /**
     * Cuts offset and returns basic UMT date from variable which contins ISO 8601 date value
     *
     * @param string $date
     */

    private function getUMTDate($date) {
    	return substr($date, 0, -6);
    } //getUMTDate

    /**
     * Function that erases messages about trust issues
     *
     * @param array $output
     * @return array
     */
    private static function letsTrustEveryone($output) {
      if (is_array($output)) {
        foreach ($output as $key => $output_item)  {
          if (strpos(strtolower($output_item), 'not trusting file') !== false) {
            unset($output[$key]);
          } //if
        } //foreach
        $output = array_values($output);
      } //if
      return $output;
    } //letsTrustEveryone


  } //MercurialRepositoryEngine