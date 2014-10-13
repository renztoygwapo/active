<?php

  /**
   * assemble handler implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Assemble URL
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_assemble($params, &$smarty) {
    $route = array_required_var($params, 'route', true);
    
    $options = null;
    
    if(isset($params['options'])) {
      $options_string = array_var($params, 'options', null, true);
      
      if($options_string) {
        parse_str($options_string, $options);
      } // if
    } // if
    
    return Router::assemble($route, $params, $options);
  } // smarty_function_assemble