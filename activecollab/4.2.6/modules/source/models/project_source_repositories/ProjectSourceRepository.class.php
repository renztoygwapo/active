<?php

  /**
   * ProjectSourceRepository.class
   *
   * @package activeCollab.modules.source
   */
  class ProjectSourceRepository extends ProjectObject implements ISubscriptions, ICanBeFavorite {
    
    /**
     * Permission name
     *
     * @var string
     */
    public $permission_name = 'repository';
  
    /**
     * Log object activities
     *
     * @var bool
     */
    public $log_activities = false;
    
    /**
     * Instance of Source repository
     *
     * @var SourceRepository
     */
    public $source_repository = null;
    
    /**
     * Count of commits that were in the last update
     * 
     * @var int
     */
    public $last_update_commits_count = null;
    
    /**
     * Send detailed notifications
     *
     * @var bool
     */
    public $detailed_notifications = false;

    /**
     * Active branch
     *
     * @var string
     */
    public $active_branch = null;

    /**
     * Fields used by this module
     *
     * @var array
     */
    public $fields = array(
      'id',
      'type', 'module',
      'project_id',
      'name','body',
      'created_on', 'created_by_id', 'created_by_name', 'created_by_email',
      'updated_on', 'updated_by_id', 'updated_by_name', 'updated_by_email',
      'state', 'original_state', 'visibility', 'original_visibility','version',
      'integer_field_1' //parent ID
    );

    /**
     * Field map
     *
     * @var array
     */
    var $field_map = array(
      'parent_id' => 'integer_field_1'
    );
  
    /**
     * Construct a new repository
     *
     * @param int $id
     */
    function __construct($id = null) {
      $this->setModule(SOURCE_MODULE);
      $this->setType('ProjectSourceRepository');
  		parent::__construct($id);
    } // __construct
    
    /**
     * Return proper type name in user's language
     *
     * @param boolean $lowercase
     * @param Language $language
     * @return string
     */
    function getVerboseType($lowercase = false, $language = null) {
      return $lowercase ? lang('repository', null, false, $language) : lang('Repository', null, null, $language);
    } // getVerboseType

    /**
     * Return email notification context ID
     *
     * @return string
     */
    function getNotifierContextId() {
      return 'REPOSITORY/' . $this->getId();
    } // getNotifierContextId
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      if($this->routing_context === false) {
        $this->routing_context = Inflector::underscore(get_class($this));
      } // if
      
      return $this->routing_context;
    } // getRoutingContext
    
    /**
     * Create update log
     *
     */
    function createActivityLog() {
      $this->source_repository->activityLogs()->logRepositoryUpdated($this->getUpdatedBy());
    } // create activity log
  
    /**
     * Prepare list of options that $user can use
     *
     * @param IUser $user
     * @param NamedList $options
     * @param string $interface
     * @return NamedList
     */
    protected function prepareOptionsFor(IUser $user, NamedList $options, $interface = AngieApplication::INTERFACE_DEFAULT) {

      if($this->canEdit($user) && $interface == AngieApplication::INTERFACE_DEFAULT) {
        $options->add('repository_users', array(
          'text' => lang('Manage Repository Users'),
          'url' => Router::assemble('repository_users', array('source_repository_id' => $this->getParentId(), 'project_slug' => $this->getProject()->getSlug())),
        	'onclick' => new FlyoutCallback()
        ), true);
      } // if
      
      parent::prepareOptionsFor($user, $options, $interface);
      $options->remove('trash'); 
      $options->remove('archive');
      $options->remove('edit');
      
      if($this->canEdit($user)) {
      	$options->addAfter('edit', array(
      		'text' => lang('Edit'),
      		'url' => $this->getEditUrl(),
      		'onclick' => new FlyoutFormCallback('repository_updated', array('width' => 'narrow'))
      	), 'repository_users');    
      } // if
      
      if($this->canDelete($user)) {
        $options->add('repository_remove_from_project', array(
          'text' => lang('Remove Repository From Project'),
          'url' => Router::assemble('repository_remove_from_project', array('project_source_repository_id' => $this->getId(), 'project_slug' => $this->getProject()->getSlug()), array('id' => 'repository_remove_from_project')),
					'onclick' => new AsyncLinkCallback(array(
                'confirmation' => lang('Are you sure that you want to remove this repository from this project?'), 
                'success_message' => lang('Repository has been successfully removed from project'), 
                'success_event' => $this->getDeletedEventName()
              )),
        ), true);
      } // if
      
      return $options;
    } // prepareOptionsFor


    
    // ---------------------------------------------------
    //  Getters and Setters
    // ---------------------------------------------------

    /**
     * Get parent_id
     *
     * @return integer
     */
    function getParentId() {
      return $this->getIntegerField1();
    } // getParentId

    /**
     * Set parent_id value
     *
     * @param integer $value
     * @return integer
     */
    function setParentId($value) {
      return $this->setIntegerField1($value);
    } // setParentId
  
    /**
     * Get edit URL
     *
     * @return string
     */
    function getEditUrl() {
      return Router::assemble('repository_edit', array('project_source_repository_id'=>$this->getId(), 'project_slug' => $this->getProject()->getSlug()));
    } // getEditUrl
  
    /**
     * Get URL for file revision compare
     *
     * @param string $path
     * @return string
     */
    function getFileCompareUrl($path) {
      $params = array('project_source_repository_id'=>$this->getId(), 'project_slug' => $this->getProject()->getSlug());
      
      if($path !== null) {
        $params['path'] = $path;
      } // if
      
      return Router::assemble('repository_compare',$params);
    } // getFileCompareUrl
    
    /**
     * Get URL for file revision compare in dialog window
     *
     * @param string $path
     * @return string
     */
    function getFileDialogFormCompareUrl($path) {
      $params = array('project_source_repository_id'=>$this->getId(), 'project_slug' => $this->getProject()->getSlug());
      
      if($path !== null) {
        $params['path'] = $path;
      } // if
      
      return Router::assemble('repository_dialog_form_compare',$params);
    } // getFileDialogFormCompareUrl
  
  
    /**
     * Get file download URL
     *
     * @param mixed $revision
     * @param string $path
     * @return string
     */
    function getFileDownloadUrl($revision, $path) {
      $params = array('project_source_repository_id' => $this->getId(), 'project_slug' => $this->getProject()->getSlug());
      
      if($revision !== null) {
        if($revision instanceof SourceCommit) {
          $params['r'] = $revision->getRevisionNumber();
        } else {
          $params['r'] = $revision;
        } // if
      } // if
      
      if($path !== null) {
        $params['path'] = $path;
      } // if
      
      return Router::assemble('repository_file_download', $params);
    } // get file download URL
  
    /**
     * Get file history URL
     *
     * @param mixed $revision
     * @param string $path
     * @return string
     */
    function getFileHistoryUrl($revision, $path) {
      $params = array('project_source_repository_id'=>$this->getId(), 'project_slug' => $this->getProject()->getSlug());
      
      if($revision !== null) {
        if($revision instanceof SourceCommit) {
          $params['r'] = $revision->getRevisionNumber();
        } else {
          $params['r'] = $revision;
        } // if
      } // if
      
      if($path !== null) {
        $params['path'] = $path;
      } // if
      
      return Router::assemble('repository_file_history',$params);
    } // file history URL
  
  
    /**
     * Get view URL
     *
     * @return string
     */
    function getViewUrl() {
      return $this->getHistoryUrl();
    } // getViewUrl
  
    /**
     * Get repository history URL
     *
     * @param null
     * @return string
     */
    function getHistoryUrl($commit_author = null) {
      $params = array('project_source_repository_id' => $this->getId(), 'project_slug' => $this->getProject()->getSlug());
      
      if (!is_null($commit_author)) {
        $params['filter_by_author'] = $commit_author;
      } // if
      
      return Router::assemble('repository_history', $params);
    } // get history URL
  
    /**
     * Get update repository URL
     *
     * @param null
     * @return string
     */
    function getUpdateUrl() {
      return Router::assemble('repository_update', array('project_source_repository_id'=>$this->getId(), 'project_slug' => $this->getProject()->getSlug()));
    } // get update url
  
    /**
     * Get the url for fetching item info
     *
     * @param null
     * @return string
     */
    function getItemInfoUrl($revision = null, $path = null) {
      $params = array('project_source_repository_id' => $this->getId(), 'project_slug' => $this->getProject()->getSlug());
      
      if($revision !== null) {
        if($revision instanceof SourceCommit) {
          $params['r'] = $revision->getRevisionNumber();
        } else {
          $params['r'] = $revision;
        } // if
      } // if
      
      if($path !== null) {
        $params['path'] = $path;
      } // if
      
      return Router::assemble('repository_item_info', $params);
    } // getItemInfoUrl
    
    /**
     * Get browse URL
     *
     * @param null
     * @return string
     */
    function getBrowseUrl($revision = null, $path = null, $peg_revision = null) {
      $params = array('project_source_repository_id' => $this->getId(), 'project_slug' => $this->getProject()->getSlug());
      
      if($revision !== null) {
        if($revision instanceof SourceCommit) {
          $params['r'] = $revision->getRevisionNumber();
        } else {
          $params['r'] = $revision;
        } // if
      } // if
      
      if($path !== null) {
        $params['path'] = $path;
      } // if
      
      if($path !== null) {
        $params['peg_revision'] = $peg_revision;
      } // if
      
      return Router::assemble('repository_browse', $params);
    } // get browse url
    
    /**
     * Get browse toggle expand URL
     *
     * @param null
     * @return string
     */
    function getToggleUrl($revision = null, $path = null, $url_key = null) {
      $params = array('project_source_repository_id' => $this->getId(), 'project_slug' => $this->getProject()->getSlug());
      if($revision !== null) {
        if($revision instanceof SourceCommit) {
          $params['r'] = $revision->getRevisionNumber();
        } else {
          $params['r'] = $revision;
        } // if
      } // if
      
      if($path !== null) {
        $params['path'] = $path;
      } // if
      
      if($url_key !== null) {
        $params['key'] = $url_key;
      } //if
      $params['async'] = true;
      return Router::assemble('repository_browse_toggle', $params);
    } // get browse toggle expand url
  
    /**
     * Return commit details URL
     *
     * @param int $revision
     * @return string
     */
    function getCommitUrl($revision) {
      return Router::assemble('repository_commit', array('project_slug' => $this->getProject()->getSlug(),'project_source_repository_id'=>$this->getId(),'r'=> $revision));
    } // getCommitUrl

    /**
     * Return change default branch URL
     *
     * @return string
     */
    function getChangeBranchUrl() {
      return Router::assemble('repository_change_branch', array('project_slug' => $this->getProject()->getSlug(),'project_source_repository_id'=>$this->getId()));
    } //getChangeBranchUrl

    /**
     * Return do change default branch URL
     *
     * @return string
     */
    function getDoChangeBranchUrl() {
      return Router::assemble('repository_do_change_branch', array('project_slug' => $this->getProject()->getSlug(),'project_source_repository_id'=>$this->getId()));
    } //getChangeBranchUrl
    
    /**
     * Returns instance of the SourceRepository
     * 
     * @return SourceRepository
     */
    function getSourceRepository() {
      if (!($this->source_repository instanceof SourceRepository)) {
        $this->source_repository = SourceRepositories::findById($this->getParentId());
      } //if
      return $this->source_repository;
    } //getSourceRepository
    
    // ---------------------------------------------------
    //  Interfaces implementation
    // ---------------------------------------------------
    
    /**
     * Return object path
     * 
     * @return string
     */
    function getObjectContextPath() {
      return parent::getObjectContextPath() . '/source/' . $this->getId();
    } // getContextPath
    
    /**
     * Subscriptions helper instance
     *
     * @var IProjectObjectSubscriptionsImplementation
     */
    private $subscriptions;
    
    /**
     * Return subscriptions helper for this object
     *
     * @return ISubscriptionsImplementation
     */
    function &subscriptions() {
      if(empty($this->subscriptions)) {
        $this->subscriptions = new IProjectObjectSubscriptionsImplementation($this);
      } // if
      
      return $this->subscriptions;
    } // subscriptions
    
    /**
     * Return history helper instance
     *
     * @return IHistoryImplementation
     */
    function history() {
      return parent::history()->alsoTrackFields(array('varchar_field_1', 'varchar_field_2', 'text_field_1'));
    } // history
    
    /**
     * Returns object of repository engine
     *
     * @return Object of engine
     */
    function getEngine() {
    	switch ($this->source_repository->getType()) {
        	case SVN_REPOSITORY :
        		return new SvnRepositoryEngine($this->source_repository);
        	case GIT_REPOSITORY :
        		return new GitRepositoryEngine($this->source_repository);
        }// switch
        return false;
    } //getEngine

    /**
     * Gets default branch for given user
     *
     * @param User $user
     * @return string
     */
    function getDefaultBranch(User $user) {
      if (is_null($this->active_branch)) {
        $default_source_branches = unserialize($user->getConfigValue('default_source_branch'));
        $this->active_branch = $default_source_branches[$this->getId()] ? $default_source_branches[$this->getId()] : $this->getSourceRepository()->getDefaultBranch();
        return $this->active_branch;
      } else {
        return $this->active_branch;
      } //if

    } //getDefaultBranch

    /**
     * Sets default branch for given user
     *
     * @param User $user
     * @param string $branch
     * @return string
     */
    function setDefaultBranch(User $user, $branch) {
      $default_source_branches = $user->getConfigValue('default_source_branch');
      if (is_string($default_source_branches)) {
        $default_source_branches = unserialize($default_source_branches);
      } else {
        $default_source_branches = Array();
      } //if
      $default_source_branches[$this->getId()] = $branch;
      return ConfigOptions::setValueFor('default_source_branch', $user, serialize($default_source_branches));
    } //setDefaultBranch


    
    /**
     * Puts '/' at the end of the url if it does not have it
     *
     * @param string $repo_url
     * @return string $repo_url
     */
    public static function slashifyAtEnd($repo_url) {
      return $repo_url[strlen($repo_url) - 1] === '/' ? $repo_url : $repo_url.'/';
    }//slashifyAtEnd

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

      $result['branch_name'] = $this->getDefaultBranch($user);
      $result['repository_location'] = $this->getSourceRepository()->getRepositoryPathUrl();

      return $result;
    } // describe

    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------

    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if(!$this->validatePresenceOf('name')) {
        $errors->addError(lang('Repository name is required'), 'name');
      } // if

      parent::validate($errors, true);
    } // validate
  
  }