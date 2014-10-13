<?php

  /**
   * select_datetime_format helper defintiion
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render select datetime format widget
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_date_format($params, &$smarty) {
    $reference = DateTimeValue::makeFromString(date('Y') . '-08-21 20:45:15')->getTimestamp();
    $e = DIRECTORY_SEPARATOR == '\\' ? '%d' : '%e'; // Windows does not support %e
    
    $possibilities = array(
      "%b $e, %Y" => strftime("%b $e, %Y", $reference),
      "%a, %b $e, %Y" => strftime("%a, %b $e, %Y", $reference),
      "$e %b %Y" => strftime("$e %b %Y", $reference),
      "%Y/%m/%d" => strftime("%Y/%m/%d", $reference), // YYYY/MM/DD
      "%m/%d/%Y" => strftime("%m/%d/%Y", $reference), // MM/DD/YYYY
      "%d/%m/%y" => strftime("%d/%m/%y", $reference), // DD/MM/YY
      "%d/%m/%Y" => strftime("%d/%m/%Y", $reference), // DD/MM/YYYY
    );
    
    $name = array_required_var($params, 'name', true);
  	$value = array_var($params, 'value', null, true);
  	$optional = array_var($params, 'optional', false, true);
  	
  	if($optional) {
  	  return HTML::optionalSelectFromPossibilities($name, $possibilities, $value, $params, lang('-- System Default (:value) --', array('value' => strftime(ConfigOptions::getValue('format_date'), $reference))), '');
  	} else {
  	  return HTML::selectFromPossibilities($name, $possibilities, $value, $params);
  	} // if
  } // smarty_function_select_date_format