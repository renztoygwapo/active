<?php

  /**
   * Select weekday helper implementation
   *
   * @package angie.library.smarty
   * @subpackage plugins
   */
  
  /**
   * Select weekday control
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_my_time_week_day($params, &$smarty) {
    $user = array_required_var($params, 'user', true, 'IUser');
    $name = array_required_var($params, 'name');

    $value = (integer) array_var($params, 'value', 0, true);

    $first_week_day = ConfigOptions::getValueFor('time_first_week_day', $user);

    $week_days = Globalization::getDayNames();

    $first_week_day_found = false;
    $previous_week_days = array();

    $ordered_week_days = array();

    foreach($week_days as $k => $day_name) {
      if($k == $first_week_day || $first_week_day_found) {
        $ordered_week_days[$k] = $day_name;
        $first_week_day_found = true;
      } else {
        $previous_week_days[$k] = $day_name;
      } // if
    } // foreach

    foreach($previous_week_days as $k => $previous_week_day) {
      $ordered_week_days[$k] = $previous_week_day;
    } // foreach
    
    return HTML::selectFromPossibilities($name, $ordered_week_days, $value, $params);
  } // smarty_function_select_my_time_week_day