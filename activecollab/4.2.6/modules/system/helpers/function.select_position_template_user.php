<?php

/**
 * select_position_template_user helper implementation
 *
 * @package activeCollab.modules.system
 * @subpackage helpers
 */

/**
 * Render select position template user widget
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_select_position_template_user($params, &$smarty) {
	$project_template = array_var($params, 'template', null, true);
	$project_object_template = array_var($params, 'object', null, true);

	if(isset($params['class'])) {
		$params['class'] .= ' select_position_template_user';
	} else {
		$params['class'] = 'select_position_template_user';
	} // if

	require_once AUTHENTICATION_FRAMEWORK_PATH . '/helpers/function.select_user.php';

	if ($project_template instanceof ProjectTemplate) {
		$positions = ProjectObjectTemplates::findByType($project_template, 'Position');
		if (is_foreachable($positions)) {
			foreach($positions as $position) {
				if ($position instanceof ProjectObjectTemplate && $project_object_template instanceof ProjectObjectTemplate && $position->getValue('user_id') != $project_object_template->getValue('user_id')) {
					$params['exclude_ids'][] = $position->getValue('user_id');
				} // if
			} // foreach
		} // if
	} // if

	//$params['users'] = Users::getForSelectByConditions(DB::prepare(TABLE_PREFIX . 'users.state >= ?', STATE_VISIBLE));

	$params['optional'] = true;
	$params['optional_text'] = "-- None --";
	return smarty_function_select_user($params, $smarty);
} // smarty_function_select_position_template_user