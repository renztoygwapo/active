<?php

/**
 * on_trash_sections event handler
 *
 * @package activeCollab.frameworks.calendars
 * @subpackage handlers
 */

/**
 * Handle on_trash_sections event
 *
 * @param NamedList $sections
 * @param array $map
 * @param User $user
 */
function calendars_handle_on_trash_sections(NamedList &$sections, &$map, User &$user) {

	// time records in trash
	$trashed_calendars = Calendars::findTrashed($user, $map);
	if (is_foreachable($trashed_calendars)) {
		$sections->add('calendars', array(
			'label' => lang('Calendars'),
			'count' => count($trashed_calendars),
			'items' => $trashed_calendars
		));
	} // if

	$trashed_calendar_events = CalendarEvents::findTrashed($user, $map);
	if (is_foreachable($trashed_calendar_events)) {
		$sections->add('calendar_events', array(
			'label' => lang('Calendar Events'),
			'count' => count($trashed_calendar_events),
			'items' => $trashed_calendar_events
		));
	} // if

} // calendars_handle_on_trash_sections