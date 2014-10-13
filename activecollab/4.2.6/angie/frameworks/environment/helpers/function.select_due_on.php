<?php

  /**
   * Render date picker considering days off based on select_date widget
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_due_on($params, &$smarty) {
  	$params['skip_days_off'] = array_var($params, 'skip_days_off', true);
  	
  	AngieApplication::useHelper('select_date', ENVIRONMENT_FRAMEWORK);
  	return smarty_function_select_date($params, $smarty);
  } // smarty_function_select_date