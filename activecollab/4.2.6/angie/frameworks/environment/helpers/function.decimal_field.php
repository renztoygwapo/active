<?php

  /**
   * decimal_field helper implementation
   * 
   * @package angie.framework.environment
   * @subpackage helpers
   */

  /**
   * Render decimal field helper
   * 
   * @param array $param
   * @param Smarty $smarty
   */
  function smarty_function_decimal_field($params, &$smarty) {
    $name = array_required_var($params, 'name');
    $value = isset($params['value']) && $params['value'] ? $params['value'] : null;
    
    if(isset($params['class'])) {
      $params['class'] .= ' number_field';
    } else {
      $params['class'] = 'number_field';
    } // if
    
    if(!isset($params['step'])) {
      $params['step'] = '0.01';
    } // if
    
    return HTML::number($name, $value, $params);
  } // smarty_function_decimal