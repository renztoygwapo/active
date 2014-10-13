<?php

  /**
   * object_history helper implementation
   *
   * @package angie.frameworks.history
   * @subpackage helpers
   */

  /**
   * Render object's history
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_object_history($params, &$smarty) {
    $object = array_required_var($params, 'object', true, 'IHistory');
    $user = array_required_var($params, 'user', true, 'IUser');
    
    $modifications = $object->history()->render($user, $smarty);
    $smarty->assign(array(
      '_history_modifications' => $modifications, 
    	'_history_object' => $object,
    ));
      
		return $smarty->fetch(get_view_path('_object_history', null, HISTORY_FRAMEWORK));
  } // smarty_function_object_history