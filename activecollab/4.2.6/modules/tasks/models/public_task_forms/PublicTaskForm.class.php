<?php

  /**
   * PublicTaskForm class
   *
   * @package activeCollab.modules.tasks
   * @subpackage models
   */
  class PublicTaskForm extends BasePublicTaskForm implements IRoutingContext, ISubscriptions {

    /**
     * Return true if this form has non-empty form description
     *
     * @return boolean
     */
    function hasBody() {
      return trim(strip_tags($this->getBody())) != '';
    } // hasBody

    // ---------------------------------------------------
    //  Interfaces
    // ---------------------------------------------------
  	
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
        $this->subscriptions = new ISubscriptionsImplementation($this);
      } // if      
      return $this->subscriptions;
    } // subscriptions  	
    
    /**
     * Cached project instance
     *
     * @var Project
     */
    private $project = false;
    
    /**
     * Return project instance
     * 
     * @return Project
     */
    function getProject() {
      if($this->project === false) {
        $this->project = $this->getProjectId() ? Projects::findById($this->getProjectId()) : null;
      } // if
      
      return $this->project;
    } // getProject
    
    /**
     * Set attributes
     * 
     * @param array $attributes
     */
    function setAttributes($attributes) {
      if(isset($attributes['slug']) && $attributes['slug']) {
        $attributes['slug'] = Inflector::slug(trim($attributes['slug']));
      } // if
      
      parent::setAttributes($attributes);
    } // setAttributes
    
    // ---------------------------------------------------
    //  Interfaces
    // ---------------------------------------------------
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'public_task_form';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('public_task_form_id' => $this->getId());
    } // getRoutingContextParams
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can change form status (enable or disable)
     * 
     * @param User $user
     * @return boolean
     */
    function canChangeStatus(User $user) {
      return $user->isAdministrator();
    } // canChangeStatus
    
    /**
     * Returns true if $user can update this form
     * 
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return $user->isAdministrator();
    } // canEdit
    
    /**
     * Returns true if $user can delete this form
     * 
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return $user->isAdministrator();
    } // canDelete
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return public URL
     * 
     * @return string
     */
    function getPublicUrl() {
      return Router::assemble('public_task_form_submit', array('public_task_form_slug' => $this->getSlug()));
    } // getPublicUrl
    
    /**
     * Return enable form URL
     * 
     * @return string
     */
    function getEnableUrl() {
      return Router::assemble('public_task_form_enable', array('public_task_form_id' => $this->getId()));
    } // getEnableUrl
    
    /**
     * Return disable form URL
     * 
     * @return string
     */
    function getDisableUrl() {
      return Router::assemble('public_task_form_disable', array('public_task_form_id' => $this->getId()));
    } // getDisableUrl
    
    // ---------------------------------------------------
    //  System
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
      
      $result['slug'] = $this->getSlug();
      $result['is_enabled'] = $this->getIsEnabled();
      $result['body'] = $this->getBody();
      
      $result['project'] = array('id' => $this->getProjectId());
      
      if($this->getProject() instanceof Project) {
        $result['project']['name'] = $this->getProject()->getName();
        $result['project']['url'] = $this->getProject()->getViewUrl();
      } // if
      
      $result['urls']['public'] = $this->getPublicUrl();
      $result['urls']['enable'] = $this->getEnableUrl();
      $result['urls']['disable'] = $this->getDisableUrl();
      
      $result['permissions']['change_status'] = $this->canChangeStatus($user);
      
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
     * Validate before save
     * 
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if($this->validatePresenceOf('name')) {
        if(!$this->validateUniquenessOf('name')) {
          $errors->addError('Form name needs to be unique', 'name');
        } // if
      } else {
        $errors->addError('Form name is required', 'name');
      } // if
      
      if($this->validatePresenceOf('slug')) {
        if(!$this->validateUniquenessOf('slug')) {
          $errors->addError(lang('Form slug needs to be unique'), 'slug');
        } // if
      } else {
        $errors->addError(lang('Form slug is required'), 'slug');
      } // if
      
      if(!$this->validatePresenceOf('project_id')) {
        $errors->addError(lang('Project is required'), 'project_id');
      } // if
    } // validate
    
  }