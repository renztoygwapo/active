<?php

  /**
   * Notebook class
   *
   * @package activeCollab.modules.notebooks
   * @subpackage models 
   */
  class Notebook extends ProjectObject implements IState, IAvatar, ISubscriptions, IAttachments, ISearchItem, ICanBeFavorite, ISharing {
    
    /**
     * Permission name
     * 
     * @var string
     */
    protected $permission_name = 'notebook';
    
    /**
     * Define fields used by this project object
     *
     * @var array
     */
    protected $fields = array(
      'id',
      'type', 'module',
      'project_id', 'milestone_id',
      'name', 'body', 
      'state', 'original_state', 'visibility', 'original_visibility',
      'created_on', 'created_by_id', 'created_by_name', 'created_by_email',
      'updated_on', 'updated_by_id', 'updated_by_name', 'updated_by_email',
      'version', 'position'
    );
    
    /**
     * Construct a new notebook
     *
     * @param mixed $id
     */
    function __construct($id = null) {
      $this->setModule(NOTEBOOKS_MODULE);
      parent::__construct($id);
    } // __construct
    
    /**
     * Cached inspector instance
     * 
     * @var INotebookInspectorImplementation
     */
    private $inspector = false;
    
    /**
     * Return inspector helper instance
     * 
     * @return INotebookInspectorImplementation
     */
    function inspector() {
      if($this->inspector === false) {
        $this->inspector = new INotebookInspectorImplementation($this);
      } // if
      
      return $this->inspector;
    } // inspector
    
    /**
     * Return proper type name in user's language
     *
     * @param boolean $lowercase
     * @param Language $language
     * @return string
     */
    function getVerboseType($lowercase = false, $language = null) {
      return $lowercase ? lang('notebook', null, false, $language) : lang('Notebook', null, null, $language);
    } // getVerboseType

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      $result = parent::describeForApi($user, $detailed);

      $result['subpages'] = array();

      $pages = NotebookPages::findByNotebook($this, $this->getState());

      if($pages) {
        foreach($pages as $page) {
          $result['subpages'][] = $page->describeForNotebookApi($user, $this->getState());
        } // foreach
      } // if

      return $result;
    } // describeForApi
    
    // ---------------------------------------------------
    //  Context
    // ---------------------------------------------------
    
    /**
     * Return object path
     */
    function getObjectContextPath() {
      return parent::getObjectContextPath() . '/notebooks/' . ($this->getVisibility() == VISIBILITY_PRIVATE ? 'private' : 'normal') . '/' . $this->getId();
    } // getContextPath
    
    // ---------------------------------------------------
    //  Move / Copy
    // ---------------------------------------------------
    
    /**
     * Copy this object to $project
     *
     * @param Project $project
     * @param array $update_attributes
     * @param boolean $bulk
     * @return Notebook
     * @throws Exception
     */
    function copyToProject(Project $project, $update_attributes = null, $bulk = false) {
      try {
        DB::beginWork('Moving notebook to project @ ' . __CLASS__);
        
        $copy = parent::copyToProject($project, $update_attributes, $bulk);
        
        if($copy instanceof Notebook) {
          NotebookPages::cloneToNotebook($this, $copy);
        } // if
        
        DB::commit('Notebook moved to project @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to move notebook to project @ ' . __CLASS__);
        throw $e;
      } // try
      
      return $copy;
    } // copyToProject
    
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------
    
    /**
     * State helper instance
     *
     * @var INotebookStateImplementation
     */
    private $state = false;
    
    /**
     * Return state helper instance
     *
     * @return INotebookStateImplementation
     */
    function state() {
      if($this->state === false) {
        $this->state = new INotebookStateImplementation($this);
      } // if
      
      return $this->state;
    } // state
    
    /**
     * Notebook avatar implementation instance for this object
     *
     * @var INotebookAvatarImplementation
     */
  	private $avatar;
    
    /**
     * Return avatar implementation for this object
     *
     * @return INotebookAvatarImplementation
     */
    function avatar() {
      if(empty($this->avatar)) {
        $this->avatar = new INotebookAvatarImplementation($this);
      } // if
      
      return $this->avatar;
    } // avatar
    
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
     * Cached search helper instance
     *
     * @var INotebookSearchItemImplementation
     */
    private $search = false;
    
    /**
     * Return search helper instance
     * 
     * @return INotebookSearchItemImplementation
     */
    function &search() {
      if($this->search === false) {
        $this->search = new INotebookSearchItemImplementation($this);
      } // if
      
      return $this->search;
    } // search

    /**
     * Cached sharing implementation helper
     *
     * @var INotebookSharingImplementation
     */
    private $sharing = false;

    /**
     * Return sharing helper instance
     *
     * @return INotebookSharingImplementation
     */
    function sharing() {
      if($this->sharing === false) {
        $this->sharing = new INotebookSharingImplementation($this);
      } // if

      return $this->sharing;
    } // sharing
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can add page to this notebook
     * 
     * @param IUser $user
     * @return boolean
     */
    function canAddPage(IUser $user) {
        return $this->canEdit($user);
    } // canAddPage
    
    // ---------------------------------------------------
    //  URls
    // ---------------------------------------------------
    
    /**
     * Return add notebook page URL
     * 
     * @return string
     */
    function getAddPageUrl() {
      return Router::assemble('project_notebook_pages_add', array(
        'project_slug' => $this->getProject() instanceof Project ? $this->getProject()->getSlug() : $this->getProjectId(), 
        'notebook_id' => $this->getId()
      ));
    } // getAddPageUrl
    
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
        $errors->addError(lang('Notbook is required'), 'name');
      } // if
      
      parent::validate($errors, true);
    } // validate
    
  }