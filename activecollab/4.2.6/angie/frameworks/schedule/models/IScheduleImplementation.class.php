<?php

  /**
   * schedule implementation that can be attached to any object
   *
   * @package angie.frameworks.schedule
   * @subpackage models
   */
  class IScheduleImplementation {
    
    /**
     * Parent object instance
     *
     * @var ISchedule
     */
    protected $object;
    
    /**
     * Construct schedule implementation and set parent object
     *
     * @param ISchedule $object
     */
    function __construct(ISchedule $object) {
      $this->object = $object;
    } // __construct
    
    /**
     * Checks if object has start and due dates or only due date
     * 
     * @return boolean
     */
    function isRange() {
    	return $this->object->fieldExists('start_on');
    } // isRange
    
    /**
     * Check if @user can reschedule $object
     * 
     * @param IUser $user
     */
    function canReschedule(IUser $user) {
    	return $this->object->canEdit($user);
    } // canReschedule
    
    /**
     * Return reschedule url
     * 
     * @return string
     * @throws NotImplementedError
     */
    function getRescheduleUrl() {
      if($this->object instanceof IRoutingContext) {
        return Router::assemble($this->object->getRoutingContext() . '_reschedule', $this->object->getRoutingContextParams());
      } else {
        throw new NotImplementedError(__CLASS__ . '::' . __METHOD__);
      } // if
    } // getRescheduleUrl
        
    /**
     * Describe tracking of the parent object for $user
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @param array $result
     */
    function describe(IUser $user, $detailed, $for_interface, &$result) {
			$result['urls']['reschedule'] = $this->getRescheduleUrl();
			
			if($for_interface) {
      	$result['permissions']['can_reschedule'] = $this->object->canEdit($user);
			} // if
    } // describe

    /**
     * Describe tracking of the parent object for $user
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param array $result
     */
    function describeForApi(IUser $user, $detailed, &$result) {
      if($detailed) {
        $result['urls']['reschedule'] = $this->getRescheduleUrl();
      } // if
    } // describeForApi
    
  }