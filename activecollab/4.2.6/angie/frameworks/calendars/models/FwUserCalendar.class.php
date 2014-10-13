<?php

  /**
   * User calendar instance
   *
   * @package angie.frameworks.calendars
   * @subpackage models
   */
  abstract class FwUserCalendar extends Calendar {

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

	  function describe(IUser $user, $detailed = false, $for_interface = false) {
		  $result = parent::describe($user, $detailed, $for_interface);

//		  $result['id'] = $this->getId();
		  $result['group_id'] = 1;
//		  $result['name'] = $this->getName();
//		  $result['color'] = $this->getColor();

		  return $result;
	  } // describe

  }