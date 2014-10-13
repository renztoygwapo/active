<?php

  /**
   * Common trecking object methods
   *
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  abstract class TrackingObject extends ApplicationObject implements IState, IActivityLogs, IObjectContext, IHistory {
    
    /**
     * Cached parent user instance
     *
     * @var IUser
     */
    private $user = false;
    
    /**
     * Return parent user
     *
     * @return IUser
     */
    function getUser() {
      if($this->user === false) {
        $user_id = $this->getFieldValue('user_id');
        
        if($user_id) {
          $this->user = Users::findById($user_id);
        } // if
        
        if(!($this->user instanceof User)) {
          if($this->getFieldValue('user_email')) {
            $this->user = new AnonymousUser($this->getFieldValue('user_name'), $this->getFieldValue('user_email'));
          } else {
            $this->user = null;
          } // if
        } // if
      } // if
      
      return $this->user;
    } // getUser
    
    /**
     * Set parent user
     *
     * @param IUser $user
     * @return IUser
     * @throws InvalidInstanceError
     */
    function setUser(IUser $user) {
      if($user instanceof IUser) {
        $this->setFieldValue('user_id', $user->getId());
        $this->setFieldValue('user_name', $user->getName());
        $this->setFieldValue('user_email', $user->getEmail());
      } else {
        throw new InvalidInstanceError('user', $user, 'IUser');
      } // if
      
      return $user;
    } // setUser
    
    /**
     * Return project that's parent of this tracking record
     * 
     * @return Project
     * @throws InvalidInstanceError
     */
    function getProject() {
      if($this->getParent() instanceof Project) {
        return $this->getParent();
      } elseif($this->getParent() instanceof ProjectObject) {
        return $this->getParent()->getProject();
      } else {
        throw new InvalidInstanceError('parent', $this->getParent(), array('Project', 'ProjectObject'));
      } // if
    } // getProject
    
    /**
     * Return verbose status of this expense
     *
     * @return string
     */
    function getBillableVerboseStatus() {
      switch($this->getBillableStatus()) {
        case BILLABLE_STATUS_NOT_BILLABLE:
          return lang('Not Billable');
        case BILLABLE_STATUS_BILLABLE:
          return lang('Billable');
        case BILLABLE_STATUS_PENDING_PAYMENT:
          return lang('Pending Payment');
        case BILLABLE_STATUS_PAID:
          return lang('Paid');
      } // switch
    } // getBillableVerboseStatus

    /**
     * Return true if this particular record is used in external resources (invoice for example)
     *
     * @return boolean
     */
    function isUsed() {
      if(AngieApplication::isModuleLoaded('invoicing')) {
        return $this->getBillableStatus() > BILLABLE_STATUS_BILLABLE && (bool) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'invoice_related_records WHERE parent_type = ? AND parent_id = ?', get_class($this), $this->getId());
      } else {
        return false;
      } // if
    } // isUsed
    
    /**
     * Mass set expense attributes
     *
     * @param array $attributes
     */
    function setAttributes($attributes) {
      if(isset($attributes['user_id']) && $attributes['user_id'] != $this->getUserId()) {
        if($attributes['user_id']) {
          $user = Users::findById($attributes['user_id']);
          
          if($user instanceof User) {
            $attributes['user_name'] = $user->getDisplayName();
            $attributes['user_email'] = $user->getEmail();
          } else {
            $attributes['user_id'] = 0;
          } // if
        } // if
        
        if(empty($attributes['user_id'])) {
          if(isset($attributes['user_name'])) {
            unset($attributes['user_name']);
          } // if
          
          if(isset($attributes['user_email'])) {
            unset($attributes['user_email']);
          } // if
        } // if
      } // if
      
      parent::setAttributes($attributes);
    } // setAttributes
    
    /**
     * Returns true if this record is billable
     *
     * @return boolean
     */
    function isBillable() {
      return $this->getBillableStatus() >= BILLABLE_STATUS_BILLABLE;
    } // isBillable
    
    /**
     * Returns true if this record is marked as paid
     *
     * @return boolean
     */
    function isPaid() {
      return $this->getBillableStatus() >= BILLABLE_STATUS_PAID;
    } // isPaid
    
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
      
      $result['billable_status'] = $this->getBillableStatus();
      $result['billable_status_verbose'] = $this->getBillableVerboseStatus();
      $result['value'] = $this->getValue();
      $result['record_date'] = $this->getRecordDate();
      $result['summary'] = $this->getSummary();
      
      $result['user'] = $this->getUser() instanceof IUser ? $this->getUser()->describe($user) : null;
      
      if ($detailed) {
        if($this->getParent() instanceof ITracking) {
          $result['parent'] = $this->getParent()->describe($user, false, $for_interface);

          if($this->getParent() instanceof Project) {
            $result['project'] = $result['parent']; // We already have the project described
          } else {
            $result['project'] = $this->getProject()->describe($user, false, $for_interface);
          } // if
        } else {
          $result['parent'] = null;
          $result['project'] = null;
        } // if
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

      unset($result['name']);

      if($detailed) {
        $result['parent'] = $this->getParent() instanceof ApplicationObject ? $this->getParent()->describeForApi($user) : null;

        if($this->getParent() instanceof Project) {
          $result['project'] = $result['parent'];
        } else {
          $result['project'] = $this->getProject() instanceof Project ? $this->getProject()->describeForApi($user) : null;
        } // if
      } else {
        $result['class'] = get_class($this);
      } // if

      $result['billable_status'] = $this->getBillableStatus();
      $result['value'] = $this->getValue();
      $result['record_date'] = $this->getRecordDate();
      $result['summary'] = $this->getSummary();

      $result['user'] = $this->getUser() instanceof IUser ? $this->getUser()->describeForApi($user) : null;

      return $result;
    } // describeForApi
    
    // ---------------------------------------------------
    //  Context
    // ---------------------------------------------------
    
    /**
     * Return object domain
     * 
     * @return string
     */
    function getObjectContextDomain() {
      return 'projects';
    } // getContextDomain
    
    /**
     * Return object path
     * 
     * @return string
     */
    function getObjectContextPath() {
      if($this->getParent() instanceof ITracking) {
        return $this->getParent()->getObjectContextPath() . '/tracking';
      } else {
        return $this->getProject()->getObjectContextPath() . '/tracking';
      } // if
    } // getContextPath
    
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------
    
    /**
     * Cached state helper instance
     *
     * @var IStateImplementation
     */
    private $state = false;
    
    /**
     * Return state implementation helper
     *
     * @return IStateImplementation
     */
    function state() {
      if($this->state === false) {
        $this->state = new IStateImplementation($this);
      } // if
      
      return $this->state;
    } // state
    
    /**
     * Cached instance of activity logs implementation
     *
     * @var ITrackingObjectActivityLogsImplementation
     */
    private $activity_logs = false;
    
    /**
     * Return activity logs implementation
     *
     * @return ITrackingObjectActivityLogsImplementation
     */
    function activityLogs() {
      if($this->activity_logs === false) {
        $this->activity_logs = new ITrackingObjectActivityLogsImplementation($this);
      } // if
      
      return $this->activity_logs;
    } // activityLogs
    
    /**
     * Cached history helper instance
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
        $this->history = new IHistoryImplementation($this, array('user_id', 'user_name', 'user_email', 'record_date', 'value', 'billable_status'));
      } // if
      
      return $this->history;
    } // history
    
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
      if($user->isProjectManager()) {
        return true;
      } // if

      if($this->getParent()->canView($user)) {
        if($this->getUserId() == $user->getId() || $this->getCreatedById() == $user->getId()) {
          return true; // Author or person for whose account this time has been logged
        } // if

        return $this->getProject()->isLeader($user) || $user->projects()->getPermission('tracking', $this->getProject()) >= ProjectRole::PERMISSION_ACCESS; // Project leader or user with at least access
      } else {
        return false;
      } // if
    } // canView
    
    /**
     * Returns true if $user can update this object
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      if($this->isUsed()) {
        return false;
      } // if

      // Project manager, project leader or person with management permissions in time and expenses section
      if($user->isProjectManager() || $this->getProject()->isLeader($user) || TrackingObjects::canManage($user, $this->getProject())) {
        return true;
      } // if

      // Author or person for whose account this time has been logged, editable within 30 days
      if($this->getParent()->canView($user) && ($this->getUserId() == $user->getId() || $this->getCreatedById() == $user->getId())) {
        return ($this->getCreatedOn()->getTimestamp() + (30 * 86400)) > DateTimeValue::now()->getTimestamp();
      } // if

      return false;
    } // canEdit

    /**
     * Returns true if $user can change billable status of specific record
     *
     * @param User $user
     * @return boolean
     */
    function canChangeBillableStatus($user) {
      return $this->canEdit($user);
    } // canChangeBillableStatus

    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
     	if($this->isNew()) {
      	if(!$this->validatePresenceOf('user_id')) {
       		$errors->addError(lang('Please select user'), 'user_id');
      	} // if
    	} // if
    	
    	if(!$this->validatePresenceOf('parent_type') || !$this->validatePresenceOf('parent_id')) {
    	  $errors->addError(lang('Please select parent'));
    	} // if
    	
    	if(!$this->validatePresenceOf('record_date')) {
    	  $errors->addError(lang('Please select record date'), 'record_date');
    	} // if
      
    	if($this->validatePresenceOf('value')) {
    	  if($this->getValue() <= 0) {
    	    $errors->addError(lang('Value is required'), 'value');
    	  } // if
    	} else {
    	  $errors->addError(lang('Value is required'), 'value');
    	} // if
    	
    	parent::validate($errors, true);
    } // validate
    
  }