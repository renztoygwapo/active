<?php
  require_once SOURCE_MODULE_PATH.'/engines/git/Git.class.php';
  require_once SOURCE_MODULE_PATH.'/engines/RepositoryEngine.class.php';
  /**
   * Git commands library
   *
   * @package activeCollab.modules.source
   * @subpackage version control engines
   * @author Goran Blazin
   */
  class GitRepositoryEngine extends RepositoryEngine {

      /**
       * Action is triggered by handler
       *
       * @var boolean
       */
      public $triggerred_by_handler = false;

      /**
       *
       * @var boolean
       */
      private $git_object = null;

      /**
       * Class constructor
       *
       * @param ProjectSourceRepository|SourceRepository $repository
       * @param int $project_id
       * @param bool $triggered_by_handler
       *
       */
      function __construct($repository, $project_id = null , $triggered_by_handler = false) {
        // check if we have necessary resources
        if ($repository instanceof ProjectSourceRepository) {
          $this->project_object_repository = $repository;
          $this->active_repository = $repository->source_repository;
        } elseif ($repository instanceof SourceRepository) {
        	$this->active_repository = $repository;
        } //if
        $this->triggerred_by_handler = $triggered_by_handler;
        $url = self::findCorrectRepositoryUrl($this->active_repository->getRepositoryPathUrl());
        if ($url) {
          $this->git_object = new Git($url);
        } else {
          $this->error = new Error(lang('Error, Git repository not found in this path'));
        } //if

      } // __construct

      /**
       * Check if path is directory
       *
       * @param string $path
       * @param int $revision
       *
       * @return array
       */
      function getInfo($path = '', $revision = null) {
        $revision = $revision === null ? $this->getHeadRevision() : $revision;
        $commit_lib_object = $this->getGitLibCommitObject($revision);

        $info = array();
        $info['path'] = $this->getRealPath($path);
        $commit_tree_object = $this->git_object->getObject($commit_lib_object->tree);

        if ($commit_tree_object ) {
          $path_object = $this->git_object->getObject($commit_tree_object->find($info['path']));
        } else {
          return false;
        } //if

        if ($path_object instanceof GitLibTree) {
          $info['type'] = 'dir';
        } elseif ($path_object instanceof GitLibBlob) {
          $info['type'] = 'file';
        } //if

        if ($info['type'] == 'file') {
          $commit_history = $this->getCommitsForPath($info['path'],$revision);
          $info['last_edited_revision_number'] = $commit_history[0]->getRevisionNumber();
          $info['last_edited_revision_hex_name'] = $commit_history[0]->getName();
          $info['size'] = strlen($path_object->data);
        } else {
          if ($info['path'] == '') {
            $info['last_edited_revision_number'] = $revision;
            $commit = SourceCommits::findByRevision($revision,$this->active_repository, $this->active_branch);
            $info['last_edited_revision_hex_name'] = $commit->getName();
          }
          else {
            $last_commit = $this->getHistoryForDir($info['path'],$revision);
            if ($last_commit instanceof SourceCommit) {
              $info['last_edited_revision_number'] = $last_commit->getRevisionNumber();
              $info['last_edited_revision_hex_name'] = $last_commit->getName();
            } else {
              $info = false;
            }
          } //if
        } //if
        return $info;
      } // getInfo

      /**
       * Get head revision ID
       *
       * @param bool
       * @return mixed
       */
      function getHeadRevision($isAsync = false) {

        $this->triggerred_by_handler = $isAsync;

        try {
          $head_name = $this->git_object->getTip($this->active_branch);
          $head_commit = $this->git_object->getObject($head_name);
          $head_revision = count($head_commit->getHistory($this->git_object));
        } catch (Exception $e) {
          if (!$this->triggerred_by_handler) {
            return false;
          } else {
            $this->error = $e;
            return false;
          } // if
        } //try

        if (isset($head_revision) && is_int($head_revision)) {
          return $head_revision;
        } else {
          $error_message = lang('Could not obtain the highest revision number for this repository');
          if (!$this->triggerred_by_handler) {
            return false;
          } else {
            $this->error = $error_message;
          } // if
        } // if
        return false;
      } // getHeadRevision

      /**
       * Compare one revision of a file to another revision
       *
       * @param string $path
       * @param int $revision_from
       * @param int $revision_to
       * @return string
       */
      function compareToRevision($path, $revision_from, $revision_to) {

        $commit_lib_object = $this->getGitLibCommitObject($revision_to);
        $commit_tree_object = $this->git_object->getObject($commit_lib_object->tree);
        $diff = array();
        $parent = is_array($revision_from) ? first($revision_from) : $revision_from;
        $git_parent_object = GitCommits::findByRevision($parent, $this->active_repository, $this->active_branch);

        if ($git_parent_object) {
          $git_lib_object = $this->git_object->getObject($this->sha1Bin($git_parent_object->getName()));
          $parent = $this->git_object->getObject($git_lib_object->tree);
        } else {
          $parent = null;
        } //if

        $paths = Array();

        if ($path === null) {
          $commit = SourceCommits::findByRevision($revision_to, $this->active_repository, $this->active_branch);
          if ($commit instanceof GitCommit) {
            $source_paths = SourcePaths::getPathsForCommit($commit->getId());
            foreach ($source_paths as $source_path) {
              $paths[] = $source_path->getPath();
            } //foreach
          } //if
        } else {
          $paths[] = $path;
        } //if

        foreach ($paths as $pathvalue) {
          $temp_diff = array();
          $temp_diff[] = "Index: $pathvalue";
          $temp_diff[] = "===================================================================";

          $file_object = $this->git_object->getObject($commit_tree_object->find($pathvalue));
          $current_data = is_null($file_object->data) ? '' : $file_object->data;

          if ($parent) {
            $file_object_parent = $this->git_object->getObject($parent->find($pathvalue));
            $parent_data = is_null($file_object_parent->data) ? '' : $file_object_parent->data;
          } else {
            $parent_data = '';
          } //if

          $temp_diff = array_merge($temp_diff, explode("\n",render_diff($parent_data,$current_data,'unified')));
          $diff = array_merge($diff,$temp_diff);

          // do not show diffs bigger than 5 MB
          if (strlen(serialize($diff)) > 5242880) {
            break;
          } //if
          set_time_limit(30);
        } //foreach
        return count($diff) > 0 ? $diff : false;

      } // compare to revision

      /**
       * Get file content
       *
       * @param Revision $revision
       * @param string $file
       * @return string
       */
      function getFileContent($revision = null, $path) {
        $revision = $revision === null ? $this->getHeadRevision() : $revision;
        $path = ($path == "") ? $path : $path."/";

        $commit_lib_object = $this->getGitLibCommitObject($revision);
        $commit_tree_object = $this->git_object->getObject($commit_lib_object->tree);
        $file_object = $this->git_object->getObject($commit_tree_object->find($path));
        return $file_object->data ? $file_object->data : false;
      } // get file content


      /**
       * Browse repository
       *
       * @param int $revision
       * @param string $path
       * @return array
       */
      function browse($revision = null, $path = '') {
        $revision = $revision === null ? $this->getHeadRevision() : $revision;
        $commit_lib_object = $this->getGitLibCommitObject($revision);
        $path = $this->getRealPath($path);
        $path = ($path == "") ? $path : $path."/";
        $commit_tree_object = $this->git_object->getObject($commit_lib_object->tree);
        $path_tree_object = $this->git_object->getObject($commit_tree_object->find($path));
        $list['current_dir'] = $path;
        $dirs = array();
        $files = array();
        $i=0;
        $j=1;
        foreach ($path_tree_object->nodes as $node_path => $node_object) {
          // put dirs and files into separate arrays
          $info = $this->getInfo($path . $node_object->name, $revision);
          $sub_module = false;
          if ($info === false) {
              $info = $this->getInfo();
              $sub_module = true;
          }
          $last_commit = $this->git_object->getObject($this->sha1Bin($info['last_edited_revision_hex_name']));
          if ($node_object->is_dir) {
            $dirs[$i]['kind'] = 'dir';
            $dirs[$i]['name'] = $node_object->name;
            $dirs[$i]['size'] = $sub_module ? lang('Submodule') : lang('Folder');
            $dirs[$i]['revision'] = $info['last_edited_revision_hex_name'];
            $dirs[$i]['revision_number'] = $info['last_edited_revision_number'];
            $dirs[$i]['author'] = $last_commit->committer->name;
            $dirs[$i]['date'] = new DateTimeValue($last_commit->committer->time);
            $dirs[$i]['url_key'] = $j;
            $j++;
          } else {
            $files[$i]['kind'] = 'file';
            $files[$i]['name'] = $node_object->name;
            $files[$i]['size'] = format_file_size($info['size']);
            $files[$i]['revision'] = $info['last_edited_revision_hex_name'];
            $files[$i]['revision_number'] = $info['last_edited_revision_number'];
            $files[$i]['author'] = $last_commit->committer->name;
            $files[$i]['date'] = new DateTimeValue($last_commit->committer->time);
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
      function getLogs($revision_from,  $revision_to , $logs_per_query = 100) {
        $commits = $this->getCommitHistory();
        if (is_null($commits)) {
          $this->error = lang('Could not retrieve revision logs. Please try again or contact your administrator.');
          return false;
        } //if

        $insert_data = array();
        $logs=0;
        $skipped_commits = 0;

        $parents = null;
        // loop through array of log entries
        for ($commit_id = $revision_from; $commit_id <= $revision_to; $commit_id++) {
         	$logs++;
          // prevent duplicate entries in case when there are two separate update processes
          // (like, both scheduled task and aC user triggered the update)
          if (SourceCommits::count(array("repository_id = ? AND revision_number = ? AND branch_name = ?", $this->active_repository->getId(), $commit_id, $this->active_branch)) > 0) {
          	$skipped_commits++;
            continue;
          } // if
          $key = $commit_id -1;

          $commit_name = $commits[$key];
          $commit_object = $this->git_object->getObject(sha1_bin($commit_name));
          $tree_object = $this->git_object->getObject($commit_object->tree);
          $paths = array();
          $k = 0;

          $parents = $commit_object->parents;
          if (is_foreachable($parents)) {
              $parent_commit_object = $this->git_object->getObject($parents[0]);
              $parent_tree_object = $this->git_object->getObject($parent_commit_object->tree);

              $dir_lists = GitLibTree::treeDiff($parent_tree_object,$tree_object,$commit_id);

              foreach ($dir_lists as $path=>$state) {
                $paths[$k]['path'] = mysql_real_escape_string($path); // paths can contain file names with characters that can break the query
                switch ($state) {
                	case 1:
                	  $paths[$k]['action'] = 'D';
                	  break;

                	case 2:
                	  $paths[$k]['action'] = 'A';
                	  break;

                	case 3:
                	  $paths[$k]['action'] = 'M';
                	  break;
                } //switch
                $k++;
            } //foreach
          } else {
            $dir_lists = $tree_object->listRecursive(null);
            foreach ($dir_lists as $path=>$tree_name) {
              $paths[$k]['path'] = mysql_real_escape_string($path); // paths can contain file names with characters that can break the query
              $paths[$k]['action'] = 'A';
              $k++;
            } // foreach
          } //if

          $updated_on = new DateTimeValue($commit_object->author->time);
          $updated_on_date = $updated_on->getYear()."-".$updated_on->getMonth().'-'.$updated_on->getDay().' '.$updated_on->getHour().':'.$updated_on->getMinute().':'.$updated_on->getSecond();
          $author = mysql_real_escape_string($commit_object->author->name);

          $commited_on = new DateTimeValue($commit_object->committer->time);
          $commited_on_date = $commited_on->getYear()."-".$commited_on->getMonth().'-'.$commited_on->getDay().' '.$commited_on->getHour().':'.$commited_on->getMinute().':'.$commited_on->getSecond();
          $committer = mysql_real_escape_string($commit_object->committer->name);

          $email_for_analyze = is_valid_email($commit_object->author->email) ? $commit_object->author->email : "nobody@site.com";
          $message_body = mysql_real_escape_string(SourceCommit::analyze_message($commit_object->summary."\n".$commit_object->detail, $commit_object->committer->name, $email_for_analyze, $key+1, $this->active_repository, $this->active_branch));

          $message_title = first(explode("\n", $message_body));

          $updated_by_email = mysql_real_escape_string(is_valid_email($commit_object->author->email) ? $commit_object->author->email : "");
          $created_by_email = mysql_real_escape_string(is_valid_email($commit_object->committer->email) ? $commit_object->committer->email : "");

          SourceCommit::analyze_email($this->active_repository->getId(),$author,$updated_by_email);
          SourceCommit::analyze_email($this->active_repository->getId(),$committer,$created_by_email);

          $insert_data[$key]['commit'] = array(
          	'name' 							=> $this->sha1Hex($commit_object->name),
          	'type'							=> 'GitCommit',
          	'revision_number'		=> $commit_id,
          	'repository_id'			=> $this->active_repository->getId(),
          	'message_title'			=> $message_title,
          	'message_body'			=> $message_body,
          	'authored_on'				=> $updated_on_date,
          	'authored_by_name'	=> $author,
          	'authored_by_email'	=> $updated_by_email,
          	'commited_on'				=> $commited_on_date,
          	'commited_by_name'	=> $committer,
          	'commited_by_email'	=> $created_by_email,
          );

          $insert_data[$key]['paths'] = serialize($paths);
        } // for
        return array('data'=>$insert_data, 'total'=>$logs, 'skipped_commits' => $skipped_commits);
      } // get logs

     /**
     * Update async
     *
     * @param mixed $revision_to
     * @param mixed $revision_from
     * @return array
     */
      function update($revision, $head_revision,$latest_revision) {
        set_time_limit(0);
        $difference = $head_revision - $latest_revision;
        $more_logs = true;
        if ($difference > $this->getModuleLogsPerRequest()) {
          $revision_from = ($revision + $this->getModuleLogsPerRequest())-1;
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
       * Check if GIT is usable
       *
       * @param string $path
       * @return boolean
       */
      static function isUsable($path = null) {
        return true;
      } // if

      /**
       * Returns a starting number of Repository
       *
       * @return int
       */
      function getZeroRevision() {
        return 1;
      } //getZeroRevision

    /**
     * Returns all branches from repository
     *
     * @return array;
     */
    function getBranches() {
      return $this->git_object->getBranches();
    } //getBranches


      /**
       * Test connection by trying to create Git object
       *
       * @param SourceRepository $active_repository
       * @return bool
       */
      static function testRepositoryConnection(SourceRepository $active_repository) {
        $url = self::findCorrectRepositoryUrl($active_repository->getRepositoryPathUrl());
        if ($url) {
          $git_object = new Git($url);
        } //if
        return ($git_object instanceof Git) ? true : 'Please check your parameters';
      } // testRepositoryConnection

  	/**
       * Retruns how much will module logs be updated per request for Git
       *
       * @return int
       */
      function getModuleLogsPerRequest() {
        return GIT_SOURCE_MODULE_LOGS_PER_REQUEST;
      } //getModuleLogsPerRequest

      /**
       * Return valid path for file history query
       *
       * @param string $path
       * @return string
       */
      function slashifyForHistoryQuery($path) {
        return $path[0] === "/" ? substr($path,1) : $path;
      } //slashifyForHistoryQuery

      /**
       * Return latest previous revision
       *
       * @param int $revision
       * @return int or false
       */
      function previousRevision($revision) {
        $git_lib_commit = $this->getGitLibCommitObject($revision);
        $git_sha_number = is_array($git_lib_commit->parents) ? $this->sha1Hex(first($git_lib_commit->parents)) : $this->sha1Hex($git_lib_commit->parents);
        $git_commit = GitCommits::findByGitShaNumber($git_sha_number, $this->active_repository,$this->active_branch);
        return ($git_commit instanceof GitCommit) ? $git_commit->getRevisionNumber() : false;
      } //previousRevision


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
       * Convert a SHA-1 hash from hexadecimal to binary representation.
       *
       * @param $hex (string) The hash in hexadecimal representation.
       * @returns (string) The hash in binary representation.
       */
      private function sha1Bin($hex)
      {
          return pack('H40', $hex);
      } //sha1Bin

      /**
       * Convert a SHA-1 hash from binary to hexadecimal representation.
       *
       * @param $bin (string) The hash in binary representation.
       * @returns (string) The hash in hexadecimal representation.
       */
      private function sha1Hex($bin)
      {
          return bin2hex($bin);
      } //sha1Hex

      /**
       * Returns GitLibCommit object from revision ID
       *
       * @param int $revision
       * @returns GitLibCommit
       */
      private function getGitLibCommitObject($revision = null) {
        $revision = $revision === null ? $this->getHeadRevision() : $revision;
        $commit = SourceCommits::findByRevision($revision,$this->active_repository, $this->active_branch);
        if ($commit instanceof SourceCommit) {
          return $this->git_object->getObject($this->sha1Bin($commit->getName()));
        } else {
          return false;
        } //if
      } //getGitLibCommitObject

      /**
       * Returns all commits for active repository
       *
       * @returns array of GitLibCommit
       */
      private function getCommitHistory() {
        $master_commits = $this->git_object->getObject($this->git_object->getTip($this->active_branch));
        return $master_commits->getHistory($this->git_object);
      } //getCommitHistory

      /**
       * Finds history for a blob
       *
       * @param string $node_path
       * @param int $revision
       *
       * @return SourceCommit[]
       */
      private function getCommitsForPath($node_path, $revision) {
        // format path
        $node_path = str_replace("\\","/",$node_path);
        $node_path = $node_path[0] == '/' ? substr($node_path,1) : $node_path;
        $source_paths = SourcePaths::findSourcePathsForPath($node_path, $this->active_branch);
        $commit_history = array();
        if (is_foreachable($source_paths)) {
          foreach ($source_paths as $key => $source_path) {
            /**
             * @var SourcePath $source_path
             */
            $commit= SourceCommits::findById($source_path->getCommitId());

            // lets check if commit is from this branch
            if ($commit->getBranchName() !== $this->active_branch) {
              continue;
            } //if

            if ($commit->getRepositoryId() == $this->active_repository->getId()) {
              if ($commit->getRevisionNumber() > $revision) {
                break;
              } //if
              $commit_history[] = $commit;
            }//if
          } //foreach
        } //if
        return array_reverse($commit_history);
      } //getCommitForPath

      /**
       * Finds last commit for a directory
       *
       * @param string $node_path
       * @param int $revision
       *
       * @return SourceCommit
       */

      private function getHistoryForDir($node_path,$revision) {
        // format path
        $node_path = mysql_real_escape_string(str_replace("\\","/",$node_path));
        $node_path = $node_path[0] == '/' ? substr($node_path,1) : $node_path;
        $source_paths = SourcePaths::find(array(
          'conditions' => array("`path` LIKE ?", $node_path.'/%')
        ));
        $last_commit = null;
        foreach ($source_paths as $source_path) {
          $commit= SourceCommits::findById($source_path->getCommitId());

          // lets check if commit is from this branch
          if ($commit->getBranchName() !== $this->active_branch) {
            continue;
          } //if

          if ($commit->getRepositoryId() == $this->active_repository->getId()) {
            if ($commit->getRevisionNumber() > $revision) {
                break;
            } //if
            $last_commit = $commit;
          }//if
        } //foreach
        return $last_commit;
      } //getHistoryForDir

      /*
      * Finds correct path for the repository based on the URL (Path) given
      *
      * @param string the provided url
      * @return string
      */
      private static function findCorrectRepositoryUrl($url) {
        if (opendir(sprintf('%s/objects/pack', $url))) {
          return with_slash($url);
        } elseif (opendir(sprintf('%s/.git/objects/pack', $url))) {
          return with_slash($url) . ".git";
        } elseif (opendir(sprintf('%s%s/objects/pack', with_slash(GIT_FILES_PATH), $url))) {
          return with_slash(GIT_FILES_PATH) . with_slash($url);
        } elseif (opendir(sprintf('%s%s/.git/objects/pack', with_slash(GIT_FILES_PATH), $url))) {
          return with_slash(GIT_FILES_PATH) . with_slash($url) . ".git";
        } //if
        return false;
      } //determineRepositoryUrl

    } // RepositoryEngine