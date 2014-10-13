<?php

/**
 * Select firewall rule users helper
 *
 * @package activeCollab.modules.authentication
 * @subpackage helpers
 */

/**
 * Render select firewall rule users helper
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
function smarty_function_select_firewall_rule_users($params, &$smarty) {
	if(isset($params['class'])) {
		$params['class'] .= ' select_firewall_rule_users';
	} else {
		$params['class'] = 'select_firewall_rule_users';
	} // if

	require_once AUTHENTICATION_FRAMEWORK_PATH . '/helpers/function.select_user.php';

	$params['users'] = Users::getForSelectByConditions(DB::prepare(TABLE_PREFIX . 'users.state >= ?', STATE_VISIBLE));

	$params['optional'] = true;
	$params['optional_text'] = "-- None --";
	return smarty_function_select_user($params, $smarty);
} // smarty_function_select_firewall_rule_users