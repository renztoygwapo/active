<?php

  /**
   * Complete interface implementation
   *
   * @package angie.frameworks.complete
   * @subpackage models
   */
  class ICompleteImplementation {
    
    /**
     * Parent object instance
     *
     * @var IComplete
     */
    protected $object;
    
    /**
     * Construct complete helper implementation
     *
     * @param IComplete $object
     */
    function __construct(IComplete $object) {
      $this->object = $object;
    } // __construct

    /**
     * Return notification subject prefix, so recipient can sort and filter notifications
     *
     * @return string
     */
    function getNotificationSubjectPrefix() {
      return '';
    } // getNotificationSubjectPrefix
    
    /**
     * Returns true if this object is marked as completed
     *
     * @return boolean
     */
    function isCompleted() {
      return $this->object->getCompletedOn() instanceof DateValue;
    } // isCompleted
    
    /**
     * Returns true if this object is open (not completed)
     *
     * @return boolean
     */
    function isOpen() {
      return !$this->isCompleted();
    } // isOpen
    
    // ---------------------------------------------------
    //  Utils
    // ---------------------------------------------------
    
    /**
     * Prepare object options
     * 
     * @param NamedList $options
     * @param IUser $user
     * @param string $interface
     */
    function prepareObjectOptions(NamedList $options, IUser $user, $interface = AngieApplication::INTERFACE_DEFAULT) {
    	if($this->canChangeStatus($user)) {
    		
	    	// Regular web browser request
	    	if($interface == AngieApplication::INTERFACE_DEFAULT) {
	    	  $options->add('complete_reopen', array(
		        'text' => 'Complete/Reopen', 
		        'url' => '#',
	    	  	'important' => true,
	    	  	'icon' => AngieApplication::getImageUrl(($this->isCompleted() ? 'icons/12x12/checkbox-unchecked.png' : 'icons/12x12/checkbox-checked.png'), COMPLETE_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT),
		        'onclick' => new AsyncTogglerCallback(array(
		          'text' => lang('Reopen'), 
		          'url' => $this->getOpenUrl(), 
		          'success_message' => lang(':type has been successfully reopened', array('type' => $this->object->getVerboseType())),
		          'success_event' => $this->object->getUpdatedEventName(),
		        ), array(
		          'text' => lang('Complete'), 
		          'url' => $this->getCompleteUrl(), 
		          'success_message' => lang(':type has been successfully completed', array('type' => $this->object->getVerboseType())),
		          'success_event' => $this->object->getUpdatedEventName(),
		        ), $this->isCompleted()), 
		      ));
		    	
		    // Phone device
	    	} elseif($interface == AngieApplication::INTERFACE_PHONE) {
	    		if($this->isOpen()) {
	    			$options->add('complete', array(
	            'text' => lang('Complete'),
	            'url' => $this->getCompleteUrl(),
	            'icon' => AngieApplication::getImageUrl('icons/navbar/complete.png', SYSTEM_MODULE, AngieApplication::INTERFACE_PHONE)
	          ));
	    		} else {
	    			$options->add('reopen', array(
	            'text' => lang('Reopen'),
	            'url' => $this->getOpenUrl(),
	            'icon' => AngieApplication::getImageUrl('icons/navbar/reopen.png', SYSTEM_MODULE, AngieApplication::INTERFACE_PHONE)
	          ));
	    		} // if
	    	} // if
    	} // if
    } // prepareObjectOptions
    
    /**
     * Describe complete information
     * 
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @param array $result
     */
    function describe(IUser $user, $detailed, $for_interface, &$result) {
    	if($this->isCompleted()) {
	    	$result['completed_on'] = $this->object->getCompletedOn();
	    	
	    	if($detailed) {
	    	  $result['completed_by'] = $this->getCompletedBy() instanceof IUser ? $this->getCompletedBy()->describe($user, false, $for_interface) : null;
	    	} else {
	    	  $result['completed_by_id'] = $this->object->getCompletedById();
	    	} // if
    	} else {
    		$result['completed_on'] = null;
    		$result['completed_by_id'] = null;
    	} // if
    	
      $result['is_completed'] = $result['completed_on'] instanceof DateTimeValue ? 1 : 0;
    	
    	if($this->object->fieldExists('priority')) {
    	  $result['priority'] = $this->object->getPriority();
    	  $result['urls']['update_priority'] = $this->getUpdatePriorityUrl();
    	} // if
    	
    	if($this->object->fieldExists('due_on')) {
    	  $result['due_on'] = $this->object->getDueOn();
    	} // if
    	
    	// Permissions
    	$result['permissions']['can_change_complete_status'] = $this->canChangeStatus($user);
    	
    	// URL-s
    	$result['urls']['open'] = $this->getOpenUrl();
      $result['urls']['complete'] = $this->getCompleteUrl();      
    } // describe

    /**
     * Describe complete information
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param array $result
     */
    function describeForApi(IUser $user, $detailed, &$result) {
      $result['is_completed'] = $this->object->getCompletedOn() instanceof DateTimeValue ? 1 : 0;

      if($detailed || $this->object->additionallyDescribeInBriefApiResponse('complete')) {
        if($result['is_completed']) {
          $result['completed_on'] = $this->object->getCompletedOn();
          $result['completed_by'] = $this->getCompletedBy() instanceof IUser ? $this->getCompletedBy()->describeForApi($user) : null;
        } // if

        if($this->object->fieldExists('priority')) {
          $result['priority'] = $this->object->getPriority();
          $result['urls']['update_priority'] = $this->getUpdatePriorityUrl();
        } // if

        if($this->object->fieldExists('due_on')) {
          $result['due_on'] = $this->object->getDueOn();
        } // if

        $result['permissions']['can_change_complete_status'] = $this->canChangeStatus($user);

        $result['urls']['open'] = $this->getOpenUrl();
        $result['urls']['complete'] = $this->getCompleteUrl();
      } // if
    } // describeForApi
    
    /**
     * Mark this object as completed
     *
     * @param User $by
     * @param Comment $comment
     * @return boolean
     */
    function complete(IUser $by, $comment = null) {
      if($this->isOpen()) {
        try {
          DB::beginWork('Marking object as completed @ ' . __CLASS__);
          
          $this->setCompletedBy($by);
          $this->object->setCompletedOn(DateTimeValue::now());
          $this->object->save();
          
          if($this->object instanceof ISubtasks) {
          	$this->object->subtasks()->completeOpenSubtasks($by);
          } // if
          
          if($this->object instanceof IActivityLogs && !$this->object->activityLogs()->isGagged()) {
            $this->object->activityLogs()->logCompletion($by);
          } // if
          
          EventsManager::trigger('on_object_completed', array(&$this->object, &$by, &$comment));
          
          DB::commit('Object marked as completed @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to mark object as completed @ ' . __CLASS__);
          throw $e;
        } // try
      } // if
      
      return true;
    } // complete
    
    /**
     * Mark this item as opened
     *
     * @param User $by
     * @param Comment $comment
     * @return boolean
     */
    function open(IUser $by, $comment = null) {
      if($this->isCompleted()) {
        try {
          DB::beginWork('Marking object as open @ ' . __CLASS__);
          
          $this->setCompletedBy(null);
          $this->object->setCompletedOn(null);
          $this->object->save();
          
          if($this->object instanceof IActivityLogs && !$this->object->activityLogs()->isGagged()) {
            $this->object->activityLogs()->logReopening($by);
          } // if
          
          EventsManager::trigger('on_object_opened', array(&$this->object, &$by));
          
          DB::commit('Object marked as open @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to mark object as open @ ' . __CLASS__);
          throw $e;
        } // try
      } // if
      
      return true;
    } // open
    
    /**
     * Return user who completed this object
     *
     * @return IUser|null
     */
    function getCompletedBy() {
      return $this->object->getUserFromFieldSet('completed_by');
    } // getCompletedBy
    
    /**
     * Set person who completed this object
     *
     * @param mixed $completed_by
     * @return mixed
     */
    private function setCompletedBy($completed_by) {
      return $this->object->setUserFromFieldSet($completed_by, 'completed_by');
    } // setCompletedBy
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return complete object URL
     *
     * @return string
     * @throws NotImplementedError
     */
    function getCompleteUrl() {
      if($this->object instanceof IRoutingContext) {
        return Router::assemble($this->object->getRoutingContext() . '_complete', $this->object->getRoutingContextParams());
      } else {
        throw new NotImplementedError(__METHOD__);
      } // if
    } // getCompleteUrl
    
    /**
     * Return open object URL
     *
     * @return string
     * @throws NotImplementedError
     */
    function getOpenUrl() {
      if($this->object instanceof IRoutingContext) {
        return Router::assemble($this->object->getRoutingContext() . '_reopen', $this->object->getRoutingContextParams());
      } else {
        throw new NotImplementedError(__METHOD__);
      } // if
    } // getOpenUrl
    
    /**
     * Return update priority url
     * 
     * @return string
     * @throws NotImplementedError
     */
    function getUpdatePriorityUrl() {
    	if (!$this->object->fieldExists('priority')) {
    		return false;
    	} // if
    	
      if($this->object instanceof IRoutingContext) {
        return Router::assemble($this->object->getRoutingContext() . '_update_priority', $this->object->getRoutingContextParams());
      } else {
        throw new NotImplementedError(__METHOD__);
      } // if
    }
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Check if $user can change completion status
     *
     * @param IUser $user
     * @return boolean
     */
    function canChangeStatus(IUser $user) {
      if($this->object instanceof IState && $this->object->getState() < STATE_VISIBLE) {
        return false;
      } // if
      
      return $this->object->canEdit($user);
    } // canChangeStatus
    
  }