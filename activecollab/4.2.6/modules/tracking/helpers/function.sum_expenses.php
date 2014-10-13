<?php

  /**
   * sum_expenses helper implementation
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
  function smarty_function_sum_expenses($params, &$smarty) {
    $user = array_required_var($params, 'user', true, 'IUser');
    $object = array_required_var($params, 'object', true, 'ApplicationObject');
    
    $only_billable = isset($params['mode']) && $params['mode'] == 'billable';
    
    $currency = Currencies::getDefault();
    AngieApplication::useHelper('money', GLOBALIZATION_FRAMEWORK, 'modifier');
    
    // Tracking object
    if($object instanceof ITracking) {
      if($object instanceof Project) {
        $currency = $object->getCurrency();
      } elseif($object instanceof Task) {
        $currency = $object->getProject()->getCurrency();
      } // if
      
      $expenses = $only_billable ? 
        $object->tracking()->sumBillableExpenses($user, true) : 
        $object->tracking()->sumExpenses($user, true);
        
    // Milestone
    } else if($object instanceof Milestone) {
      $currency = $object->getProject()->getCurrency();
      
      $expenses = $only_billable ? 
        Expenses::sumByMilestone($user, $object, BILLABLE_STATUS_BILLABLE) : 
        Expenses::sumByMilestone($user, $object);
     
    //TrackingReport
    } elseif($object instanceof TrackingReport) {
      $expenses = $only_billable ?  
        $object->getTotalExpenses($user,BILLABLE_STATUS_BILLABLE) : 
        $object->getTotalExpenses($user); 
    
        if(is_foreachable($expenses)) {
          foreach ($expenses as $currency_code => $expense) {
            $currency = Currencies::findByCode($currency_code);
            $exp .= $exp ? ' + ' : '';
            $exp .= smarty_modifier_money($expense, $currency);
          }//foreach
        } else {
          $exp = 0;
        }//if
          return $exp;
        
    // Unknown instance
    } else {
      throw new InvalidInstanceError('object', $object, array('ITracking', 'Milestone'));
    } // if
    
    return smarty_modifier_money($expenses, $currency);
  } // smarty_function_sum_expenses