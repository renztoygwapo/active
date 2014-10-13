<?php

  /**
   * CalendarEvent class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  class CalendarEvent extends FwCalendarEvent {

	  /**
	   * Return verbose type name
	   *
	   * @param boolean $lowercase
	   * @param Language $language
	   * @return string
	   */
	  function getVerboseType($lowercase = false, $language = null) {
		  return $lowercase ? lang('calendar event', null, true, $language) : lang('Calendar Event', null, true, $language);
	  } // getVerboseType
    
  }