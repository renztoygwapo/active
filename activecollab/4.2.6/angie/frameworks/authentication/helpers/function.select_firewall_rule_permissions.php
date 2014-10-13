<?php

/**
 * Select firewall rule permissions helper
 *
 * @package activeCollab.modules.authentication
 * @subpackage helpers
 */

/**
 * Render select rule helper
 *
 * Params:
 *
 * - name - Select name attribute
 * - value - ID of selected role
 * - optional - Wether value is optional or not
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_select_firewall_rule_permissions($params, &$smarty) {
	$name = array_var($params, 'name', null, true);
	$value = array_var($params, 'value', null, true);

	// Prepare options
	$options = array();

	$permissions = array('allow', 'deny');
	if($permissions) {
		foreach($permissions as $permission) {
			$options[] = HTML::optionForSelect(ucfirst($permission), $permission, $permission == $value, array(
				'class' => 'object_option'
			));
		} // foreach
	} // if

	$result = array_var($params, 'optional', false, true) ?
		HTML::optionalSelect($name, $options, $params, lang('None')) :
		HTML::select($name, $options, $params);

	return $result;
} // smarty_function_select_firewall_rule_permissions