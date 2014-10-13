<?php

  /**
   * file_field helper implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
	 * Render input file
   * 
   * Parameters:
   * 
	 * - name - field name
   * - value - initial value
	 * - array of additional attributes
   *
	 * @param array $params
   * @param Smarty $smarty
	 * @return string
   */
  function smarty_function_file_field($params, &$smarty) {
    $name = array_required_var($params, 'name');
    
    if(isset($params['class'])) {
      $params['class'] .= ' file_field';
    } else {
      $params['class'] = 'file_field';
    } // if
    
    $params['type'] = 'file';

    AngieApplication::useWidget('form', ENVIRONMENT_FRAMEWORK);
    
    return HTML::file($name, $params);
  } // smarty_function_text_field