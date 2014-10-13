<?php

  /**
   * Framework level subtask implementation
   *
   * @package angie.frameworks.subtasks
   * @subpackage models
   */
  abstract class FwSubtask extends BaseSubtask implements IRoutingContext, IComplete, ISubscriptions, IAssignees, ILabel, IActivityLogs, ICreatedBy, IHistory, IState, IObjectContext {
    
    /**
     * Return task name (first few words from text)
     *
     * @return string
     */
    function getName() {
      return trim($this->getBody());
    } // getName
    
    /**
     * Return base type name
     * 
     * @param boolean $singular
     * @return string
     */
    function getBaseTypeName($singular = true) {
      return $singular ? 'subtask' : 'subtasks';
    } // getBaseTypeName
    
    /**
     * Return proper type name in user's language
     * 
     * We are putting strict definition here, so we don't mess up type name when 
     * subtask is subclassed in application
     *
     * @param boolean $lowercase
     * @param Language $language
     * @return string
     */
    function getVerboseType($lowercase = false, $language = null) {
      return $lowercase ? lang('subtask', null, true, $language) : lang('Subtask', null, true, $language);
    } // getVerboseType
    
    /**
     * Return formatted priority
     *
     * @param Language $language
     * @return string
     */
    function getFormattedPriority($language = null) {
      switch($this->getPriority()) {
        case PRIORITY_LOWEST:
          return lang('Lowest', null, true, $language);
        case PRIORITY_LOW:
          return lang('Low', null, true, $language);
        case PRIORITY_HIGH:
          return lang('High', null, true, $language);
        case PRIORITY_HIGHEST:
          return lang('Highest', null, true, $language);
        default:
          return lang('Normal', null, true, $language);
      } // switch
    } // getFormattedPriority
    
    /**
     * Prepare list of options that $user can use
     *
     * @param IUser $user
     * @param NamedList $options
     * @param string $interface
     * @return NamedList
     */
    protected function prepareOptionsFor(IUser $user, NamedList $options, $interface = AngieApplication::INTERFACE_DEFAULT) {
      if($this->canEdit($user)) {
        $options->add('edit', array(
        	'text' => lang('Edit'),
          'url' => $this->getEditUrl(),
        	'icon' => AngieApplication::getImageUrl('icons/12x12/edit.png', ENVIRONMENT_FRAMEWORK),
        	'classes' => array('for_active_only'),  
        ));
      } // if
      
      if($interface == AngieApplication::INTERFACE_DEFAULT) {
      	if($this->state()->canTrash($user)) {
	        $options->add('trash', array(
	          'text' => lang('Trash'),
	          'url' => $this->state()->getTrashUrl(),
	          'icon' => AngieApplication::getImageUrl('/icons/12x12/delete.png', ENVIRONMENT_FRAMEWORK)
	        ));
	      } // if
      	
      	$options->add('subscription', array(
	        'text' => lang('Subscription'), 
	      	'url' => '#',
	        'classes' => array('always_show', 'for_active_only'), 
	        'onclick' => new AsyncTogglerCallback(array(
	          'url' => $this->subscriptions()->getUnsubscribeUrl($user), 
	      		'text' => lang('Subscribed'), 
	      		'title' => lang('Click to Unsubscribe'), 
	      		'icon' => AngieApplication::getImageUrl('icons/12x12/object-subscription-active.png', SUBSCRIPTIONS_FRAMEWORK), 
	        ), array(
	          'url' => $this->subscriptions()->getSubscribeUrl($user),
	        	'text' => lang('Not Subscribed'),  
	      		'title' => lang('Click to Subscribe'), 
	      		'icon' => AngieApplication::getImageUrl('icons/12x12/object-subscription-inactive.png', SUBSCRIPTIONS_FRAMEWORK), 
	        ), $this->subscriptions()->isSubscribed($user)), 
	      ));
      } elseif($interface == AngieApplication::INTERFACE_PHONE) {
      	parent::prepareOptionsFor($user, $options, $interface);
      } // if
      
      EventsManager::trigger('on_subtask_options', array(&$this, &$user, &$options, $interface));
    } // prepareOptions
    
    /**
     * Set subtask attributes
     *
     * @param array $attributes
     */
    function setAttributes($attributes) {
      if(array_key_exists('label_id', $attributes)) {
        $this->label()->set($attributes['label_id']);
        unset($attributes['label_id']);
      } // if
      
      parent::setAttributes($attributes);
    } // setAttributes

    /**
     * Allow subclasses to include extra details in additional brief describe for API results
     *
     * @param $what
     * @return bool
     */
    function additionallyDescribeInBriefApiResponse($what) {
      return in_array($what, array('basic_urls', 'basic_permissions', 'state', 'complete'));
    } // additionallyDescribeInBriefApiResponse
    
    // ---------------------------------------------------
    //  Context
    // ---------------------------------------------------
    
    /**
     * Return object domain
     * 
     * @return string
     */
    function getObjectContextDomain() {
      return $this->getParent()->getObjectContextDomain();
    } // getContextDomain
    
    /**
     * Return object path
     * 
     * @return string
     */
    function getObjectContextPath() {
      return $this->getParent()->getObjectContextPath() . '/subtasks/' . $this->getId();
    } // getContextPath
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can access this task
     *
     * @param User $user
     * @return boolean
     */
    function canView(User $user) {
    	return $this->getParent() instanceof ISubtasks && $this->getParent()->canView($user);
    } // canView
    
    /**
     * Return true if $user can edit this object
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return $this->getParent() instanceof ISubtasks && ($this->getParent()->canEdit($user) || $this->getAssigneeId() == $user->getId());
    } // canEdit
    
    /**
     * Returns true only $user can delete parent object
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
    	return $this->getParent() instanceof ISubtasks && $this->getParent()->canDelete($user);
    } // canDelete
    
    // ---------------------------------------------------
    //  Interfaces implementation
    // ---------------------------------------------------
    
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
        $this->routing_context = $this->getParent()->getRoutingContext() . '_subtask';
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
        $parent_params = $this->getParent()->getRoutingContextParams();
        
        $this->routing_context_params = is_array($parent_params) ? array_merge($parent_params, array(
          'subtask_id' => $this->getId(), 
        )) : array('subtask_id' => $this->getId());
      } // if
      
      return $this->routing_context_params;
    } // getRoutingContextParams
    
    /**
     * Cached complete implementation instance
     *
     * @var ISubtaskCompleteImplementation
     */
    private $complete = false;
    
    /**
     * Return complete interface implementation
     *
     * @return ISubtaskCompleteImplementation
     */
    function complete() {
      if($this->complete === false) {
        $this->complete = new ISubtaskCompleteImplementation($this);
      } // if
      
      return $this->complete;
    } // complete
    
    /**
     * Created by implementation instance
     *
     * @var ICreatedByImplementation
     */
    private $created_by;
    
    /**
     * Return created by implementation instance
     *
     * @return ICreatedByImplementation
     */
    function createdBy() {
      if(empty($this->created_by)) {
        $this->created_by = new ICreatedByImplementation($this);
      } // if
      
      return $this->created_by;
    } // createdBy
    
    /**
     * Subscriptions helper instance
     *
     * @var ISubscriptionsImplementation
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
     * Cached assignees implementation instance
     *
     * @var IAssigneesImplementation
     */
    private $assignees = false;
    
    /**
     * Return assignees implementation instance for this object
     *
     * @return ISubtaskAssigneesImplementation
     */
    function assignees() {
      if($this->assignees === false) {
        $this->assignees = new ISubtaskAssigneesImplementation($this);
      } // if
      
      return $this->assignees;
    } // assignees
    
    /**
     * Cached instance of activity logs implementation
     *
     * @var ISubtaskActivityLogsImplementation
     */
    private $activity_logs = false;
    
    /**
     * Return activity logs implementation
     *
     * @return ISubtaskActivityLogsImplementation
     */
    function activityLogs() {
      if($this->activity_logs === false) {
        $this->activity_logs = new ISubtaskActivityLogsImplementation($this);
      } // if
      
      return $this->activity_logs;
    } // activityLogs
    
    /**
     * History helper instance
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
     * Cached state helper instance
     *
     * @var IStateImplementation
     */
    private $state = false;
    
    /**
     * Return state helper
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
     * Cached inspector instance
     * 
     * @var ISubtaskInspectorImplementation
     */
    private $inspector = false;
    
    /**
     * Return inspector helper instance
     * 
     * @return ISubtaskInspectorImplementation
     */
    function inspector() {
      if($this->inspector === false) {
        $this->inspector = new ISubtaskInspectorImplementation($this);
      } // if
      
      return $this->inspector;
    } // inspector
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if(!$this->validatePresenceOf('body')) {
        $errors->addError(lang('Subtask text is required'), 'body');
      } // if
    } // validate
    
    /**
     * Save to the database
     * 
     * @return boolean
     */
    function save() {
      $count_cache_affected = $this->isNew() || $this->isModifiedField('state') || $this->isModifiedField('completed_on');
      $search_index_affected = $this->isNew() || $this->isModifiedField('state') || $this->isModifiedField('body');

      parent::save();

      if($count_cache_affected || $search_index_affected) {
        $parent = $this->getParent();

        if($search_index_affected && $parent instanceof ISearchItem) {
          $parent->search()->create();
        } // if

        if($count_cache_affected && $parent instanceof ISubtasks) {
          AngieApplication::cache()->removeByObject($this->getParent(), 'subtasks_count');
        } // if
      } // if
    } // save
    
    /**
     * Delete subtask from database
     *
     * @return boolean
     * @throws Exception
     */
    function delete() {
      try {
        DB::beginWork('Deleting subtask @ ' . __CLASS__);
        
        ActivityLogs::deleteByParentAndAdditionalProperty($this->getParent(), 'subtask_id', $this->getId());
        parent::delete();
        
        DB::commit('Subtask deleted @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to delete subtask @ ' . __CLASS__);
        throw $e;
      } // try
      
      return true;
    } // delete
    
    /**
     * Use labels
     *
     * @return boolean
     */
    function useLabels() {
      return true;
    } // useLabels
    
    /**
     * Use priority field
     *
     * @return boolean
     */
    function usePriority() {
      return true;
    } // usePriority
    
  }