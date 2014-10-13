<?php

  /**
   * Framework level calendar events managear implementation
   *
   * @package angie.frameworks.calendars
   * @subpackage models
   */
  abstract class FwCalendarEvents extends BaseCalendarEvents {

	  /**
	   * Returns true if $user can create a new events
	   *
	   * @param User $user
	   * @param Calendar $calendar
	   * @return bool
	   */
	  static function canAdd(User $user, Calendar $calendar) {
		  return $user->getId() == $calendar->getCreatedById() || ($calendar->canView($user) && ($calendar->getShareCanAddEvents() || $user->isAdministrator()));
	  } // canAdd

	  /**
	   * Return if user can create new events
	   * before choose to which calendar want to add it
	   *
	   * @param User $user
	   * @return bool
	   */
	  static function canAddGlobal(User $user) {
		  return $user instanceof User;
	  } // canAddBeforeChooseCalendar

	  /**
	   * Returns true if $user can manage events
	   *
	   * @param User $user
	   * @return boolean
	   */
	  static function canManage(User $user) {
		  return $user instanceof User;
	  } // canManage

	  // ---------------------------------------------------
	  //  Finders
	  // ---------------------------------------------------

	  /**
	   * Get trashed map
	   *
	   * @param User $user
	   * @return array
	   */
	  static function getTrashedMap($user) {
		  return array(
			  'calendar_event' => DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'calendar_events WHERE state = ? ORDER BY created_on DESC', STATE_TRASHED)
		  );
	  } // getTrashedMap

	  /**
	   * Find trashed calendar events
	   *
	   * @param User $user
	   * @param array $map
	   * @return array
	   */
	  static function findTrashed(User $user, &$map) {
		  $skip_calendar_ids = array_var($map, 'calendar');

		  if (is_foreachable($skip_calendar_ids)) {
			  $trashed_calendar_events = DB::execute('SELECT id, parent_id, name FROM ' . TABLE_PREFIX . 'calendar_events WHERE state = ? AND parent_id NOT IN (?) ORDER BY created_on DESC', STATE_TRASHED, $skip_calendar_ids);
		  } else {
			  $trashed_calendar_events = DB::execute('SELECT id, parent_id, name FROM ' . TABLE_PREFIX . 'calendar_events WHERE state = ? ORDER BY created_on DESC', STATE_TRASHED);
		  } // if

		  if (!is_foreachable($trashed_calendar_events)) {
			  return null;
		  } // if

		  $view_url = Router::assemble('calendar_event', array('calendar_id' => '--CALENDAR-ID--', 'calendar_event_id' => '--CALENDAR-EVENT-ID--'));

		  $items = array();
		  foreach ($trashed_calendar_events as $calendar_event) {
			  $items[] = array(
				  'id'            => $calendar_event['id'],
				  'name'          => $calendar_event['name'],
				  'type'          => 'CalendarEvent',
				  'permalink'     => str_replace('--CALENDAR-ID--', $calendar_event['parent_id'], str_replace('--CALENDAR-EVENT-ID--', $calendar_event['id'], $view_url)),
				  'can_be_parent' => false,
			  );
		  } // foreach

		  return $items;
	  } // findTrashed

	  /**
	   * Delete trashed calendar events
	   */
	  static function deleteTrashed() {
		  $calendar_events = CalendarEvents::find(array(
			  'conditions' => array('state = ?', STATE_TRASHED)
		  ));

		  if (is_foreachable($calendar_events)) {
			  foreach ($calendar_events as $calendar_event) {
				  $calendar_event->state()->delete();
			  } // foreach
		  } // if

		  return true;
	  } // deleteTrashed

	  /**
	   * Find calendars events for list
	   *
	   * @param User $user
	   * @param DateValue $from
	   * @param DateValue $to
	   * @return array
	   */
	  static function findForList(User $user, DateValue $from, DateValue $to=null) {
		  $result = array();

		  $calendars = Calendars::getCalendarsByUser($user, STATE_VISIBLE);

		  if (!$calendars) {
			  return $result;
		  } // if

		  $calendars = $calendars->toArrayIndexedBy('id');
		  $calendar_ids = array_keys($calendars);

		  $table_calendar_events = TABLE_PREFIX . "calendar_events";
			$events = DB::execute("SELECT * FROM $table_calendar_events WHERE state >= ? AND parent_id IN (?) AND (((starts_on BETWEEN ? AND ?) OR (ends_on BETWEEN ? AND ?) OR (starts_on < ? AND ends_on > ?)) OR (repeat_event_option = ? OR (repeat_event_option = ? AND repeat_until >= ?)))", STATE_VISIBLE, $calendar_ids, $from, $to, $from, $to, $from, $to, 'forever', 'periodic', $from);

		  if (is_foreachable($events)) {
			  $events->setCasting(array(
				  'starts_on'       => DBResult::CAST_DATE,
				  'ends_on'         => DBResult::CAST_DATE,
				  'repeat_until'    => DBResult::CAST_DATE
			  ));

			  $event_id_prefix_pattern = '--EVENT-ID--';
			  $calendar_id_prefix_pattern = '--CALENDAR-ID--';
			  $event_url_parameters = array('calendar_id' => $calendar_id_prefix_pattern, 'calendar_event_id' => $event_id_prefix_pattern);
			  $edit_event_url_pattern = Router::assemble('calendar_event_edit', $event_url_parameters);
			  $trash_event_url_pattern = Router::assemble('calendar_event_trash', $event_url_parameters);

			  foreach($events as $subobject) {
				  $id = $subobject['id'];
				  $parent_id = $subobject['parent_id'];

				  // is calendar owner
				  $calendar_owner_id = isset($calendars[$parent_id]['created_by_id']) ? $calendars[$parent_id]['created_by_id'] : null;
				  $is_calendar_owner = $calendar_owner_id == $user->getId();

				  // is calendar event creator
				  $calendar_event_created_by_id = (integer) $subobject['created_by_id'];
				  $is_calendar_event_creator = $calendar_event_created_by_id == $user->getId();

				  $starts_on_time = $subobject['starts_on_time'];
				  if ($starts_on_time) {
					  $starts_on_time = DateTimeValue::makeFromString($starts_on_time)->formatTimeForUser($user, 0);
				  } // if

				  $result[] = array(
					  'id'              => $id,
					  'type'            => $subobject['type'],
					  'parent_id'       => $parent_id,
					  'parent_type'     => $subobject['parent_type'],
					  'name'            => $subobject['name'],
					  'starts_on'       => $subobject['starts_on'],
					  'ends_on'         => $subobject['ends_on'],
					  'repeat'          => isset($subobject['repeat_event']) ? $subobject['repeat_event'] :'dont',
					  'repeat_until'    => $subobject['repeat_until'],
					  'starts_on_time'  => $starts_on_time,
					  'permissions'     => array(
						  'can_edit'        => $is_calendar_owner || $is_calendar_event_creator || $user->isAdministrator(),
						  'can_trash'       => $is_calendar_owner || $is_calendar_event_creator || $user->isAdministrator(),
						  'can_reschedule'  => $is_calendar_owner || $is_calendar_event_creator || $user->isAdministrator()
					  ),
					  'urls'          => array(
						  'edit'          => str_replace($event_id_prefix_pattern, $id, str_replace($calendar_id_prefix_pattern, $parent_id, $edit_event_url_pattern)),
						  'trash'         => str_replace($event_id_prefix_pattern, $id, str_replace($calendar_id_prefix_pattern, $parent_id, $trash_event_url_pattern)),
						  'reschedule'    => str_replace($event_id_prefix_pattern, $id, str_replace($calendar_id_prefix_pattern, $parent_id, $edit_event_url_pattern)),
					  )
				  );
			  } // foreach
		  } // if

		  return $result;
	  } // findForList

	  /**
	   * Find calendar events
	   *
	   * @param Calendar $calendar
	   * @param int $min_state
	   * @return DBResult[]
	   */
	  static function findByCalendar(Calendar $calendar, $min_state = STATE_VISIBLE) {
		  return CalendarEvents::find(array(
			  "conditions" => array('parent_id = ? AND state >= ?', $calendar->getId(), $min_state),
			  "order" => "position DESC"
		  ));
	  } // findByCalendar

    /**
     * Take $date_or_range input and return array with start and end dates (can be the same date)
     *
     * This function accepts:
     *
     * 1. DateValue instance
     * 2. Array of two elements, where each element is either string, integer or DateValue instance
     * 3. String representation of a date or timestamp
     *
     * @param $date_or_range
     * @return array
     * @throws InvalidParamError
     */
    static function dateOrRangeToRange($date_or_range) {
      if($date_or_range instanceof DateValue) {
        $from_date = $date_or_range;
      } elseif(is_array($date_or_range) && count($date_or_range) == 2) {
        list($from_date, $to_date) = $date_or_range;

        if(!($from_date instanceof DateValue)) {
          if($from_date && (is_string($from_date) || is_integer($from_date))) {
            $from_date = new DateValue($from_date);
          } else {
            throw new InvalidParamError('date_or_range', $date_or_range, 'First element of range needs to be an instance or DateValue, time stamp or string representation of a date');
          } // if
        } // if

        if(!($to_date instanceof DateValue)) {
          if($to_date && (is_string($to_date) || is_integer($to_date))) {
            $to_date = new DateValue($to_date);
          } else {
            throw new InvalidParamError('date_or_range', $date_or_range, 'Second element of range needs to be an instance or DateValue, time stamp or string representation of a date');
          } // if
        } // if

      } elseif($date_or_range && (is_string($date_or_range) || is_integer($date_or_range))) {
        $from_date = new DateValue($date_or_range);
      } else {
        throw new InvalidParamError('date_or_range', $date_or_range, '$date_or_range is expected to be an instance of DateValue class, array with start and end dates, timestamp or a strict representation of a date');
      } // if

      if(!isset($to_date)) {
        $to_date = $from_date;
      } // if

      return array($from_date, $to_date);
    } // dateOrRangeToRange

    /**
     * Prepare conditions based on date or range
     *
     * @param mixed $date_or_range
     * @return string
     */
    static function prepareConditionsBasedOnDateOrRange($date_or_range) {
      list($range_start, $range_end) = CalendarEvents::dateOrRangeToRange($date_or_range);

      if($range_start->isSameDay($range_end)) {
        return CalendarEvents::prepareConditionsForDay($range_start);
      } else {
        return CalendarEvents::prepareConditionsForRange($range_start, $range_end);
      } // if
    } // prepareConditionsBasedOnDateOrRange

    /**
     * Prepare conditions for a given day
     *
     * @param DateValue $for
     * @return string
     */
    static function prepareConditionsForDay(DateValue $for) {
      $calendar_events_table = TABLE_PREFIX . 'calendar_events';
      $escaped_day = DB::escape($for);

      // Exact events defined for a given day
      $exact_match = "$calendar_events_table.starts_on <= $escaped_day AND $calendar_events_table.ends_on >= $escaped_day";

      $day = $for->getDay();
      $month = $for->getMonth();
      $week_day = $for->getWeekday() + 1; // Add 1 to meet MySQL's DAYOFWEEK() result (ODBC complient)

      $recurring_match =
        "($calendar_events_table.repeat_event = 'daily') OR " . // All daily events
          "($calendar_events_table.repeat_event = 'weekly' AND DAYOFWEEK($calendar_events_table.starts_on) = '$week_day') OR " . // All weekly events that fall on a given day
          "($calendar_events_table.repeat_event = 'monthly' AND DAY($calendar_events_table.starts_on) = '$day') OR " . // All monthly events that fall on a given month day
          "($calendar_events_table.repeat_event = 'yearly' AND DAY($calendar_events_table.starts_on) = '$day' AND MONTH($calendar_events_table.starts_on) = '$month')"; // All yearly events that fall on a given day of a given month

      $ignore_past_repeting_events = "($calendar_events_table.repeat_event != 'dont' AND $calendar_events_table.ends_on < $escaped_day)";

      return "($exact_match OR ($ignore_past_repeting_events AND ($recurring_match)))";
    } // prepareConditionsForDay

    /**
     * Prepare conditions for a given date range
     *
     * @param DateValue $from
     * @param DateValue $to
     * @return string
     */
    static function prepareConditionsForRange(DateValue $from, DateValue $to) {
      $calendar_events_table = TABLE_PREFIX . 'calendar_events';

      $escaped_from_day = DB::escape($from);
      $escaped_to_day = DB::escape($to);

      // Find all exact event definitions
      $exact_match = "((($calendar_events_table.starts_on >= $escaped_from_day AND $calendar_events_table.starts_on <= $escaped_to_day)) OR (($calendar_events_table.ends_on >= $escaped_from_day AND $calendar_events_table.ends_on <= $escaped_to_day)))"; // All events that either start or end in the given range

      // Ignore repeating events that start after the range
      $ignore_past_repeting_events = DB::prepare("($calendar_events_table.repeat_event != 'dont' AND $calendar_events_table.starts_on < ?)", $to);

      if(CalendarEvents::matchWholeYear($from, $to)) {
        return "($exact_match OR ($ignore_past_repeting_events AND $calendar_events_table.repeat_event != 'dont'))"; // Exact match or any repeating event
      } else {
        $repeat_event_conditions = array(
          "($calendar_events_table.repeat_event = 'daily')" // Any daily event matches the range filter
        );

        // Prepare weekdays match
        $weekdays = CalendarEvents::matchWeekdays($from, $to);

        if($weekdays == 'any') {
          $repeat_event_conditions[] = "($calendar_events_table.repeat_event = 'weekly')";
        } else {
          $repeat_event_conditions[] = DB::prepare("($calendar_events_table.repeat_event = 'weekly' AND WEEKDAY($calendar_events_table.starts_on) IN (?))", array(CalendarEvents::phpWeekdaysToMySQLWeekdays($weekdays)));
        } // if

        foreach(CalendarEvents::matchYearMonthAndDay($from, $to) as $year => $months) {
          $full_months = array();
          $all_days = array();

          foreach($months as $month => $days) {
            if($days === 'any') {
              $full_months[] = $month;
            } else {
              $repeat_event_conditions[] = DB::prepare("($calendar_events_table.repeat_event = 'yearly' AND MONTH($calendar_events_table.starts_on) = '$month' AND DAY($calendar_events_table.starts_on) IN (?))", $days);

              $all_days = array_merge($all_days, $days);
            } // if
          } // foreach

          if($full_months) {
            $repeat_event_conditions[] = "($calendar_events_table.repeat_event = 'monthly')";
            $repeat_event_conditions[] = DB::prepare("($calendar_events_table.repeat_event = 'yearly' AND MONTH($calendar_events_table.starts_on) IN (?))", $full_months);
          } else {
            array_unique($all_days);
            sort($all_days);

            $all_days_count = count($all_days);

            if($all_days_count == 31) {
              $repeat_event_conditions[] = "($calendar_events_table.repeat_event = 'monthly')";
            } elseif($all_days_count) {
              $repeat_event_conditions[] = DB::prepare("($calendar_events_table.repeat_event = 'monthly' AND DAY($calendar_events_table.starts_on) IN (?))", $all_days);
            } // if
          } // if
        } // foreach

        return "($exact_match OR ($ignore_past_repeting_events AND (" . implode(' OR ', $repeat_event_conditions) . ")))";
      } // if
    } // prepareConditionsForRange

    /**
     * Returns true if the two dates are year or more apart
     *
     * @param DateValue $from
     * @param DateValue $to
     * @return bool
     */
    static function matchWholeYear(DateValue $from, DateValue $to) {
      if($from->getTimestamp() >= $to->getTimestamp()) {
        return false; // Invalid input (from larger or equal than to)
      } // if

      if($from->getYear() == $to->getYear()) {
        return $from->getDay() === 1 && $from->getMonth() === 1 && // January 1st
               $to->getDay() === 31 && $to->getMonth() === 12; // December 31st
      } else {
        return $from->getYearday() <= (DateValue::make($to->getMonth(), $to->getDay(), $from->getYear())->getYearday() + 1); // Add one so we fetch situations like 2010/05/12 - 2011/05/11 (we have both 11 and 12 and that makes a whole year)
      } // if
    } // matchWholeYear

    /**
     * Return array of weekdays that are affected with this date range
     *
     * @param DateValue $from
     * @param DateValue $to
     * @return array
     */
    static function matchWeekdays(DateValue $from, DateValue $to) {
      if($from->getTimestamp() < $to->getTimestamp()) {
        if($to->daysBetween($from) >= 6) {
          return 'any';
        } else {
          $from_clone = clone $from;

          $result = array($from_clone->getWeekday());

          while(!$from_clone->isSameDay($to)) {
            $from_clone->advance(86400);
            $result[] = $from_clone->getWeekday();
          } // white

          sort($result);

          return $result;
        } // if
      } else {
        return array($from->getWeekday());
      } // if
    } // matchWeekdays

    /**
     * Convert PHP weekdays to MySQL weekdays (Monday 0, Sunday 6)
     *
     * @param $weekdays
     * @return array
     */
    static protected function phpWeekdaysToMySQLWeekdays($weekdays) {
      foreach($weekdays as $k => $v) {
        if($v == 0) {
          $weekdays[$k] = 6;
        } else {
          $weekdays[$k] = $v - 1;
        } // if
      } // foreach

      sort($weekdays);

      return $weekdays;
    } // phpWeekdaysToMySQLWeekdays

    /**
     * Return array of matching events for given range
     *
     * This is extracted in a separate function so it can be tested, before any complex queries are build based on the
     * data returned by this code
     *
     * @param DateValue $from
     * @param DateValue $to
     * @return array
     */
    static function matchYearMonthAndDay(DateValue $from, DateValue $to) {
      $result = array();

      $from_day = $from->getDay();
      $from_month = $from->getMonth();
      $from_year = $from->getYear();

      $to_day = $to->getDay();
      $to_month = $to->getMonth();
      $to_year = $to->getYear();

      // Same year
      if($from_year == $to_year) {
        $result[$from_year] = array();

        // Same month
        if($from_month == $to_month) {
          if($from_day == 1 && $to_day == get_last_month_day($to_month, $to->isLeapYear())) {
            $result[$from_year][$from_month] = 'any';
          } else {
            $result[$from_year][$from_month] = array();

            for($i = $from_day; $i <= $to_day; $i++) {
              $result[$from_year][$from_month][] = $i;
            } // for
          } // if

        // Different month
        } else {

          // First from month calculation
          if($from_day == 1) {
            $result[$from_year][$from_month] = 'any';
          } else {
            $result[$from_year][$from_month] = array();

            $last_day = get_last_month_day($from_month, $from->isLeapYear());

            for($i = $from_day; $i <= $last_day; $i++) {
              $result[$from_year][$from_month][] = $i;
            } // for
          } // if

          // Other months, until the $to_month
          for($i = $from_month + 1; $i < $to_month; $i++) {
            $result[$from_year][$i] = 'any';
          } // for

          // Dates in tp month

          $last_day = get_last_month_day($to_month, $to->isLeapYear());
          if($to_day == $last_day) {
            $result[$from_year][$to_month] = 'any';
          } else {
            for($i = 1; $i <= $to_day; $i++) {
              $result[$from_year][$to_month][] = $i;
            } // for
          } // if

        } // if

      // Differnt year
      } else {

        // From day and month
        if($from_day == 1) {
          $result[$from_year][$from_month] = 'any';
        } else {
          $result[$from_year][$from_month] = array();

          $last_day = get_last_month_day($from_month, $from->isLeapYear());

          for($i = $from_day; $i <= $last_day; $i++) {
            $result[$from_year][$from_month][] = $i;
          } // for
        } // if

        if($from_month < 12) {
          for($i = $from_month + 1; $i <= 12; $i++) {
            $result[$from_year][$i] = 'any';
          } // for
        } // if

        // Years in between
        for($i = $from_year + 1; $i < $to_year; $i++) {
          $result[$i] = 'any';
        } // for

        if($to_month > 1) {
          for($i = 1; $i < $to_month; $i++) {
            $result[$to_year][$i] = 'any';
          } // for
        } // if

        if($to_day == get_last_month_day($to_month, $to->isLeapYear())) {
          $result[$to_year][$to_month] = 'any';
        } else {
          $result[$to_year][$to_month] = array();

          for($i = 1; $i <= $to_day; $i++) {
            $result[$to_year][$to_month][] = $i;
          } // for
        } // if
      } // if

      return $result;
    } // matchYearMonthAndDay

    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------

    /**
     * Return number of events in a given date or range
     *
     * @param mixed $date_or_range
     * @param IUser $user
     * @param mixed $additional_conditions
     * @return DBResult
     */
    static function countFor($date_or_range, IUser $user, $additional_conditions = null) {
      $conditions = CalendarEvents::prepareConditionsBasedOnDateOrRange($date_or_range);

      if($additional_conditions) {
        $conditions = "($conditions AND (" . DB::prepareConditions($additional_conditions) . "))";
      } // if

      return DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . "calendar_events WHERE $conditions");
    } // countFor

    /**
     * Return number of events by parent
     *
     * @param ICalendarEvents $parent
     * @param IUser $user
     * @return integer
     */
    static function countByParent(ICalendarEvents $parent, IUser $user) {
      $min_state = $parent instanceof IState ? $parent->getState() : STATE_VISIBLE;

      return CalendarEvents::count(array('parent_type = ? AND parent_id = ? AND state >= ?', get_class($parent), $parent->getId(), $min_state));
    } // countByParent

  }