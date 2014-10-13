<?php

  /**
   * SourceRepository class
   *
   * @package activeCollab.modules.source
   * @subpackage models
   */
  abstract class SourceRepository extends BaseSourceRepository implements IRoutingContext, IActivityLogs, IObjectContext {
  
    /**
     * List of commits
     *
     * @var mixed
     */
    public $commits = null;
  
    /**
     * Last commit info
     *
     * @var mixed
     */
    public $last_commit = null;
    
    /**
     * Source repository instance
     *
     * @var mixed
     */
    public $source_repository = null;
  
    /**
     * We do not need protected vars here
     *
     * @var array
     */
    public $protect = array();
  
    /**
     * List of supported source version systems
     *
     * @var array
     */
    public $types = array();
  
    /**
     * Repository update types
     *
     * Values are placed into __construct and variable is populated there
     * because calling lang() function is not possible when defining class variables
     *
     * @var array
     */
    public $update_types = array();
    
    /**
     * Array of mapped repository users
     *
     * @var SourceUser[]
     */
    public $mapped_users = array();

    /**
     * Construct a new repository
     *
     * @param int $id
     */
    function __construct() {
      parent::__construct();
      
      $this->update_types = source_module_update_types();
      $this->types = source_module_types();
    } // __construct
    
    /**
     * Use specific version control engine
     *
     * @param string $engine
     */
    abstract function loadEngine(); // load engine
    
    /**
     * Return new instance of SourceCommit
     * 
     * @return SourceCommit
     */
    abstract function getNewCommit(); // getNewCommit
    
    /**
     * Returns SourceCommit by revision
     *
     * @param int $revision
     * @param string $branch_name
     * 
     * @return SourceCommit
     */
    abstract function getCommitByRevision($revision, $branch_name); //getCommitByRevision
    
    /**
     * Returns the name of the repository commit
     * 
     * @return string
     */
    abstract function getCommitName(); //getCommitName

    /**
     * Returns the name of the repository in real language
     *
     * @return string
     */
    abstract function getVerboseName(); //getVerboseName

    /**
     * Returns the name of icon file representing the repository
     *
     * @return string
     */
    abstract function getIconFileName(); //getIconFileName

    /**
     * Returns true if repository has branches
     *
     * @return bool
     */
    abstract function hasBranches(); //hasBranches

    /**
     * Returns the name of the default branch or false
     *
     * @return string or FALSE
     */
    abstract function getDefaultBranch(); //getDefaultBranch

    /**
     * Get distinct list of users from repository commits
     *
     * @return array
     */
    function getDistinctUsers() {
      $users = array();
      $repository_users = DB::execute("SELECT DISTINCT commited_by_name FROM ".TABLE_PREFIX."source_commits WHERE repository_id = ".$this->getId()." ORDER BY commited_by_name ASC");
      if (is_foreachable($repository_users)) {
        foreach ($repository_users as $repository_user) {
        	$users[] = $repository_user['commited_by_name'];
        } // foreach
      } //if
      
      $repository_users = DB::execute("SELECT DISTINCT authored_by_name FROM ".TABLE_PREFIX."source_commits WHERE repository_id = ".$this->getId()." ORDER BY authored_by_name ASC");
      if (is_foreachable($repository_users)) {
        foreach ($repository_users as $repository_user) {
          if ( ! in_array($repository_user['authored_by_name'],$users,true) && $repository_user['authored_by_name']) {
        	$users[] = $repository_user['authored_by_name'];
          } //if
        } // foreach
      } // if
      
      return $users;
    } // getDistinctUsers
    
    /**
     * Update repository with new commits
     *
     * @param array $logs
     * @param string $branch
     */
    function update($logs, $branch) {
      if (is_foreachable($logs)) {
        foreach ($logs as $data) {
        // commit insert
        	$authored_on = is_null($data['commit']['authored_on']) ? 'NULL' : "'".$data['commit']['authored_on']."'";
        	$commited_on = is_null($data['commit']['commited_on']) ? 'NULL' : "'".$data['commit']['commited_on']."'";

        	$insert = "('".$data['commit']['name']."',
        						'".$data['commit']['type']."' , 
        						".$data['commit']['revision_number']." , 
        						".$data['commit']['repository_id']." , 
        						'".$data['commit']['message_title']."' , 
        						'".$data['commit']['message_body']."' , 
          					".$authored_on." , 
        						'".$data['commit']['authored_by_name']."' ,  
          					'".$data['commit']['authored_by_email']."' , 
        						".$commited_on." , 
        						'".$data['commit']['commited_by_name']."' ,  
          					'".$data['commit']['commited_by_email']."',
          					'" . $branch . "')";
        	
          $query = "INSERT INTO ".TABLE_PREFIX."source_commits (`name`,`type`,`revision_number`,`repository_id`,`message_title`,`message_body`,`authored_on`,`authored_by_name`,`authored_by_email`,`commited_on`,`commited_by_name`,`commited_by_email`,`branch_name`) VALUES ". $insert;
          DB::execute($query) or die(mysql_error().'<br/>'.$query);
          
          $last_inserted_commit = mysql_insert_id();
          // search index insert
          $repositories = SourceRepositories::getIdNameMap();
    			Search::set('source',array(
    				'id'		=> $last_inserted_commit,
          	'class' => $data['commit']['type'], 
						'context' => 'source/' . $this->getId(), 
						'repository_id' => $this->getId(), 
						'repository' => isset($repositories[$this->getId()]) && $repositories[$this->getId()] ? $repositories[$this->getId()] : null, 
						'body' => $data['commit']['message_body'], 
      		));
          
          // source paths insert
          $paths = unserialize($data['paths']);
          
          if (is_foreachable($paths)) {
            $query = "INSERT INTO ".TABLE_PREFIX."source_paths (`commit_id`,`path`,`action`) VALUES ";
            $path_insert = array();
            foreach ($paths as $i => $path_log) {
              $path = mysql_real_escape_string($path_log['path']);
              $action = mysql_real_escape_string($path_log['action']);
              $path_insert[] = "( $last_inserted_commit , '$path' , '$action' )";
              // insert query on every 500 elements and reset array for inserting
              if (($i+1) % 500 == 0) {
                DB::execute($query.implode(",",$path_insert));
                $path_insert = array();
              } //if
            } //foreach
            if (!empty($path_insert)) {
              DB::execute($query.implode(",",$path_insert)) or die(mysql_error().'<br/>'.$query);
            } //if
          } //if
        } // foreach
      } // if
    } // update repository
    
    /**
     * Get information about latest commit(s). Count is the number of the commmits
     *
     * @param string $branch_name
     * @param int $count
     * @param boolean $always_array
     *
     * @return SourceCommit
     */
    abstract function getLastCommit($branch_name, $count = 1, $always_array = false); // getLastCommit
    
    /**
     * Return revision number based on revision name
     * 
     * @param string $revision_name
     * @return integer
     */
    abstract function getRevisionNumber($revision_name); //getRevisionNumber
    
    /**
     * Set graph data
     *
     * @param string $graph_data
     * @return string
     */
    function setGraph($graph_data) {
      return parent::setGraph(serialize($graph_data));
    }//setGraph
    
    /**
     * Get graph data
     *
     * @return mixed
     */
    function getGraph() {
      return unserialize(parent::getGraph());
    }//getGraph
    
    /**
     * Get recent activity for repository graph at module home page
     *
     * @param string $branch_name
     *
     * @return array
     */
    function getRecentActivity($branch_name) {
      $latest_commit = $this->getLastCommit($branch_name);

      if (!($latest_commit instanceof SourceCommit)) {
        return null;
      } // if

      $all_cached_data = $this->getGraph();

      if ($this->hasBranches()) {
        $cached_data = $all_cached_data[$branch_name];
      } else {
        $cached_data = $all_cached_data;
      } //if

      $latest_revision = $latest_commit->getRevisionNumber();

      $cache_id = date('m-d-Y').'_'.$latest_revision;
      
      if (isset($cached_data['logs']) && is_array($cached_data['logs']) && $cached_data['cache_id'] == $cache_id) {
        $graph_data = $cached_data['logs'];
      } else {
        $graph_data = SourceCommits::getRecentActivity($this, $branch_name);

        $data_for_cache = array(
          'logs' => $graph_data,
          'cache_id' => $cache_id,
        );
        if ($this->hasBranches()) {
          $all_cached_data[$branch_name] = $data_for_cache;
          $this->setGraph($all_cached_data);
        } else {
          $this->setGraph($data_for_cache);
        } //if
        $this->save();
      } // if
      
      return $graph_data;
    } // getRecentActivity
    
  	/**
     * Get mapped user
     *
     * @param string $repository_user
     * @param string $commit_email
     * @return mixed
     */
    function getMappedUser($repository_user, $commit_email) {
      if (!is_foreachable($this->mapped_users)) {
        $this->mapped_users = SourceUsers::findBySourceRepository($this);
      } // if
      
      if (isset($this->mapped_users[$repository_user]) && $this->mapped_users[$repository_user] instanceof SourceUser) {
        $source_user = $this->mapped_users[$repository_user];
        if($source_user->system_user instanceof User) {
          return $source_user->system_user;
        } // if
      } // if
      
      return new AnonymousUser($repository_user, $commit_email);
    }
    
    /**
     * Get repository URL
     *
     * @return string
     */
    function getRepositoryPathUrl() {
      return str_replace(' ', '%20', parent::getRepositoryPathUrl());
    } // getRepositoryPathUrl
    
    /**
     * Returns object of repository engine
     *
     * @var int $project_id
     * @var ProjectSourceRepository $project_object_repository
     *
     * @return RepositoryEngine
     */
    abstract function getEngine($project_id = null,$project_object_repository = null); //getEngine
    
    /**
     * Returns all projects in which is this repository included
     * 
     * @return array of Project 
     */
    function getProjects() {
      $project_source_repositories = ProjectSourceRepositories::findByParent($this);
      $projects = array();
      foreach ($project_source_repositories as $project_source_repository) {
        if ($project_source_repository instanceof ProjectSourceRepository) {
          $projects[] = $project_source_repository->getProject();
        }//if
      } //foreach
      return $projects;
    }
    
    /**
     * Returns true if $user can update repository data
     * 
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return $user->isAdministrator();
    } // canEdit
    
    /**
     * Returns true if $user can delete this repository
     * 
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return $user->isAdministrator();
    } // canDelete
    
    /**
     * Returns true if $user can add repository
     * 
     * @param User $user
     * @return boolean
     */
    static function canAdd(User $user) {
      return $user->isAdministrator();
    } // canAdd
    
    /**
     * Tests if connection parameters for this repository are valid
     *
     * @return boolean
     */
    abstract function testRepositoryConnection(); //testRepositoryConnection
    
    /**
     * Tests if engines can be used
     *
     * @return boolean
     */
    abstract function engineIsUsable(); //engineIsUsable
    
    /**
     * Puts '/' at the end of the url if it does not have it
     *
     * @param string $repo_url
     * @return string $repo_url
     */
    public static function slashifyAtEnd($repo_url) {
      return $repo_url[strlen($repo_url) - 1] === '/' ? $repo_url : $repo_url.'/';
    }//slashifyAtEnd
    
    /**
     * Delete repository from database
     * 
     * @throws Exception
     */
    function delete() {
      try {
      	DB::beginWork('Removing repository @ ' . __CLASS__);
      	
      	ProjectSourceRepositories::deleteByRepository($this);
      	SourceCommits::deleteByRepository($this);
      	
      	parent::delete();
      	
      	DB::commit('Repository removed @ ' . __CLASS__);
      } catch (Exception $e) {
        DB::rollback('Failed to remove repository @ ' . __CLASS__);
        throw $e;
      } // try
    } // delete

    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------

    /**
     * Cached activity logs helper instance
     *
     * @var ISourceActivityLogsImplementation
     */
    private $activity_logs = false;

    /**
     * Return activity logs helper instance
     *
     * @return ISourceActivityLogsImplementation
     */
    function activityLogs() {
      if($this->activity_logs === false) {
        $this->activity_logs = new ISourceActivityLogsImplementation($this);
      } // if

      return $this->activity_logs;
    } // activityLogs

    // ---------------------------------------------------
    //  Context
    // ---------------------------------------------------

    /**
     * Return object domain
     *
     * @return string
     */
    function getObjectContextDomain() {
      return 'source_repository';
    } // getContextDomain

    /**
     * Return object path
     *
     * @return string
     */
    function getObjectContextPath() {
      return 'source_repository/' . $this->getId();
    } // getContextPath

  }