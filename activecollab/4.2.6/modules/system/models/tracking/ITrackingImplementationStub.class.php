<?php

  /**
   * Tracking implementation stub, used when tracking module is not installed
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ITrackingImplementationStub {
    
    /**
     * Returns true if parent object has time or expenses tracked
     *
     * @param IUser $user
     * @return boolean
     */
    function has(IUser $user) {
      return false;
    } // has
    
    /**
     * Returns true if parent object has billable time or expenses tracked
     * 
     * @param IUser $user
     * @return boolean
     */
    function hasBillable(IUser $user) {
      return false;
    } // hasBillable
    
    /**
     * Describe tracking of the parent object for $user
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @param array $result
     */
    function describe(IUser $user, $detailed, $for_interface, &$result) {
    	$result['object_time'] = 0;
    	$result['object_expenses'] = 0;

			$result['urls']['tracking'] = null;
			$result['permissions']['can_manage_tracking'] = false;
    } // describe

    /**
     * Describe tracking of the parent object for $user
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param array $result
     */
    function describeForApi(IUser $user, $detailed, &$result) {
      $result['object_time'] = 0;
      $result['object_expenses'] = 0;

      $result['urls']['tracking'] = null;
      $result['permissions']['can_manage_tracking'] = false;
    } // describeForApi
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can track time or expenses for parent object
     *
     * @param User $user
     * @return boolean
     */
    function canAdd(User $user) {
      return false;
    } // canAdd
    
    /**
     * Returns true if $user can track time and expenses for $for user
     *
     * @param User $user
     * @param User $for
     * @return boolean
     */
    function canAddFor(User $user, User $for) {
      return false;
    } // canAddFor
    
    /**
     * Returns true if $user can set or change estimate for the parent object
     *
     * @param User $user
     * @return boolean
     */
    function canEstimate(User $user) {
      return false;
    } // canEstimate
    
  }