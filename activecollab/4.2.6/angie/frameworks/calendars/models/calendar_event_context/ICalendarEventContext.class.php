<?php


	/**
	 * Calendar event context interface
	 *
	 * @package angie.frameworks.calendars
	 * @subpackage models
	 */
	interface ICalendarEventContext {

		/**
		 * Return calendar helper instance
		 *
		 * @return ICalendarEventContextImplementation
		 */
		function calendar_event_context();

	}