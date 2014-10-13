<?php

  /**
   * Select currency picker
   * 
   * @package angie.frameworks.globalization
   * @subpackage helpers
   */

  /**
   * Render select currency box
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_currency($params, &$smarty) {
    $possibilities = array();
    
    $default_currency_id = null;
    $default_currency_name = null;
    
    $rows = DB::execute('SELECT id, name, code, is_default FROM ' . TABLE_PREFIX . 'currencies ORDER BY name');
    if($rows) {
      foreach($rows as $row) {
        if($row['is_default'] && $default_currency_id === null) {
          $default_currency_id = (integer) $row['id'];
          $default_currency_name = $row['name'];
        } // if
        
        $possibilities[(integer) $row['id']] = "$row[name] ($row[code])";
      } // foreach
    } // if
    
    if(empty($default_currency_id) && count($possibilities) > 0) {
      $default_currency_id = first($possibilities, true);
      $default_currency_name = $possibilities[$default_currency_id];
    } // if
    
    $optional = array_var($params, 'optional', false, true);
    
    $name = array_required_var($params, 'name', true);
    $value = array_var($params, 'value', ($optional ? null : $default_currency_id), true);
    
    if($optional) {
      $optional_text = $default_currency_name ? 
        lang('System Default (:default)', array('default' => $default_currency_name)) : 
        lang('System Default');
      
      return HTML::optionalSelectFromPossibilities($name, $possibilities, $value, $params, $optional_text);
    } else {
      return HTML::selectFromPossibilities($name, $possibilities, $value, $params);
    } // if
  } // smarty_function_select_currency