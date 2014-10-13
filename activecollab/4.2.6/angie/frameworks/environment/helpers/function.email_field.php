<?php

  /**
   * email_field helper implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Render email input
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_email_field($params, &$smarty) {
    $name = array_required_var($params, 'name');
    $value = isset($params['value']) && $params['value'] ? $params['value'] : null;
    
    if(isset($params['class'])) {
      $params['class'] .= ' email_field';
    } else {
      $params['class'] = 'email_field';
    } // if
    
    return HTML::email($name, $value, $params);
  } // smarty_function_email_field