<?php

  /**
   * list_prepared_object helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Show a prepared object ready for import
   *
   * Parameters:
   * 
   * - object - Company instance
   * - type - An object type
   * - name - Name of the field
   * - master_checkbox - Master / slave checkbox
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_list_prepared_object($params, &$smarty) {
  	$type = array_var($params, 'type');
  	if(empty($type)) {
  		throw new InvalidParamError('type', $type, '$type is expected to be set correctly', true);
  	} // if

  	$name = array_var($params, 'name');
    if(empty($name)) {
      throw new InvalidParamError('name', $name, '$name is expected to be set correctly', true);
    } // if
    
    $master_checkbox = array_var($params, 'master_checkbox', true);

    $smarty->assign(array(
    	'_list_prepared_object_object' 					=> array_var($params, 'object'),
      '_list_prepared_object_name' 						=> $name,
      '_list_prepared_object_master_checkbox' => $master_checkbox
    ));
    
    if($type == "Company") {
    	return $smarty->fetch(get_view_path('_list_company', null, SYSTEM_MODULE));
    } elseif($type == "User") {
    	return $smarty->fetch(get_view_path('_list_user', null, SYSTEM_MODULE));
    } // if
  } // smarty_function_list_prepared_object