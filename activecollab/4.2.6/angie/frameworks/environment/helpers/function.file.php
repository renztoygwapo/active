<?php

  /**
   * file helper implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Render file input
   * 
   * @param array $params
   * @param Smarty $smarty
   */
  function smarty_function_file($params, &$smarty) {
    static $counter = 1;
    
    return HTML::file((isset($params['name']) && $params['name'] ? $params['name'] : 'file_' . $counter++), $params);
  } // smarty_function_file