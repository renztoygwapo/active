<?php

	/**
	 * Calendar event context implementation
	 *
	 * @package angie.frameworks.calendars
	 * @subpackage models
	 */
	abstract class ICalendarEventContextImplementation {

		/**
		 * Parent object instance
		 *
		 * @var ICalendarEventContext
		 */
		protected $object;

		/**
		 * Construct calendar event context helper instance
		 *
		 * @param ICalendarEventContext $object
		 */
		function __construct(ICalendarEventContext $object) {
			$this->object = $object;
		} // __construct

		/**
		 * Describe object as calendar event
		 *
		 * @param IUser $user
		 * @param bool $detailed
		 * @param bool $for_interface
		 * @param int $min_state
		 * @return mixed
		 */
		abstract function describe(IUser $user, $detailed = false, $for_interface = false, $min_state = STATE_VISIBLE);

	}