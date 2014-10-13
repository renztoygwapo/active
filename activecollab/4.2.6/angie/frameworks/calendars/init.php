<?php

  /**
   * Calendars framework initialisation file
   *
   * @package angie.framework.calendars
   */
  
  const CALENDARS_FRAMEWORK = 'calendars';
  const CALENDARS_FRAMEWORK_PATH = __DIR__;

	// ---------------------------------------------------
	//  Load
	// ---------------------------------------------------

	require_once CALENDARS_FRAMEWORK_PATH . '/functions.php';

	defined('CALENDARS_FRAMEWORK_INJECT_INTO') or define('CALENDARS_FRAMEWORK_INJECT_INTO', 'system');
  
  AngieApplication::setForAutoload(array(
		'ICalendarEvents' => CALENDARS_FRAMEWORK_PATH . '/models/ICalendarEvents.class.php',
		'ICalendarEventsImplementation' => CALENDARS_FRAMEWORK_PATH . '/models/ICalendarEventsImplementation.class.php',

    'FwCalendar' => CALENDARS_FRAMEWORK_PATH . '/models/calendars/FwCalendar.class.php',
    'FwCalendars' => CALENDARS_FRAMEWORK_PATH . '/models/calendars/FwCalendars.class.php',

    'FwUserCalendar' => CALENDARS_FRAMEWORK_PATH . '/models/FwUserCalendar.class.php',
    'FwExternalCalendar' => CALENDARS_FRAMEWORK_PATH . '/models/FwExternalCalendar.class.php',

		'FwCalendarEvent' => CALENDARS_FRAMEWORK_PATH . '/models/calendar_events/FwCalendarEvent.class.php',
		'FwCalendarEvents' => CALENDARS_FRAMEWORK_PATH . '/models/calendar_events/FwCalendarEvents.class.php',

		'ICalendarContext' => CALENDARS_FRAMEWORK_PATH . '/models/calendar_context/ICalendarContext.class.php',
		'ICalendarContextImplementation' => CALENDARS_FRAMEWORK_PATH . '/models/calendar_context/ICalendarContextImplementation.class.php',

	  'ICalendarEventContext' => CALENDARS_FRAMEWORK_PATH . '/models/calendar_event_context/ICalendarEventContext.class.php',
	  'ICalendarEventContextImplementation' => CALENDARS_FRAMEWORK_PATH . '/models/calendar_event_context/ICalendarEventContextImplementation.class.php',

	  'ICalendarUsersContextImplementation' => CALENDARS_FRAMEWORK_PATH . '/models/ICalendarUsersContextImplementation.class.php',
	  'ICalendarStateImplementation' => CALENDARS_FRAMEWORK_PATH . '/models/ICalendarStateImplementation.class.php',
	  'ICalendarEventStateImplementation' => CALENDARS_FRAMEWORK_PATH . '/models/ICalendarEventStateImplementation.class.php',

	  'ICalendarEventActivityLogsImplementation' => CALENDARS_FRAMEWORK_PATH . '/models/ICalendarEventActivityLogsImplementation.class.php',

	  'vcalendar' => ANGIE_PATH . "/classes/icalendar/iCalCreator.class.php",
  ));