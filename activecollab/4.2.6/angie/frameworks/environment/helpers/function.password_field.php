<?php

  /**
   * password_field helper implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Render password field
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_password_field($params, &$smarty) {
    $label = array_var($params, 'label', null, true);
    
    $params['type'] = 'password';
    if(isset($params['class'])) {
      $params['class'] .= ' password_field';
    } else {
      $params['class'] = 'password_field';
    } // if
    
    if($label) {
      if(!isset($params['id'])) {
        $params['id'] = HTML::uniqueId('password_field');
      } // if
      
      $prefix = HTML::label($label, $params['id'], (boolean) array_var($params, 'required'), array('class' => 'main_label'));
    } else {
      $prefix = '';
    } // if
    
    return $prefix . HTML::openTag('input', $params);
  } // smarty_function_password_field