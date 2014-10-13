<?php

/**
 * on_empty_trash event handler
 *
 * @package activeCollab.frameworks.calendars
 * @subpackage handlers
 */

/**
 * Handle on_empty_trash event
 *
 * @param NamedList $sections
 * @param User $user
 */
function calendars_handle_on_empty_trash(User &$user) {

	// delete trashed calendars
	Calendars::deleteTrashed($user);

	// delete trashed calendar events
	CalendarEvents::deleteTrashed($user);

} // calendars_handle_on_empty_trash