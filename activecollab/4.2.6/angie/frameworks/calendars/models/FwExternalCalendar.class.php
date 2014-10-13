<?php

  /**
   * External calendar instance
   *
   * @package angie.frameworks.calendars
   * @subpackage models
   */
  abstract class FwExternalCalendar extends Calendar {

	  const ICAL_FREQ_SECONDLY = 'SECONDLY';
	  const ICAL_FREQ_MINUTELY = 'MINUTELY';
	  const ICAL_FREQ_HOURLY = 'HOURLY';
	  const ICAL_FREQ_DAILY = 'DAILY';
	  const ICAL_FREQ_WEEKLY = 'WEEKLY';
	  const ICAL_FREQ_MONTHLY = 'MONTHLY';
	  const ICAL_FREQ_YEARLY = 'YEARLY';

	  /**
	   * Return verbose type name
	   *
	   * @param boolean $lowercase
	   * @param Language $language
	   * @return string
	   */
	  function getVerboseType($lowercase = false, $language = null) {
		  return $lowercase ? lang('calendar', null, true, $language) : lang('Calendar', null, true, $language);
	  } // getVerboseType

  }