<?php

  /**
   * SourceCommit class
   *
   * @package activeCollab.modules.source
   * @subpackage models
   */
  abstract class SourceCommit extends BaseSourceCommit implements ISearchItem, IObjectContext {
  
    /**
     * Total paths affected
     *
     * @var int
     */
    public $total_paths = 0;
    
    /**
     * Grouped paths
     *
     * @var array
     */
    public $grouped_paths = array();
    
    /**
     * Temp action for one file
     *
     * @var array
     */
    public $temp_action = null;

    /**
     * Return verbose commit name
     *
     * @param Language $language
     * @return string
     */
    abstract function getVerboseName($language = null);

    /**
     * Return base type name
     *
     * @param boolean $singular
     * @return string
     */
    function getBaseTypeName($singular = true) {
      return $singular ? 'source_commit' : 'source_commits';
    } // getBaseTypeName
    
    /**
     * Find project objects in commit message, make them links and
     * save the relations to database
     *
     * @param string $commit_message
     * @param string $commit_author
     * @param int $revision
     * @param SourceRepository $source_repository
     * @param Project $project
     * @return string
     */
    static function analyze_message($commit_message, $commit_author, $commit_email, $revision, $source_repository, $branch_name = '') {

      $pattern = '/((complete[d]*)[\s]+)?(task|milestone|discussion|subtask|todolist)[s]*[\s]+[#]*\d+(@[A-Za-z0-9\-\_]+)?+/i';
      if (preg_match_all($pattern, $commit_message, $matches)) {
        $i = 0;
        $search = array();
        $replace = array();
        $matches_unique = array_unique($matches['0']);
        foreach ($matches_unique as $key => $match) {
          $match_data = preg_split('/[\s,]+/', $match, null, PREG_SPLIT_NO_EMPTY);
          // check if the object got completed by this commit
          $object_completed = false;          if (strpos(strtolower($match_data['0']), 'complete') !== false) {
            $object_completed = true;
            unset($match_data['0']);
            $match_data = array_values($match_data);
          } //if

          $object_class_name = $match_data['0'];
        	$module_name = Inflector::pluralize($object_class_name);
          $object_class_name = Inflector::singularize($object_class_name);
          $project_data = explode('@',$match_data['1']);
        	$object_id = trim($project_data['0'], '#');
          if ($project_data['1']) {
            $project = Projects::findBySlug($project_data['1']);
          } else {
            $project = null;
          } //if
        	$search[$i] = $match;
          // check if repo is added just on one project - if so take that project
          if (strtolower($object_class_name) === 'task' && is_null($project)) {
            $project_source_repositories = ProjectSourceRepositories::findByParent($source_repository);
            if ($project_source_repositories->count() == 1) {
              $project = first($project_source_repositories->toArray())->getProject();
            } //if
          } //if
        	if (class_exists($module_name) && class_exists($object_class_name) && !((strtolower($object_class_name) === 'task') && is_null($project)) ) {
        	  $object = null;
        	  switch (strtolower($module_name)) {
        	  	case 'tasks':
        	  	  $object = Tasks::find(array(
                  'conditions' => array('project_id = ? AND integer_field_1 = ? AND type = ? AND state >= ?', $project->getId(), $object_id, 'Task', STATE_ARCHIVED),
                  'one' => true,
                ));
        	  		break;
        	  	case 'discussions':
        	  	  $object = Discussions::find(array(
                  'conditions' => array('id = ? AND type = ? AND state >= ?', $object_id, 'Discussion', STATE_ARCHIVED),
                  'one' => true,
                ));
        	  	  break;
        	  	case 'milestones':
        	  	  $object = Milestones::find(array(
                  'conditions' => array('id = ? AND type = ? AND state >= ?', $object_id, 'Milestone', STATE_ARCHIVED),
                  'one' => true,
                ));
        	  	  break;
        	  	case 'subtasks' :
        	  	  $object = Subtasks::find(array(
                  'conditions' => array('id = ? AND type = ? AND state >= ?', $object_id, 'ProjectObjectSubtask', STATE_ARCHIVED),
                  'one' => true,
                ));
        	  	  break;
              case ('todo' || 'todolist') :
                $object = TodoLists::find(array(
                  'conditions' => array('id = ? AND type = ? AND state >= ?', $object_id, 'TodoList', STATE_ARCHIVED),
                  'one' => true,
                ));
                break;
        	  } // switch



        	  if($object instanceof $object_class_name) {
              /* @var ProjectObject $object */
        	    $link_already_created = (boolean)CommitProjectObjects::count(array("parent_id = ? AND revision = ?", $object->getId(), $revision));
        	    
        	    if (!$link_already_created) {
        	      $comit_project_object = new CommitProjectObject();

        	      $comit_project_object->setProjectId($object->getProject()->getId());
        	      $comit_project_object->setParentId($object->getId());
        	      $comit_project_object->setParentType(ucfirst($object_class_name));
        	      $comit_project_object->setRepositoryId($source_repository->getId());
        	      $comit_project_object->setRevision($revision);
                $comit_project_object->setBranchName($branch_name);
        	   
        	      DB::beginWork();
        	      $save = $comit_project_object->save();
        	      if ($save && !is_error($save)) {
        	        DB::commit();
        	      } else {
        	        DB::rollback();
        	      } // if save
        	    } // if
        	  
        	    $replace[$i] = ($object_completed ? 'Completed ' : '') . '<a href="'.$object->getViewUrl().'">'.$match_data['0'].' '.$match_data['1'].'</a>';

        	    // set the object as completed
        	    if($object_completed && ($object instanceof IComplete)) {

        	      $completed_by = $source_repository->getMappedUser($commit_author, $commit_email);
                if (($completed_by instanceof User) && ($object->complete()->canChangeStatus($completed_by))) {
        	        $object->complete()->complete($completed_by);
                } //if
        	    } // if
        	  } else {
        	    $replace[$i] = ($object_completed ? 'Completed ' : '') . '<a href="#" class="project_object_missing" title="'.lang('Project object does not exist in this project').'">'.$match_data['0'].' '.$match_data['1'].'</a>';
        	  } // if
        	  
        	  $i++;
        	} // if module loaded
        } // foreach
        if (!empty($replace)) {
          return str_ireplace($search, $replace, htmlspecialchars($commit_message));
        } //if
      } // if preg_match

      return $commit_message;
    } // get_project_objects
    
    /**
     * Find if there are users with this email in activeCollab and 
     * creates a new source user relationship
     *
     * @param int $repository_id
     * @param string $user_name
     * @param string $user_email
     * @return bool
     */
    static function analyze_email($repository_id,$user_name,$user_email) {
      if (! is_valid_email($user_email)) {
        return false;
      } //if
      $user = Users::findByEmail($user_email, true);
      if ($user instanceof User) {
        // if allready exists we will not insert it
        $source_user = SourceUsers::findByRepositoryUser($user_name,$repository_id);
        if ($source_user instanceof SourceUser) {
          return false; 
        } //if
        $source_user = new SourceUser();
        $source_user->setRepositoryId($repository_id);
        $source_user->setRepositoryUser($user_name);
        $source_user->setUserId($user->getId());
        $save = $source_user->save();
        if ($save && !is_error($save)) {
          return true;
        } else {
          return false;
        } //if
      } else {
        return false;
      } //if
      
    } // analyze_email


    /**
     * Method that takes parsed array of diff in files and removes non-text extensions
     *
     * @param $parsed_array
     *
     * @return array or FALSE
     */
    static function removeNonTextFiles($parsed_array) {
      if (is_array($parsed_array)) {
        foreach($parsed_array as $key => $file) {
          if (!file_source_can_be_displayed($file['file'])) {
            unset($parsed_array[$key]);
          } //
        } //foreach

        return $parsed_array;
      } else {
        return false;
      } //if
    } // removeNonTextFiles
    
    /**
     * Return count of affected paths
     *
     * @return integer
     */
    function countPaths() {
      $paths = SourcePaths::getPathsForCommit($this->getId());
      return (int) count($paths);
    } // countPaths

    /**
     * Return commit author instance
     *
     * @param SourceRepository $source_repository
     * @return string
     */
    function getAuthorInstance($source_repository = null) {
      if(!($source_repository instanceof SourceRepository) || $source_repository->isNew()) {
        $source_repository = SourceRepositories::findById($this->getRepositoryId());
      } // if

      if (!is_foreachable($source_repository->mapped_users)) {
        $source_repository->mapped_users = SourceUsers::findBySourceRepository($source_repository);
      } // if

      if (isset($source_repository->mapped_users[$this->getCommitedByName()]) && $source_repository->mapped_users[$this->getCommitedByName()] instanceof SourceUser) {
        $source_user = $source_repository->mapped_users[$this->getCommitedByName()];

        return $source_user->system_user instanceof User ? $source_user->system_user : null;
      } // if

      return null;
    } // getAuthorInstance
    
    /**
     * Get Author
     *
     * @param SourceRepository $source_repository
     * @return string
     */
    function getAuthor($source_repository = null, $anchor = true) {
      if(!($source_repository instanceof SourceRepository) || $source_repository->isNew()) {
        $source_repository = SourceRepositories::findById($this->getRepositoryId());
      } // if
      
      if (!is_foreachable($source_repository->mapped_users)) {
        $source_repository->mapped_users = SourceUsers::findBySourceRepository($source_repository);
      } // if
      
      if (isset($source_repository->mapped_users[$this->getCommitedByName()]) && $source_repository->mapped_users[$this->getCommitedByName()] instanceof SourceUser) {
        $source_user = $source_repository->mapped_users[$this->getCommitedByName()];
        $system_user = $source_user->system_user;
        if($system_user instanceof User) {
          return $anchor ? '<a href="'.$system_user->getViewUrl().'">' . $system_user->getDisplayName(true) . '</a>' : $system_user->getDisplayName(true);
        } // if
      } // if
      
      return clean($this->getCommitedByName());
    } // getAuthor
  
    /**
     * Set diff
     *
     * @param array $value
     * @return boolean
     */
    function setDiff($value) {
      if (is_array($value)) {
        $value = serialize($value);
      } //if
      return parent::setDiff($value);
    } // set diff
  
    /**
     * Get diff
     *
     * @return array
     */
    function getDiff() {
      return unserialize(parent::getDiff());
    } // get diff
  
    /**
     * Get View URL
     *
     * @param string $project_slug
     * @param integer $project_source_repository_id
     * @return string
     */
    function getViewUrl($project_slug, $project_source_repository_id = null) {
      if (!$project_source_repository_id) {
        $source_repository = SourceRepositories::findById($this->getRepositoryId());
        $project = Projects::findBySlug($project_slug);
        $project_source_repository = ProjectSourceRepositories::findBySourceRepositoryId($project->getId(),$source_repository->getId());
        if ($project_source_repository) {
          $project_source_repository_id = $project_source_repository->getId();
        } else {
          return false;
        } // if
      } // if

      return Router::assemble('repository_commit', array(
        'project_slug' => $project_slug,
        'project_source_repository_id' => $project_source_repository_id,
        'r' => $this->getRevisionNumber()
      ));
    } // getViewUrl
    
    /**
     * Get View URL of one file
     *
     * @return string
     */
    function getViewFileUrl($project_slug,$project_source_repository_id, $file_path) {
      return Router::assemble('repository_commit', array(
        'project_slug' => $project_slug,
        'project_source_repository_id' => $project_source_repository_id,
        'r' => $this->getRevisionNumber(),
        'path' => $file_path
      ));
    } // getViewUrl
    
    /**
     * Get one commit info url
     *
     * @return string
     */
    function getOneCommitInfoUrl($project_slug,$project_source_repository_id) {
      return Router::assemble('repository_one_commit_info', array('project_slug' => $project_slug, 'project_source_repository_id'=>$project_source_repository_id, 'r'=>$this->getRevisionNumber()));
    } // getOneCommitInfoUrl
    
    /**
     * Get Edit URL
     * 
     * Basically, commits are history entries and are not meant to be edited, 
     * but the method is here for possible compatibility issues with project 
     * object model
     *
     * @return string
     */
    function getEditUrl() {
      return '';  
    } // getEditUrl
    
    /**
     * Get number of each actions (added, deleted, edited)
     * 
     * @return array
     */
    function getActions() {
      $paths = SourcePaths::getPathsForCommit($this->getId());
      $action_array = array('M' => 0, 'D' => 0, 'A' => 0);
      foreach ($paths as $path) {
        switch ($path->getAction()) {
          case 'M': 
          	$action_array['M']++;
          	break;
          case 'D': 
          	$action_array['D']++;
          	break;
          case 'A': 
          	$action_array['A']++;
          	break;
        } //switch
      } //foreach
      return $action_array;
    } // getActions
    
    /**
     * Get repository in which this commit belongs to
     * 
     * @return SourceRepository
     */
    function getSourceRepository() {
    	return SourceRepositories::findById($this->getRepositoryId());
    } // getSourceRepository
        
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------
    
		/**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
      $result = parent::describe($user, $detailed, $for_interface);
      
      $result['commited_on'] = $this->getCommitedOn();
      $result['commit_message'] = stripslashes($this->getMessageBody());

      $result['commited_by'] = $this->getCommitedBy();
      $result['authored_by'] = $this->getAuthoredBy();
      
      $result['source_repository'] = $this->getSourceRepository();

      $result['branch_name'] = $this->getBranchName();
      
      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      throw new NotImplementedError(__METHOD__);
    } // describeForApi
    
    /**
     * Cached search helper instance
     *
     * @var ISourceSearchItemImplementation
     */
    private $search = false;
    
    /**
     * Return search helper instance
     * 
     * @return ISourceSearchItemImplementation
     */
    function &search() {
      if($this->search === false) {
        $this->search = new ISourceSearchItemImplementation($this);
      } // if
      
      return $this->search;
    } // search
    
    /**
     * Return object context domain
     * 
     * @return string
     */
    function getObjectContextDomain() {
      return 'source';
    } // getObjectContextDomain
    
    /**
     * Return object context path
     * 
     * @return string
     */
    function getObjectContextPath() {
      return 'repositories/' . $this->getRepositoryId() . '/commits/' . $this->getRevisionNumber();
    } // getObjectContextPath


    
  }