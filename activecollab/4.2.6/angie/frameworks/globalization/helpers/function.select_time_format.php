<?php

  /**
   * select_time_format helper implementation
   * 
   * @package angie.frameworks.globalization
   * @subpackage helpers
   */

  /**
   * Render select time format picker
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_time_format($params, &$smarty) {
    $reference = DateTimeValue::makeFromString(date('Y') . '-11-21 20:45:15')->getTimestamp();
    
    $possibilities = array(
      '%I:%M %p' => strftime('%I:%M %p', $reference),
      '%H:%M' => strftime('%H:%M', $reference),
    );
  	
  	$name = array_required_var($params, 'name', true);
  	$value = array_var($params, 'value', null, true);
  	$optional = array_var($params, 'optional', false, true);
  	
  	if($optional) {
  	  return HTML::optionalSelectFromPossibilities($name, $possibilities, $value, $params, lang('-- System Default (:value) --', array('value' => strftime(ConfigOptions::getValue('format_time'), $reference))), '');
  	} else {
  	  return HTML::selectFromPossibilities($name, $possibilities, $value, $params);
  	} // if
  } // smarty_function_select_time_format