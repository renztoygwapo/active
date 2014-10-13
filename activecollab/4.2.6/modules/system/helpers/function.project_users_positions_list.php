<?php

/**
 * project_users_list helper
 *
 * @package activeCollab.modules.system
 * @subpackage helpers
 */

/**
 * Render project users list
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_project_users_positions_list($params, &$smarty) {
	$project = array_required_var($params, 'project',true,'Project');
	$name = array_required_var($params, 'name', true);

	$print = '<div class="users_list"><ul>';

	$users = $project instanceof Project ? $project->users()->get() : null;

	if (is_foreachable($users)) {
		foreach($users as $user) {
			if ($user instanceof User) {
				$params['name'] = $name . "[" . $user->getId() . "]";
				$print .= '<li><span class="user_name">' . lang('for :name', array('name' => $user->getName() . ":")) . '</span>' . smarty_function_text_field($params, $smarty) . '</li>';
			} // if
		} // foreach
	} else {
		$print .= '<li><p class="empty_page">' . lang('No users defined') . '</p></li>';
	} // if

	return $print . '</ul></div>';
} // smarty_function_project_users_positions_list