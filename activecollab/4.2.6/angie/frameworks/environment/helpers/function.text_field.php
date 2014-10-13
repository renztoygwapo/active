<?php

  /**
   * text_field helper implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Render text input
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_text_field($params, &$smarty) {
    $name = array_required_var($params, 'name');
    $value = isset($params['value']) && $params['value'] ? $params['value'] : null;
    
    if(isset($params['class'])) {
      $params['class'] .= ' text_field';
    } else {
      $params['class'] = 'text_field';
    } // if
    
    $params['type'] = 'text';
    
    return HTML::input($name, $value, $params);
  } // smarty_function_text_field