<?php

  /**
   * Calendar context interface
   *
   * @package angie.frameworks.calendars
   * @subpackage models
   */
  interface ICalendarContext {

    /**
     * Return calendar helper instance
     *
     * @return ICalendarContextImplementation
     */
    function calendar_context();

  }