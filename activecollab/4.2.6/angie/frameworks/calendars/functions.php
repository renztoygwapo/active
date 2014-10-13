<?php
	/**
	 * Calendar module functions
	 *
	 * @package angie.frameworks.calendars
	 */

	/**
	 * Render iCal data
	 *
	 * @param string $name iCalendar name
	 * @param array $objects
	 * @param boolean $include_calendar_name
	 * @return void
	 */
	function render_calendar_icalendar($name, $objects, $include_calendar_name = false) {
		require_once ANGIE_PATH . '/classes/icalendar/iCalCreator.class.php';

		$calendar = new vcalendar();
		$calendar->setProperty('X-WR-CALNAME', $name);
		$calendar->setProperty('METHOD', 'PUBLISH');

		$calendars = array();
		foreach($objects as $object) {
			$summary = $object->getName();
			if($include_calendar_name) {
				$calendar_id = $object->getParentId();
				if(isset($calendars[$calendar_id])) {
					$summary .= ' | ' . $calendars[$calendar_id]->getName();
				} else {
					$calendar = $object->getParent();
					if($calendar instanceof Calendar) {
						$calendars[$calendar_id] = $calendar;
						$summary .= ' | ' . $calendars[$calendar_id]->getName();
					} // if
				} // if
			} // if

			if (!($object instanceof CalendarEvent)) {
				continue;
			} // if

			$start_on = $object->getStartsOn();
			$due_on   = $object->getEndsOn();
			$time = $object->getStartsOnTime();

			if (!($start_on instanceof DateValue) || !($due_on instanceof DateValue)) {
				continue;
			} // if

			if ($time) {
				$start_on = DateTimeValue::makeFromString($start_on->toMySQL() . ' ' . $time);
				$due_on = DateTimeValue::makeFromString($due_on->toMySQL() . ' ' . $time);
			} // if

			$event = new vevent();

			// START_ON
			$start_on_year = $start_on->getYear();
			$start_on_month = $start_on->getMonth() < 10 ? '0' . $start_on->getMonth() : $start_on->getMonth();
			$start_on_day = $start_on->getDay() < 10 ? '0' . $start_on->getDay() : $start_on->getDay();
			if ($start_on instanceof DateTimeValue) {
				$date_args = array(
					'year' => $start_on_year,
					'month' => $start_on_month,
					'day' => $start_on_day,
					'hour' => $start_on->getHour(),
					'min' => $start_on->getMinute(),
					'sec' => $start_on->getSecond()
				);
				$event->setProperty('dtstart', $date_args);
			} else {
				$event->setProperty('dtstart', array($start_on_year, $start_on_month, $start_on_day), array('VALUE'=>'DATE'));
			} // if

			// DUE_ON
			if (!($due_on instanceof DateTimeValue)) {
				$due_on->advance(24 * 60 * 60, true); // One day shift because iCal and Windows Calendar don't include last day
			} // if
			$due_on_year = $due_on->getYear();
			$due_on_month = $due_on->getMonth() < 10 ? '0' . $due_on->getMonth() : $due_on->getMonth();
			$due_on_day = $due_on->getDay() < 10 ? '0' . $due_on->getDay() : $due_on->getDay();
			if ($due_on instanceof DateTimeValue) {
				$date_args = array(
					'year' => $due_on_year,
					'month' => $due_on_month,
					'day' => $due_on_day,
					'hour' => $due_on->getHour(),
					'min' => $due_on->getMinute(),
					'sec' => $due_on->getSecond()
				);
				$event->setProperty('dtend', $date_args);
			} else {
				$event->setProperty('dtend', array($due_on_year, $due_on_month, $due_on_day), array('VALUE'=>'DATE'));
			} // if

			// repeating event
			if ($object->isRepeating()) {
				$freq = strtoupper($object->getRepeatEvent());
				$until = $object->getRepeatUntil();
				$until->advance(24 * 60 * 60, true);
				$event->setProperty('rrule', array( "FREQ" => $freq, "until" => $until->format('Ymd')));
			} // if

			$event->setProperty('dtstamp', date('Ymd'));
			$event->setProperty('summary', $summary);

			$calendar->addComponent($event);
		} // foreach

		$cal = $calendar->createCalendar();

		header('Content-Type: text/calendar; charset=UTF-8');
		header('Content-Disposition: attachment; filename="' . $name .'.ics"');
		header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Pragma: no-cache');

		print $cal;
		die();
	} // render_calendar_icalendar