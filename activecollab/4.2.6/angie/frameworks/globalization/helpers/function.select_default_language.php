<?php

  /**
   * Select language helper definition
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render select language box
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_default_language($params, &$smarty) {
    $name = array_required_var($params, 'name', true);
    $value = array_var($params, 'value', null, true);
    
    $languages = Languages::getIdNameMap();
    if (is_foreachable($languages)) {
    	foreach ($languages as $language_id => $language_name) {
    		$options[$language_id] = $language_name;
    	} // foreach
    } // if
    
    return HTML::radioGroupFromPossibilities($name, $options, $value, $params);
  } // smarty_function_select_default_language