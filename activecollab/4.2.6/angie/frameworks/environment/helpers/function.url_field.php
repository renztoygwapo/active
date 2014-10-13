<?php

  /**
   * url_field helper implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Render url input
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_url_field($params, &$smarty) {
    $name = array_required_var($params, 'name');
    $value = isset($params['value']) && $params['value'] ? $params['value'] : null;
    
    if(isset($params['class'])) {
      $params['class'] .= ' url_field';
    } else {
      $params['class'] = 'url_field';
    } // if
    
    return HTML::url($name, $value, $params);
  } // smarty_function_url_field