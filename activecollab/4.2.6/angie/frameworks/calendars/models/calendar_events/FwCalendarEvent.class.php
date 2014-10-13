<?php

  /**
   * Framework level calendar event implementation
   *
   * @package angie.frameworks.calendars
   * @subpackage models
   */
  abstract class FwCalendarEvent extends BaseCalendarEvent implements IRoutingContext, IState, IActivityLogs, IObjectContext, IHistory, IAccessLog {
    
    /**
     * Repeat Values
     */
    const DONT_REPEAT = 'dont';
    const REPEAT_DAILY = 'daily';
    const REPEAT_WEEKLY = 'weekly';
    const REPEAT_MONTHLY = 'monthly';
    const REPEAT_YEARLY = 'yearly';

	  /**
	   * Repeat Options
	   */
	  const REPEAT_OPTION_DEFAULT = 'default';
	  const REPEAT_OPTION_FOREVER = 'forever';
	  const REPEAT_OPTION_PERIODIC = 'periodic';
	  const REPEAT_OPTION_SELECT_DATE = 'date';

	  /**
	   * Can user manage event
	   *
	   * @param User $user
	   * @return bool
	   */
	  function canManage(User $user) {
		  if ($this->isCreator($user)) {
			  return true;
		  } // if

		  $calendar = $this->getParent();

		  if ($calendar instanceof Calendar) {
			  if ($user->getId() == $calendar->getCreatedById()) {
				  return true;
			  } // if

			  if ($user->isAdministrator() && $calendar->canView($user)) {
				  return true;
			  } // if
		  } // if

		  return false;
	  } // canManage

	  /**
	   * Can user view event
	   *
	   * @param User $user
	   * @return bool
	   */
	  function canView(User $user) {
		  return $this->canManage($user);
	  } // canView

	  /**
	   * Can user edit event
	   * @param User $user
	   * @return bool
	   */
	  function canEdit(User $user) {
		  return $this->canManage($user);
	  } // canEdit

	  /**
	   * Can user delete event
	   *
	   * @param User $user
	   * @return bool
	   */
	  function canDelete(User $user) {
		  return $this->canManage($user);
	  } // canRemove

	  /**
	   * Check is user creator of this event
	   *
	   * @param User $user
	   * @return bool
	   */
	  function isCreator(User $user) {
		  return $user->getId() == $this->getCreatedById();
	  } // isCreator

    /**
     * Returns true if this event is not a single day event, but spans across multiple days
     *
     * @return bool
     */
    function isSpan() {
      return $this->getEndsOn()->getTimestamp() > $this->getStartsOn()->getTimestamp();
    } // isSpan

    /**
     * Returns true if this event is repeating
     *
     * @return bool
     */
    function isRepeating() {
      return $this->getRepeatEvent() !== self::DONT_REPEAT;
    } // isRepeating

	  /**
	   * Set repeat until
	   *
	   * @param DateValue $repeat
	   * @param $option
	   * @param $option_values
	   * @return DateValue|mixed
	   */
	  function setRepeatUntil($repeat, $option, $option_values) {
		  $start_on = $this->getStartsOn();
		  if ($option == CalendarEvent::REPEAT_OPTION_PERIODIC) {
			  $pre_value = array_var($option_values, CalendarEvent::REPEAT_OPTION_PERIODIC) - 1;
			  switch($repeat) {
				  case self::REPEAT_YEARLY:
					  $interval_period = 'Y';
					  break;
				  case self::REPEAT_MONTHLY:
					  $interval_period = 'M';
					  break;
				  case self::REPEAT_WEEKLY:
					  $interval_period = 'W';
					  break;
				  case self::REPEAT_DAILY:
					  $interval_period = 'D';
					  break;
				  default:
					  $interval_period = null;
					  break;
			  } // switch

			  if ($interval_period) {
				  $interval = new DateInterval('P' . $pre_value . $interval_period);
				  $date = new DateTime($start_on->toMySQL());
				  $value = DateValue::makeFromTimestamp($date->add($interval)->getTimestamp());
			  } else {
				  $value = null;
			  } // if
		  } else if ($option == CalendarEvent::REPEAT_OPTION_SELECT_DATE) {
			  $pre_value = array_var($option_values, CalendarEvent::REPEAT_OPTION_SELECT_DATE);
			  $value = DateValue::makeFromString($pre_value);
		  } else if ($option == CalendarEvent::REPEAT_OPTION_FOREVER) {
			  $value = null;
		  } else {
			  $value = $this->getRepeatUntil();
		  } // if

		  return $this->setFieldValue('repeat_until', $value);
	  } // setRepeatUntil

	  // ---------------------------------------------------
	  //  Context
	  // ---------------------------------------------------

	  /**
	   * Return object domain
	   *
	   * @return string
	   */
	  function getObjectContextDomain() {
		  return 'calendars';
	  } // getContextDomain

	  /**
	   * Return object path
	   *
	   * @return string
	   */
	  function getObjectContextPath() {
		  return 'calendars/' . $this->getParentId() . '/events/' . $this->getId();
	  } // getContextPath

    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------

    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'calendar_event';
    } // getRoutingContext

    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array(
        'calendar_id' => $this->getParentId(),
        'calendar_event_id' => $this->getId(),
      );
    } // getRoutingContextParams

		/**
		 * Cached state helper instance
		 *
		 * @var ICalendarEventStateImplementation
		 */
		private $state = false;

		/**
		 * Return state helper instance
		 *
		 * @return ICalendarEventStateImplementation
		 */
		function state() {
			if($this->state === false) {
				$this->state = new ICalendarEventStateImplementation($this);
			} // if

			return $this->state;
		} // state

	  /**
	   * Cached access log helper instance
	   *
	   * @var IProjectObjectActivityLogsImplementation
	   */
	  private $activity_logs = false;

	  /**
	   * Return activity logs helper instance
	   *
	   * @return ICalendarEventActivityLogsImplementation
	   */
	  function activityLogs() {
		  if($this->activity_logs === false) {
			  $this->activity_logs = new ICalendarEventActivityLogsImplementation($this);
		  } // if

		  return $this->activity_logs;
	  } // activityLogs

	  /**
	   * Cached access log helper instance
	   *
	   * @var IAccessLogImplementation
	   */
	  private $access_log = false;

	  /**
	   * Return access log helper instance
	   *
	   * @return IAccessLogImplementation
	   */
	  function accessLog() {
		  if($this->access_log === false) {
			  $this->access_log = new IAccessLogImplementation($this);
		  } // if

		  return $this->access_log;
	  } // accessLog

	  /**
	   * Cached history helper
	   *
	   * @var IHistoryImplementation
	   */
	  private $history = false;

	  /**
	   * Return history helper instance
	   *
	   * @return IHistoryImplementation
	   */
	  function history() {
		  if($this->history === false) {
			  $this->history = new IHistoryImplementation($this, array('name', 'parent_id', 'starts_on', 'ends_on', 'starts_on_time', 'repeat_event'));
		  } // if

		  return $this->history;
	  } // history

	  /**
	   * Describe object
	   *
	   * @param IUser $user
	   * @param bool $detailed
	   * @param bool $for_interface
	   * @return array
	   */
	  function describe(IUser $user, $detailed = false, $for_interface = false) {
		  $result = parent::describe($user, $detailed, $for_interface);

		  $result['id'] = $this->getId();
		  $result['type'] = $this->getType();
		  $result['parent_id'] = $this->getParentId();
		  $result['parent_type'] = $this->getParentType();
		  $result['name'] = $this->getName();
		  $result['starts_on'] = $this->getStartsOn();
		  $result['ends_on'] = $this->getEndsOn();

		  $result['repeat'] = $this->getRepeatEvent();
			$result['repeat_until'] = $this->getRepeatUntil();

		  $starts_on_time = $this->getStartsOnTime();

		  if ($starts_on_time) {
			  $starts_on_time = DateTimeValue::makeFromString($starts_on_time)->format('H:i:s');
			  $result['starts_on_time'] = DateTimeValue::makeFromString($starts_on_time)->formatTimeForUser($user, 0);
		  } else {
			  $result['starts_on_time'] = null;
		  } // if

		  $result['permissions'] = array(
			  'can_edit'        => $this->canEdit($user),
			  'can_trash'       => $this->state()->canTrash($user),
			  'can_reschedule'  => $this->canEdit($user)
		  );

		  $result['urls'] = array(
			  'edit'        => $this->getEditUrl(),
			  'trash'       => $this->state()->getTrashUrl(),
			  'reschedule'  => $this->getEditUrl()
		  );

		  return $result;
	  } // describe

	  // ---------------------------------------------------
	  //  Options
	  // ---------------------------------------------------

	  /**
	   * Prepare list of options that $user can use
	   *
	   * @param IUser $user
	   * @param NamedList $options
	   * @param string $interface
	   * @return NamedList
	   */
	  protected function prepareOptionsFor(IUser $user, NamedList $options, $interface = AngieApplication::INTERFACE_DEFAULT) {
		  parent::prepareOptionsFor($user, $options, $interface);
	  } // prepareOptionsFor

    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------

    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if(!$this->validatePresenceOf('name')) {
        $errors->addError(lang('Event name is required'), 'name');
      } // if

      if(!$this->validatePresenceOf('starts_on')) {
        $errors->addError(lang('Event start date is required'), 'starts_on');
      } // if

      if(!$this->validatePresenceOf('ends_on')) {
        $errors->addError(lang('Event end date is required'), 'ends_on');
      } // if

	    parent::validate($errors, true);
    } // validate
    
  }