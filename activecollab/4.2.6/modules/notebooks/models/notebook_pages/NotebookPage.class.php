<?php

  /**
   * NotebookPage class
   *
   * @package activeCollab.modules.notebooks
   * @subpackage models
   */
  class NotebookPage extends BaseNotebookPage implements IRoutingContext, IComments, ISubscriptions, IAttachments, IState, IActivityLogs, ISearchItem, ICanBeFavorite, IAccessLog, IObjectContext, IHistory {
  	
  	/**
     * Saved original name value if setAttributes() is called
     *
     * @var string
     */
    private $original_name = false;
    
    /**
     * Saved original body value if setAttributes() is called
     *
     * @var string
     */
    private $original_body = false;
    
    /**
     * List of rich text fields
     *
     * @var array
     */
    protected $rich_text_fields = array('body');
    
    /**
     * Cached inspector instance
     * 
     * @var INotebookPageInspectorImplementation
     */
    private $inspector = false;
    
    /**
     * Return inspector helper instance
     * 
     * @return INotebookPageInspectorImplementation
     */
    function inspector() {
      if($this->inspector === false) {
        $this->inspector = new INotebookPageInspectorImplementation($this);
      } // if
      
      return $this->inspector;
    } // inspector
    
    /**
     * Cached history implementation instance
     *
     * @var IHistoryImplementation
     */
    private $history = false;
    
    /**
     * Return history helper instance
     * 
     * @return IHistoryImplementation
     */
    function history() {
      if($this->history === false) {
        $this->history = new IHistoryImplementation($this);
      } // if
      
      return $this->history;
    } // history
    
    /**
     * Return proper type name in user's language
     *
     * @param boolean $lowercase
     * @param Language $language
     * @return string
     */
    function getVerboseType($lowercase = false, $language = null) {
      return $lowercase ? lang('page', null, null, $language) : lang('Page', null, null, $language);
    } // getVerboseType
    
    /**
     * Return parent project
     *
     * @return Project
     * @throws InvalidInstanceError
     */
    function &getProject() {
      if($this->isNew()) {
        if($this->getParent() instanceof Notebook || $this->getParent() instanceof NotebookPage) {
          return $this->getParent()->getProject();
        } else {
          throw new InvalidInstanceError('$parent', $this->getParent(), array('Notebook', 'NotebookPage'));
        } // if
      } else {
        return $this->getNotebook()->getProject();
      } // if
    } // getProject
    
    // ---------------------------------------------------
    //  Tree walkers...
    // ---------------------------------------------------
    
    /**
     * Return parent notebook
     *
     * @param boolean $force
     * @return Notebook
     */
    function &getNotebook($force = false) {
      $notebook_id = $this->getNotebookId($force);
      
      if($notebook_id) {
        return DataObjectPool::get('Notebook', $notebook_id);
      } else {
        return $var = null;
      } // if
    } // getNotebook
    
    /**
     * Cached notebook ID
     *
     * @var integer
     */
    private $notebook_id = false;
    
    /**
     * Return ID of parent notebook
     * 
     * @param boolean $force
     * @return integer
     */
    function getNotebookId($force = false) {
      if($force || $this->notebook_id === false) {
        $this->walkTheTree();
      } // if
      
      return $this->notebook_id;
    } // getNotebookId
    
    /**
     * Cached depth value
     *
     * @var integer
     */
    private $depth = false;
    
    /**
     * Calculate object depth
     * 
     * @param boolean $force
     * @return integer
     */
    function getDepth($force = false) {
      if($force || $this->depth === false) {
        $this->walkTheTree();
      } // if
      
      return $this->depth;
    } // getDepth
    
    /**
     * This function will walk the tree to find parents, notebook and depth
     */
    private function walkTheTree() {
      list($this->notebook_id, $this->depth) = Notebooks::getTreeInfoByPage($this);
    } // walkTheTree
    
    // ---------------------------------------------------
    //  Subpages and revisions
    // ---------------------------------------------------
  	
  	/**
     * Return subpages
     *
     * $min_state is optional (when left out, system will use state value of this page as min state)
     *
     * @param mixed $min_state
     * @return NotebookPage[]
     */
    function getSubpages($min_state = null) {
      return NotebookPages::findSubpages($this, $min_state !== null ? $min_state : $this->getState());
    } // getSubpages
    
    /**
     * Return user who commited last version of this page
     *
     * @return IUser|null
     */
    function getLastVersionBy() {
      return $this->getUserFromFieldSet('last_version_by');
    } // getLastVersionBy

    /**
     * Set person who last update this page
     *
     * @param IUser|null $last_version_by
     * @return IUser|null
     */
    function setLastVersionBy($last_version_by) {
      return $this->setUserFromFieldSet($last_version_by, 'last_version_by');
    } // setLastVersionBy
    
    // ---------------------------------------------------
    //  Versions
    // ---------------------------------------------------
    
    /**
     * Cached notebook page versions
     *
     * @var DBResult
     */
    private $versions = false;
    
    /**
     * Return all notebook page version objects
     *
     * @return NotebookPageVersion[]
     */
    function getVersions() {
      if($this->versions === false) {
        $this->versions = NotebookPageVersions::findByNotebookPage($this);
        $this->versions_count = $this->versions instanceof DBResult ? $this->versions->count() : 0;
      } // if
      return $this->versions;
    } // getVersions
    
    /**
     * Cached notebook page previous version
     *
     * @var array
     */
    private $previous_version = false;
    
    /**
     * Find previous version
     *
     * @return NotebookPageVersion
     */
    function findPrevious() {
      if($this->previous_version === false) {
        $this->previous_version = NotebookPageVersions::findPrevious($this);
      } // if
      return $this->previous_version;
    } // findPrevious
    
    /**
     * Cached number of notebook page versions
     *
     * @var integer
     */
    private $versions_count = false;
    
    /**
     * Returns number of versions
     *
     * @param boolean $load
     * @return integer
     */
    function countVersions($load = false) {
      if($this->versions_count === false) {
        if($this->versions === false) {
          if($load) {
            $this->getVersions();
          } else {
            $this->versions_count = NotebookPageVersions::countByNotebookPage($this);
          } // if
        } // if
      } // if
      return $this->versions_count;
    } // countVersions
    
    /**
     * Set attributes
     *
     * @param array $attributes
     */
    function setAttributes($attributes) {
      if($this->original_name === false && isset($attributes['name']) && $attributes['name'] != $this->getName()) {
        $this->original_name = $this->getName();
      } // if
      
      if($this->original_body === false && isset($attributes['body']) && $attributes['body'] != $this->getBody()) {
        $this->original_body = $this->getBody();
      } // if
      
      parent::setAttributes($attributes);
    } // setAttributes
    
    /**
     * Move this page to $notebook
     * 
     * @param Notebook $notebook
     * @param IUser $user
     */
    function moveToNotebook(Notebook $notebook, IUser $user) {
			$this->setParent($notebook, false);
			$this->setUpdatedBy($user);
			$this->setUpdatedOn(new DateTimeValue());
			$this->save();
    } // moveToNotebook
    
    /**
     * Create a new page version
     * 
     * @param User $by
     * @return NotebookPageVersion
     * @throws Exception
     */
    function createVersion(User $by) {
      try {
        DB::beginWork('Creating a new notebook page version @ ' . __CLASS__);
      
        $version = new NotebookPageVersion();
        
        $version->setNotebookPage($this);
        $version->setName($this->original_name !== false ? $this->original_name : $this->getName());
        $version->setBody($this->original_body !== false ? $this->original_body : $this->getBody());
        $version->setVersion($this->getVersion());
        $version->setCreatedBy($this->getUpdatedBy());
        $version->setCreatedOn($this->getUpdatedOn());
        
        $version->save();
        
        DB::commit('Notebook page version created @ ' . __CLASS__);
        
        // Update this notebook page with version properties
        $this->setVersion($this->getVersion() + 1);
        
//        $this->setUpdatedBy($by);
//        $this->setUpdatedOn(new DateTimeValue());
        
        $this->setLastVersionBy($by);
        $this->setLastVersionOn(new DateTimeValue());
        
        return $version;
      } catch(Exception $e) {
        DB::rollback('Failed to create a new notebook page version @ ' . __CLASS__);
        throw $e;
      } // try
    } // createVersion
    
    /**
     * Revert to version
     *
     * @param NotebookPageVersion $version
     * @param User $by
     * @return boolean
     * @throws Exception
     */
    function revertToVersion(NotebookPageVersion $version, User $by) {
      try {
        DB::beginWork('Reverting to version @ ' . __CLASS__);
        
        // Save current version
        $this->createVersion($by);
        
        // Revert properties
        $this->setName($version->getName());
        $this->setBody($version->getBody());
        $this->setUpdatedBy($version->getCreatedBy());
        
        $this->save();
        
        DB::commit('Reverted to an older version @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to revert to older version @ ' . __CLASS__);
        throw $e;
      } // try
    } // revertToVersion
    
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
      
      $result['revision_num'] = $this->getVersion();   
			$result['is_archived'] = $this->getState() == STATE_ARCHIVED ? 1 : 0;
			
			$result['parent_id'] = $this->getParentId();
			$result['parent_type'] = $this->getParentType();
			
			$result['depth'] = $this->getDepth();
      
      if($detailed) {
        $result['parent'] = $this->getParent()->describe($user, false, false);
        $result['notebook'] = $this->getNotebook()->describe($user);
        $result['project'] = $this->getNotebook()->getProject()->describe($user);
        $result['subpages'] = array();
        
        $subpages = $this->getSubpages();
        if(is_foreachable($subpages)) {
          foreach($subpages as $subpage) {
            $result['subpages'][] = $subpage->describe($user);
          } // foreach
        } // if
        
        $result['revisions'] = array();
        
        $revisions = $this->getVersions();
        if(is_foreachable($revisions)) {
          foreach($revisions as $revision) {
            $result['revisions'][] = $revision->describe($user, true, false);
          } // foreach
        } // if
      } else {
        $result['notebook_id'] = $this->getNotebookId();
        $result['project_id'] = $this->getNotebook()->getProjectId();
      } // if
      
      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      $result = parent::describeForApi($user, $detailed);

      $result['revision_num'] = $this->getVersion();
      $result['is_archived'] = $this->getState() == STATE_ARCHIVED ? 1 : 0;

      $result['parent_id'] = $this->getParentId();
      $result['parent_type'] = $this->getParentType();

      $result['depth'] = $this->getDepth();

      if($detailed) {
        $result['notebook'] = $this->getNotebook()->describeForApi($user);
        $result['project'] = $this->getNotebook()->getProject()->describeForApi($user);
        $result['subpages'] = array();

        $subpages = $this->getSubpages();
        if($subpages) {
          foreach($subpages as $subpage) {
            $result['subpages'][] = $subpage->describeForApi($user);
          } // foreach
        } // if

//        $result['revisions'] = array();
//
//        $revisions = $this->getVersions();
//        if($revisions) {
//          foreach($revisions as $revision) {
//            $result['revisions'][] = $revision->describeForApi($user);
//          } // foreach
//        } // if
      } else {
        $result['notebook_id'] = $this->getNotebookId();
        $result['project_id'] = $this->getNotebook()->getProjectId();
      } // if

      return $result;
    } // describeForApi

    /**
     * Describe notebook page for notebook API response
     *
     * @param IUser $user
     * @param int $min_state
     * @return array
     */
    function describeForNotebookApi(IUser $user, $min_state = STATE_VISIBLE) {
      $result = array(
        'name' => $this->getName(),
        'permalink' => $this->getViewUrl(),
        'subpages' => array(),
      );

      $subpages = $this->getSubpages(STATE_VISIBLE);

      if($subpages) {
        foreach($subpages as $subpage) {
          $result['subpages'][] = $subpage->describeForNotebookApi($user, $min_state);
        } // foreach
      } // if

      return $result;
    } // describeForNotebookApi
    
    // ---------------------------------------------------
    //  Context
    // ---------------------------------------------------
    
    /**
     * Return page context domain
     * 
     * @return string
     * @throws InvalidInstanceError
     */
    function getObjectContextDomain() {
      if($this->getNotebook() instanceof Notebook) {
        return $this->getNotebook()->getObjectContextDomain();
      } else {
        throw new InvalidInstanceError('notebook', $this->getNotebook(), 'Notebook');
      } // if
    } // getObjectContextDomain
    
    /**
     * Return object path
     * 
     * @return string
     * @throws InvalidInstanceError
     */
    function getObjectContextPath() {
      if($this->getNotebook() instanceof Notebook) {
        return $this->getNotebook()->getObjectContextPath() . '/pages/' . $this->getId();
      } else {
        throw new InvalidInstanceError('notebook', $this->getNotebook(), 'Notebook');
      } // if
    } // getContextPath
  	
  	// ---------------------------------------------------
  	//  Permissions
  	// ---------------------------------------------------
  	
  	/**
     * Returns true if $user can view this object
     *
     * @param User $user
     * @return boolean
     */
    function canView(User $user) {
      return $this->getNotebook()->canView($user);
    } // canView
    
    /**
     * Returns true if $user can edit this object
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return $this->getNotebook()->canEdit($user);
    } // canEdit
  
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------
    
    /**
     * Cached instance of activity logs implementation
     *
     * @var INotebookPageActivityLogsImplementation
     */
    private $activity_logs = false;
    
    /**
     * Return activity logs implementation
     *
     * @return INotebookPageActivityLogsImplementation
     */
    function activityLogs() {
      if($this->activity_logs === false) {
        $this->activity_logs = new INotebookPageActivityLogsImplementation($this);
      } // if
      
      return $this->activity_logs;
    } // activityLogs
    
    /**
     * Routing context name
     *
     * @var string
     */
    private $routing_context = false;
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      if($this->routing_context === false) {
        $this->routing_context = 'project_notebook_page';
      } // if
      
      return $this->routing_context;
    } // getRoutingContext
    
    /**
     * Routing context parameters
     *
     * @var array
     */
    private $routing_context_params = false;
    
    /**
     * Return routing context parameters
     *
     * @return array
     */
    function getRoutingContextParams() {
      if($this->routing_context_params === false) {
        $this->routing_context_params = array(
          'project_slug' => $this->getProject()->getSlug(),
          'notebook_id' => $this->getNotebook()->getId(),
          'notebook_page_id' => $this->getId(), 
        );
      } // if
      
      return $this->routing_context_params;
    } // getRoutingContextParams
    
    /**
     * Comment interface instance
     *
     * @var NotebookPageComments
     */
    private $comments;
    
    /**
     * Return notebook page comments interface instance
     *
     * @return INotebookPageCommentsImplementation
     */
    function &comments() {
      if(empty($this->comments)) {
        $this->comments = new INotebookPageCommentsImplementation($this);
      } // if
      return $this->comments;
    } // comments
    
    /**
     * Subscriptions helper instance
     *
     * @var INotebookPageSubscriptionsImplementation
     */
    private $subscriptions;
    
    /**
     * Return subscriptions helper for this object
     *
     * @return INotebookPageSubscriptionsImplementation
     */
    function &subscriptions() {
      if(empty($this->subscriptions)) {
        $this->subscriptions = new INotebookPageSubscriptionsImplementation($this);
      } // if
      
      return $this->subscriptions;
    } // subscriptions
    
    /**
     * Cached attachment manager instance
     *
     * @var IAttachmentsImplementation
     */
    private $attachments;
    
    /**
     * Return attachments manager instance for this object
     *
     * @return IAttachmentsImplementation
     */
    function &attachments() {
      if(empty($this->attachments)) {
        $this->attachments = new IAttachmentsImplementation($this);
      } // if
      
      return $this->attachments;
    } // attachments
    
    /**
     * Cached state helper instance
     *
     * @var INotebookPageStateImplementation
     */
    private $state = false;
    
    /**
     * Return state helper
     *
     * @return INotebookPageStateImplementation
     */
    function state() {
      if($this->state === false) {
        $this->state = new INotebookPageStateImplementation($this);
      } // if
      
      return $this->state;
    } // state
    
    /**
     * Cached search helper instance
     *
     * @var INotebookPageSearchItemImplementation
     */
    private $search = false;
    
    /**
     * Return search helper instance
     * 
     * @return INotebookPageSearchItemImplementation
     */
    function &search() {
      if($this->search === false) {
        $this->search = new INotebookPageSearchItemImplementation($this);
      } // if
      
      return $this->search;
    } // search
    
    /**
     * Cached access log helper instance
     *
     * @var IAccessLogImplementation
     */
    private $access_log = false;
    
    /**
     * Return access log helper instance
     * 
     * @return IAccessLogImplementation
     */
    function accessLog() {
      if($this->access_log === false) {
        $this->access_log = new IAccessLogImplementation($this);
      } // if
      
      return $this->access_log;
    } // accessLog
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return compare versions URL
     *
     * @param NotebookPageVersion $version
     * @return string
     */
    function getCompareVersionsUrl($version = null) {
      $params = array(
      	'project_slug' => $this->getProject()->getSlug(),
      	'notebook_id' => $this->getNotebook()->getId(),
      	'notebook_page_id' => $this->getId()
      );
      
      if($version instanceof NotebookPageVersion) {
        $params['new'] = 'latest';
        $params['old'] = $version->getVersion();
      } // if
      
      return Router::assemble('project_notebook_page_compare_versions', $params);
    } // getCompareVersionsUrl
    
    /**
     * Get revert to URL
     *
     * @param NotebookPageVersion $version
     * @return string
     */
    function getRevertUrl($version) {
      $to = $version instanceof NotebookPageVersion ? $version->getVersion() : $version;
      
      return Router::assemble('project_notebook_page_revert', array(
      	'project_slug' => $this->getProject()->getSlug(),
      	'notebook_id' => $this->getNotebook()->getId(),
      	'notebook_page_id' => $this->getId(),
      	'to' => $to,
      ));
    } // getRevertUrl
    
    /**
     * Get url for moving notebook page
     * 
     * @return string
     */
    function getMoveUrl() {
    	return Router::assemble('project_notebook_page_move', array(
      	'project_slug' => $this->getProject()->getSlug(),
      	'notebook_id' => $this->getNotebook()->getId(),
      	'notebook_page_id' => $this->getId(),
    	));
    } // getMoveUrl
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Prepare list of options that $user can use
     *
     * @param IUser $user
     * @param NamedList $options
     * @param string $interface
     * @return NamedList
     */
    protected function prepareOptionsFor(IUser $user, NamedList $options, $interface = AngieApplication::INTERFACE_DEFAULT) {
    	parent::prepareOptionsFor($user, $options, $interface);
      
      // Default interface
      if ($this->canEdit($user)) {
        $options->beginWith('move', array(
          'url' => $this->getMoveUrl(),
          'text' => lang('Move to Notebook'),
          'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? '' : AngieApplication::getImageUrl('icons/navbar/move.png', NOTEBOOKS_MODULE, AngieApplication::INTERFACE_PHONE),
          'onclick' => new FlyoutFormCallback('notebook_page_updated', array(
            'width' => 'narrow'
          )),
        ));
      } // if
      
      return $options;
    } // prepareOptionsFor
    
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if(!$this->validatePresenceOf('name')) {
        $errors->addError(lang('Notbook Page name is required'), 'name');
      } // if
      
      parent::validate($errors, true);
    } // validate
    
    /**
     * Save changes to DB
     *
     * @return boolean
     */
    function save() {
      if($this->isNew()) {
        $this->setVersion(1); // initial version number
      } // if
        
      parent::save();
    } // save
    
  }