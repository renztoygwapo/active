<?php

  /**
   * remember_me helper implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Render remember me checkbox field
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_remember_me($params, &$smarty) {
    $name = array_var($params, 'name', null, true);
    $label = array_var($params, 'label', null, true);
    $checked = array_var($params, 'checked', false, true);
    
    $smarty->assign(array(
  		'_remember_id' => HTML::uniqueId('remember_me'),
  		'_remember_name' => $name,
  		'_remember_label' => $label,
  		'_remember_checked' => $checked
  	));
  	
  	return $smarty->fetch(get_view_path('_remember_me', null, ENVIRONMENT_FRAMEWORK));
  } // smarty_function_remember_me