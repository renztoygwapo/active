<?php

  /**
   * select_tags helper implementation
   * 
   * @package angie.frameworks.search
   * @subpackage helpers
   */

  /**
   * Render select_tags widget
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_tags($params, &$smarty) {
  	$value = array_var($params, 'value', null, true);
    if(is_array($value)) {
      $value = implode(', ', $value);
    } // if
    
    $params['type'] = 'text';
    $params['value'] = $value;
    
    return HTML::input(@$params['name'], @$params['value'], $params);
  } // smarty_function_select_tags