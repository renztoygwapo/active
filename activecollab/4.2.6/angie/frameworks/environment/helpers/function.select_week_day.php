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
  function smarty_function_select_week_day($params, &$smarty) {
    $name = array_required_var($params, 'name');
    $value = (integer) array_var($params, 'value', 0, true);
    
    return HTML::selectFromPossibilities($name, Globalization::getDayNames(), $value, $params);
  } // smarty_function_select_week_day