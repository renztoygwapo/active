<?php

  /**
   * Base calendar events implementation
   *
   * @package angie.frameworks.calendars
   * @subpackage models
   */
  class ICalendarEventsImplementation {
    
    /**
     * Parent object instance
     *
     * @var ICalendarEvents
     */
    protected $object;
    
    /**
     * Construct calendar events helper
     *
     * @param ICalendarEvents $object
     */
    function __construct(ICalendarEvents $object) {
      $this->object = $object;
    } // __construct

    /**
     * Create new event instance
     *
     * @return CalendarEvent
     */
    function newEvent() {
      $event = new CalendarEvent();
      $event->setParent($this->object);
      $event->setState(STATE_VISIBLE);

      return $event;
    } // newEvent

    /**
     * Add calendar event to the parent object
     *
     * There are two ways that this function can be called:
     *
     * First is by providing event instance as $p1. In that case, $p2 is treated as save indicator. Example:
     *
     * $calendar->calendarEvents->add(new CalendarEvent(), true);
     *
     * Second is by providing event name, event date (single value or range as array) and save indicator. Example:
     *
     * $calendar->calendarEvents->add("Dusan's Holiday", array('2012/05/05', '2012/05/15'), true);
     *
     * @param mixed $p1
     * @param mixed $p2
     * @param mixed $p3
     * @return CalendarEvent
     */
    function add($p1, $p2 = null, $p3 = null) {
      if($p1 instanceof CalendarEvent) {
        return $this->addEventInstance($p1, $p2);
      } else {
        return $this->addEventFromParams($p1, $p2, $p3);
      } // if
    } // add

    /**
     * Add event instance to the parent object
     *
     * @param CalendarEvent $event
     * @param boolean $save
     * @return CalendarEvent
     */
    private function addEventInstance(CalendarEvent $event, $save = false) {
      $event->setParent($this->object);

      if($save) {
        $event->save();
      } // if

      return $event;
    } // addEventInstance

    /**
     * Add event based on given parameters
     *
     * @param string $event_name
     * @param mixed $date_or_range
     * @param bool $save
     * @return CalendarEvent
     */
    private function addEventFromParams($event_name, $date_or_range, $save = false) {
      list($starts_on, $ends_on) = CalendarEvents::dateOrRangeToRange($date_or_range);

      $event = $this->newEvent();

      $event->setName($event_name);
      $event->setStartsOn($starts_on);
      $event->setEndsOn($ends_on);

      if($save) {
        $event->save();
      } // if

      return $event;
    } // addEventFromParams
    
    /**
     * Return true if parent object has events that $user can see
     *
     * @param IUser $user
     * @return boolean
     */
    function has(IUser $user) {
      return (boolean) $this->count($user);
    } // has
    
    /**
     * Return true if parent object has events that $user can see for a given date or date range
     *
     * @param mixed $date_or_range
     * @param IUser $user
     * @return boolean
     */
    function hasFor($date_or_range, IUser $user) {
      return (boolean) $this->countFor($date_or_range, $user);
    } // hasFor
    
    /**
     * Return number of events associated with parent object that $user can see
     *
     * @param IUser $user
     * @return integer
     */
    function count(IUser $user) {
      return CalendarEvents::countByParent($this->object, $user);
    } // count
    
    /**
     * Return number of events for a given date or date range, associated with parent object that $user can see
     *
     * @param $date_or_range
     * @param IUser $user
     * @return integer
     */
    function countFor($date_or_range, IUser $user) {
      return CalendarEvents::countFor($date_or_range, $user, array('parent_type = ? AND parent_id = ?', get_class($this->object), $this->object->getId()));
    } // countFor
    
  }