<?php

  /**
   * Calendar context implementation
   *
   * @package angie.frameworks.calendars
   * @subpackage models
   */
  abstract class ICalendarContextImplementation {

    /**
     * Parent object instance
     *
     * @var ICalendarContext
     */
    protected $object;

    /**
     * Construct calendar context helper instance
     *
     * @param ICalendarContext $object
     */
    function __construct(ICalendarContext $object) {
      $this->object = $object;
    } // __construct

	  /**
	   * @param IUser $user
	   * @param bool $detailed
	   * @param bool $for_interface
	   * @param int $min_state
	   * @return mixed
	   */
	  abstract function describe(IUser $user, $detailed = false, $for_interface = false, $min_state = STATE_VISIBLE);

  }