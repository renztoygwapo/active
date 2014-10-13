<?php

  /**
   * CalendarEvents class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  class CalendarEvents extends FwCalendarEvents {

	  /**
	   * Returns true if $user can create a new events
	   *
	   * @param User $user
	   * @param Calendar $calendar
	   * @return bool
	   */
	  static function canAdd(User $user, Calendar $calendar) {
		  if ($user instanceof Subcontractor || $user instanceof Client) {
			  return false;
		  } // if

		  return parent::canAdd($user, $calendar);
	  } // canAdd

	  /**
	   * Return if user can create new events
	   * before choose to which calendar want to add it
	   *
	   * @param User $user
	   * @return bool
	   */
	  static function canAddGlobal(User $user) {
		  if ($user instanceof Subcontractor || $user instanceof Client) {
			  return false;
		  } // if

		  return parent::canAddGlobal($user);
	  }
  
  }