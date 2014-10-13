<?php

  /**
   * select_timezone helper implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Render select box for timezone
   * 
   * Parameters:
   * 
   * - all HTML attributes
   * - value - value of selected timezone
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_timezone($params, &$smarty) {
    $name = array_required_var($params, 'name');
    $value = array_var($params, 'value', 0, true);
    
    $possibilities = array();
    
    foreach(Globalization::getTimezones() as $offset => $timezone) {
      $possibilities[$offset] = Globalization::getFormattedTimezone($offset, implode(', ', $timezone));
    } // foreach
    
    if(array_var($params, 'optional', true, true)) {
      return HTML::optionalSelectFromPossibilities($name, $possibilities, $value, $params, lang('-- System Default (:value) --', array(
        'value' => Globalization::getFormattedTimezone(ConfigOptions::getValue('time_timezone')),
      )), '');
    } else {
      return HTML::selectFromPossibilities($name, $possibilities, $value, $params);
    } // if
  } // smarty_function_select_timezone