<?php

/**
 * on_calendar_events event handler implementation
 *
 * @package activeCollab.modules.system
 * @subpackage handlers
 */

/**
 * Handle on_calendar_events event
 *
 * @param $events
 * @param $from
 * @param $to
 * @param $user
 * @param $assigned
 * @param $all_for_admins_and_pms
 * @param $include_completed_and_archived
 */
function system_handle_on_calendar_events(&$events, $from, $to, $user, $assigned, $all_for_admins_and_pms, $include_completed_and_archived) {
	$from = DateValue::makeFromString($from);
	$to = DateValue::makeFromString($to);

	// find all milestones
	$milestones = Milestones::findForCalendarByUser($user, $from, $to, $assigned, $all_for_admins_and_pms, $include_completed_and_archived);
	if (is_foreachable($milestones)) {
		$events = array_merge($events, $milestones);
	} // if

	// find all tasks
	$tasks = Tasks::findForCalendarByUser($user, $from, $to, $assigned, $all_for_admins_and_pms, $include_completed_and_archived);
	if (is_foreachable($tasks)) {
		$events = array_merge($events, $tasks);
	} // if

	// find all subtasks
	$subtasks = Subtasks::findForCalendarByUser($user, $from, $to, $assigned, $all_for_admins_and_pms, $include_completed_and_archived);
	if (is_foreachable($subtasks)) {
		$events = array_merge($events, $subtasks);
	} // if

} // system_handle_on_calendar_events