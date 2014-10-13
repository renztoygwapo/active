<?php

  /**
   * money_field helper implementation
   * 
   * @package angie.frameworks.globalization
   * @subpackage helpers
   */

  /**
   * Display money input field
   * 
   * @param array $param
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_money_field($params, &$smarty) {    
    $name = array_required_var($params, 'name');
    $value = isset($params['value']) && $params['value'] ? $params['value'] : null;
  	
    if(isset($params['class'])) {
      $params['class'] .= ' money';
    } else {
      $params['class'] = 'money';
    } // if
    
    $params['type'] = 'number';
    $params['step'] = '0.01';
    
    $set_as_readonly = array_var($params,'set_as_readonly',false,true);
    if($set_as_readonly) {
      $params['readonly'] = 'readonly';
    }//if
    
    return HTML::input($name, $value, $params);
  } // smarty_function_money_field