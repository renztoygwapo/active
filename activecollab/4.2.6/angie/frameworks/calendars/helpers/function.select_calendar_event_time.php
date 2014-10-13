<?php

/**
 * calendar_event_time helper
 *
 * @package angie.frameworks.calendars
 * @subpackage helpers
 */

/**
 * Render calendar event time select
 *
 * @param $params
 * @param $smarty
 * @return string
 */
function smarty_function_select_calendar_event_time($params, &$smarty) {
	$user = array_required_var($params, 'user', true, 'User');
	$name = array_var($params, 'name', 'calendar_event[starts_on_time]');
	$value = array_var($params, 'value', null, true);

	if ($value instanceof DateTimeValue) {
		$time = $value->formatTimeForUser($user, 0);
		$all_day_event = false;
	} else {
		$time = DateTimeValue::now()->formatTimeForUser($user);
		$all_day_event = true;
	} // if

	// split time meridiem from time string
	list($time_value, $meridiem_value) = explode(" ", $time);

	// check user time format
	$meridiem = $user->getTimeFormat() == FORMAT_TIME ? true : false;

	// initialize options
	$options = array();

	// hour and minute step
	$minute_step = 5;

	// prepare defaults
	$h = $meridiem ? 1 : 0;
	$hours = $meridiem ? 12 : 23;
	$minutes = 60 - $minute_step;

	// populate options for select
	for ($h; $h <= $hours; $h++) {
		for ($m = 0; $m <= $minutes; $m += $minute_step) {
			$option_value = str_pad($h, 2, '0', STR_PAD_LEFT) . ":" . str_pad($m, 2, '0', STR_PAD_LEFT);
			$options[] = HTML::optionForSelect($option_value, $option_value, $time_value == $option_value);
		} // for
	} // for

	$result = HTML::openTag('div', array('class' => 'select_time'));
	$result .= HTML::select($name . '[value]', $options, $params);
	$label = array_var($params, 'label', null, true);

	if ($meridiem) {
		$options_for_meridiem = array(
			HTML::optionForSelect('AM', 'AM', $meridiem_value == 'AM'),
			HTML::optionForSelect('PM', 'PM', $meridiem_value == 'PM')
		);
		$result .= HTML::select($name . '[meridiem]', $options_for_meridiem, $params);
	} // if

	$result .= HTML::checkbox($name . '[all_day_event]', $all_day_event, array('label' => lang('All Day Event'), 'class' => 'all_day_event'));
	return $result . '</div>';

} // smarty_function_select_calendar_event_time