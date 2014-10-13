<?php

  /**
   * select_group_task_report_by helper implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render select group for task report
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_group_task_report_by($params, &$smarty) {
    $name = array_required_var($params, 'name', true);
    $value = array_var($params, 'value', 'milestone_id');
    
    $possibilities = array( 
      'milestone_id' => lang("Milestone"),
    	'category_id' => lang("Category"),
    	'label_id' => lang("Label"),
	    'priority' => lang("Priority"),
	    'completed_on'  => lang("Status"),
	  );
    
    $options = array();
    
    foreach($possibilities as $k => $v) {
      $options[] = HTML::optionForSelect($v, $k, $k == $value);
    } // foreach
    
    return HTML::select($name, $options, $params);
    
  } // smarty_function_select_group_assignments_by