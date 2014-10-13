<?php

/**
 * on_calendar_groups event handler implementation
 *
 * @package activeCollab.modules.system
 * @subpackage handlers
 */

/**
 * Handle on_calendar_groups event
 *
 * @param $user
 * @param $calendar_groups
 */
function system_handle_on_calendar_groups(&$user, &$calendar_groups, $all_for_admins_and_pms) {
	if (is_array($calendar_groups)) {
		$calendar_groups['projects'] = array(
			'label'     => lang('Project Calendars'),
			'calendars' => Projects::findForCalendarList($user, $all_for_admins_and_pms),
			'options'   => null,
		);
	} // if
} // system_handle_on_calendar_groups