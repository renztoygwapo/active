<?php

  /**
   * Calendar events interface
   *
   * @package angie.frameworks.calendars
   * @subpackage models
   */
  interface ICalendarEvents {
    
    /**
     * Return calendar events helper instance
     *
     * @return ICalendarEventsImplementation
     */
    function calendarEvents();
    
  }