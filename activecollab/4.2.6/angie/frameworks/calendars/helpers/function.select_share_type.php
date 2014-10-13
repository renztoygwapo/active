<?php

/**
 * select_share_type helper
 *
 * @package angie.frameworks.calendars
 * @subpackage helpers
 */

/**
 * Render select share type picker
 *
 * @param $params
 * @param $smarty
 * @return string
 */
function smarty_function_select_share_type($params, &$smarty) {
	$name = array_required_var($params, 'name', true);
	$value = (string) array_var($params, 'value', Calendar::DONT_SHARE, true);
	$select_users = $value == Calendar::SHARE_WITH_SELECTED_USERS ? true : false;
	$user = array_required_var($params, 'user', true, 'User');

	$select_users_params['exclude']  = $user->getId();
	$select_users_params['value']    = array_var($params, 'user_ids', array(), true);;
	$select_users_params['name']     = 'calendar[user_ids]';
	$select_users_params['user']     = $user;

	$selected_users = '<div class="selected_users_wrapper" style="' . (!$select_users ? "display: none;" : "") . '">';

	AngieApplication::useHelper('select_users', AUTHENTICATION_FRAMEWORK);
	$selected_users .= smarty_function_select_users($select_users_params, $smarty);

	$selected_users .= '</div>';

	$options = new NamedList(array(
		Calendar::SHARE_WITH_EVERYONE => lang('Everyone'),
		//Calendar::SHARE_WITH_TEAM_AND_SUBCONTRACTORS  => lang('Our Team and Subcontractors'),
		//Calendar::SHARE_WITH_TEAM_ONLY                => lang('Our Team Only'),
		Calendar::SHARE_WITH_MEMBERS_ONLY => lang('Members Only'),
		Calendar::SHARE_WITH_ADMINS_ONLY => lang('Administrators Only'),
		Calendar::SHARE_WITH_SELECTED_USERS => lang('Selected Users')
	));

	EventsManager::trigger('on_calendar_share_types', array(&$options));

	return HTML::optionalSelectFromPossibilities($name, $options, $value, $params, lang('-- do not share --'), 0) . $selected_users;
} // smarty_function_select_share_type