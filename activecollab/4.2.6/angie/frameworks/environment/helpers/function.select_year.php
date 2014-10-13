<?php

 /**
  * select_year helper
  *
  * @package angie.framework.environment
  * @subpackage helpers
  */
  
  /**
   * Render select year type control
   * 
   * Params:
   * 
   * - from - From year, calculated automatically if missing
   * - to - To year, calculated automatically if missing
   * - past - Yes by default
   * - future - Yes by default
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_year($params, &$smarty) {
    $current_year = date('Y');
    
    $from = (integer) array_var($params, 'from', null, true);
    if(empty($from)) {
      $from = array_var($params, 'past', true, true) ? $current_year - 20 : $current_year;
    } // if
    
    $to = (integer) array_var($params, 'to', null, true);
    if(empty($to)) {
      $to = array_var($params, 'future', true, true) ? $current_year + 20 : $current_year;
    } // if
    
    $value = array_var($params, 'value', null, true);
    $default_current = array_var($params, 'default_current', false, true);

    if (is_null($value) && $default_current) {
      $value = $current_year;
    } // if
    
    $options = array();
    if(array_var($params, 'optional', false, true)) {
      $options[] = option_tag(lang('-- Select Year --'), '');
      $options[] = option_tag('', '');
    } // if
    
    for($i = $from; $i <= $to; $i++) {
      $options[] = option_tag($i, $i, array(
        'selected' => $value == $i, 
      ));
    } // for
    
    return select_box($options, $params);
  } // smarty_function_select_day