<?php

 /**
  * select_month helper
  *
  * @package angie.framework.environment
  * @subpackage helpers
  */
  
  /**
   * Render select month type control
   * 
   * Params:
   * 
   * - optional
   * - short
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_month($params, &$smarty) {
    $months = array_var($params, 'short', false, true) ? Globalization::getShortMonthNames() : Globalization::getMonthNames();
    
    $options = array();
    if(array_var($params, 'optional', false, true)) {
      $options[] = option_tag(lang('-- Select Month --'), '');
      $options[] = option_tag('', '');
    } // if
    
    $value = array_var($params, 'value', null, true);
    $default_current = array_var($params, 'default_current', false, true);

    if (is_null($value) && $default_current) {
      $value = date('n');
    } // if
    
    foreach($months as $num => $name) {
      $options[] = option_tag($name, $num, array(
        'selected' => $num == $value, 
      ));
    } // foreach

    return select_box($options, $params);
  } // smarty_function_select_day