<?php

/**
 * Abstract RepositoryEngine class
 *
 * @package activeCollab.modules.source
 * @subpackage version control engines
 * @author Goran Blazin
 */

 abstract class RepositoryEngine {
 	
     /**
     * Action is triggered by handler if it is async request
     *
     * @var boolean
     */
    public $triggerred_by_handler = false;
    
    /**
     * Error message
     *
     * @var Error
     */
    public $error = null;
    
    /**
     * Root path of the repository
     *
     * @var string
     */
    public $root_path = null;
    
    /**
     * Active repository
     *
     * @var SourceRepository
     */
    public $active_repository = null;
    
    /**
     * Project object repository
     *
     * @var ProjectSourceRepository
     */
    public $project_object_repository = null;
    
    /**
     * Active project
     *
     * @var Project
     */
    public $active_project = null;

   /**
     * Active branch
     *
     * @var string or null
     */
    public $active_branch = null;

   /**
     * Get head revision
     *
     * @param boolean
     * @return mixed
     */
    abstract function getHeadRevision($isAsync = false);
    
    /**
     * Gets information of repository URL with secified revision (gets HEAD revision for default)
     *
     * @param string $path
     * @param mixed $revision
     * @return array $info
     */
    abstract function getInfo($path, $revision);
    
    /**
   * Get log data
   *
   * @param mixed $revision_to
   * @param mixed $revision_from
   * @return array
   */
    abstract function getLogs($revision_to, $revision_from);
    
   /**
   * Update asycn
   *
   * @param mixed $revision_to
   * @param mixed $revision_from
   * @return array
   */
    abstract function update($revision, $head_revision,$latest_revision);
    
    /**
     * Get file content
     *
     * @param mixed $revision
     * @param string $file
     * @return string
     */
    abstract function getFileContent($revision, $path);
    
    /**
     * Browse repository
     *
     * @param mixed $revision
     * @param string $path
     * @return array 
     */
    abstract function browse($revision, $path = '');
    
    /**
     * Compare one revision of a file to another revision
     *
     * @param string $path
     * @param int $revision_from
     * @param int $revision_to
     * @return string
     */
    abstract function compareToRevision($path, $revision_from, $revision_to); 
    
    /**
     * Retruns how much will module logs be updated per request
     *
     * @return int
     */
    abstract function getModuleLogsPerRequest();
    
    /**
     * Return valid path for file history query
     *
     * @param string $path
     * @return string
     */
    abstract function slashifyForHistoryQuery($path);
    
    /**
     * Return latest previous revision
     *
     * @param int $revision
     * @return int
     */
    abstract function previousRevision($revision);
    
    /**
     * Returns a starting number of Repository
     * 
     * @return int
     */
    abstract function getZeroRevision();
    
    
    /**
     * Group paths by type
     *
     * @param array $paths
     * @return array
     */
    function groupPaths($paths) {
      if (is_foreachable($paths)) {
        $grouped = array();
        foreach ($paths as $path) {
            $grouped[$path->getAction()][] = $path->getPath();
        } // if
        return $grouped;
      } //if
      return null;
    } // groupPaths
    
    /**
     * Parse diff content
     *
     * @param array $data
     * @return array
     */
    function parseDiff($data) {
      $diff = array();
      $i = 0;
      $files = array();
      foreach ($data as $key => $diff_line) {
        $add_line = true;
        $skip_file = false;
        
        // Diff ended, property changes are starting
        if (str_starts_with($diff_line, "Property changes on:")) {
          $add_line = false;
          $files[$i]['ended'] = true;
        }
  
        // We have a new file diff
        if (str_starts_with($diff_line, "Index: ")) {
          $i++;
          $files[$i]['started'] = true;
          $files[$i]['ended'] = false;
          
          $diff[$i]['file'] = str_replace("Index: ", "", $diff_line);
          $diff[$i]['content'] = "";
          $diff[$i]['lines'] = "";
          $add_line = false;
        }
  
        // we are ignoring beginning of diff for a file info about start/end revision
        if (str_starts_with($diff_line, "===") || str_starts_with($diff_line, "+++") || str_starts_with($diff_line, "---") || strcmp($diff_line, "\\ No newline at end of file") == 0) {
          $add_line = false;
        }
  
        // line numbers on the left side of the diff
        if (str_starts_with($diff_line, "@@")) {
          $diff[$i]['lines'] .= "... | ...\n";
          $diff[$i]['content'] .= " \n";
  
          $begin_lines = explode(" ", trim(str_replace("@@", "", $diff_line)));
  
          // start counting lines from these
          $left_line = abs(intval($begin_lines['0']));
          $right_line = abs(intval($begin_lines['1']));
  
          $add_line = false;
        }
  
        // we have a line of file content
        if ($add_line && !$skip_file && !$files[$i]['ended']) {
          $left_line++;
          $right_line++;
  
          // fix vertical line alignment when there is no number on the left or right side
          $add = str_pad("",strlen($left_line > $right_line ? $left_line : $right_line)," ");
  
          switch(substr($diff_line, 0, 1)) {
            case '+':
              $row_class = "line_added";
              $diff[$i]['lines'] .= $add.' | '.$right_line."\n";
              break;
            case '-':
              $row_class = "line_removed";
              $diff[$i]['lines'] .= $left_line." | ".$add."\n";
              break;
            case '!':
              $row_class = "wtf";
              $diff[$i]['lines'] .= $left_line." | ".$right_line."\n";
              break;
  
            default:
              $row_class = "default";
              $diff[$i]['lines'] .= $left_line." | ".$right_line."\n";
          }
  
          $diff[$i]['content'] .= "<span class=\"".$row_class."\">".clean($diff_line)." </span>";
  
        } //if
      } // foreach
  
      return $this->removeBinaryFiles(array_values($diff));
    } // parse diff content
    
    /**
     * Remove binary files from diff
     *
     * @param array $diff
     * @return array
     */
    function removeBinaryFiles($diff) {
      if (is_array($diff)) {
        foreach ($diff as $key=>$diff_item) {
          if (strpos($diff_item['content'], 'Cannot display: file marked as a binary type.') !== false) {
            unset($diff[$key]);
          } // if
        } // foreach
      } // if
  
      return $diff;
    } // remove binary files
    
    /**
     * Return file type
     *
     * @param array $diff
     * @return string or false
     */
    function getFileType($filename) {
      if (file_source_can_be_displayed($filename)) {
        return 'text';
      } //if
      if (file_is_image($filename)) {
        return 'image';
      } //if
      return false;
    } //getFileType
    
    static function getName() {
      if (ConfigOptions::getValue('source_svn_type') === "exec") {
        return lang('Executable');
      } elseif (ConfigOptions::getValue('source_svn_type') === "extension") {
        return lang('PHP Extension');
      } //if
    } // getName
    
    /**
     * Checks if the path contains any folders
     * 
     * @param string $path
     * @return bool
     */
    public static function pathInFolder ($path) {
    	return ((empty($path) OR $path == '/')) ? false : true;
    } //pathInFolder
    
 } //RepositoryEngine