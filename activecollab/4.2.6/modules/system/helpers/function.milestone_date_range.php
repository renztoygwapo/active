<?php

  /**
   * smarty_function_milestone_data_range helper implementation
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render milestone data range
   * 
   * Parameteres:
   * 
   * - object - Milestone
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_milestone_date_range($params, &$smarty) {
    $object = array_required_var($params, 'object', true, 'Milestone');
    
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    
    // Phone interface
    if($interface == AngieApplication::INTERFACE_PHONE) {
    	if($object instanceof IComplete && $object->complete()->isCompleted()) {
        return lang('Completed');
      } // if
      
      AngieApplication::useHelper('date', GLOBALIZATION_FRAMEWORK, 'modifier');
      
      if($object->getDueOn() instanceof DateValue) {
	      return smarty_modifier_date($object->getStartOn()) . '&mdash;' . smarty_modifier_date($object->getDueOn());
	    } else {
	      return lang('No Due Date');
	    } // if
    	
    // Other interfaces
    } else {
    	$smarty->assign(array(
	      '_milestone' => $object, 
	    ));
	    
	    return $smarty->fetch(get_view_path('_milestone_data_range', 'milestones', SYSTEM_MODULE), $interface);
    } // if
  } // smarty_function_milestone_data_range