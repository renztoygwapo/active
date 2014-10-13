<?php

	/**
	 * on_trash_map event handler
	 *
	 * @package activeCollab.framework.calendars
	 * @subpackage handlers
	 */

	/**
	 * Handle on_trash_map event
	 *
	 * @param $map
	 * @param User $user
	 */
	function calendars_handle_on_trash_map(&$map, User &$user) {

		$map = array_merge(
			(array) $map,
			(array) Calendars::getTrashedMap($user)
		);

		$map = array_merge(
			(array) $map,
			(array) CalendarEvents::getTrashedMap($user)
		);

	} // calendars_handle_on_trash_map