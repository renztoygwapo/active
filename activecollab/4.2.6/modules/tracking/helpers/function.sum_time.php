<?php

  /**
   * sum_time helper implementation
   * 
   * @package activeCollab.modules.tracking
   * @subpackage helpers
   */

  /**
   * Return summed object time
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_sum_time($params, &$smarty) {
    $user = array_required_var($params, 'user', true, 'IUser');
    $object = array_required_var($params, 'object', true, 'ApplicationObject');
    
    $only_billable = isset($params['mode']) && $params['mode'] == 'billable';
    
    // Tracking instance
    if($object instanceof ITracking) {
      $time = $only_billable ? 
        $object->tracking()->sumBillableTime($user,true) : 
        $object->tracking()->sumTime($user, true);
        
    // Milestone
    } elseif($object instanceof Milestone) {
      $time = $only_billable ? 
        TimeRecords::sumByMilestone($user, $object, BILLABLE_STATUS_BILLABLE) : 
        TimeRecords::sumByMilestone($user, $object); 
        
    //TrackingReport
    } elseif($object instanceof TrackingReport) {
      $time = $only_billable ?  
        $object->getTotalTime($user,BILLABLE_STATUS_BILLABLE) : 
        $object->getTotalTime($user);
    
      return $time . 'h';  
    // Unknown instance
    } else {
      throw new InvalidInstanceError('object', $object, array('ITracking', 'Milestone'));
    } // if
    
    AngieApplication::useHelper('hours', GLOBALIZATION_FRAMEWORK, 'modifier');
    
    return smarty_modifier_hours($time) . 'h';
  } // smarty_function_sum_time