<?php

/**
 * select_repeat_option helper
 *
 * @package angie.frameworks.calendars
 * @subpackage helpers
 */

/**
 * Render select repeat option picker
 *
 * @param $params
 * @param $smarty
 * @return string
 */
function smarty_function_select_repeat_option($params, &$smarty) {
	$user = array_required_var($params, 'user', true, 'User');
	$name = array_var($params, 'name', null, true);
	$value = array_var($params, 'value', CalendarEvent::DONT_REPEAT, true);
	$repeat_event_option = array_var($params, 'repeat_event_option', CalendarEvent::REPEAT_OPTION_FOREVER, true);
	$repeat_until = array_var($params, 'repeat_until', DateValue::now(), true);
	$starts_on = array_var($params, 'starts_on', DateValue::now(), true);

	$options = array(
		CalendarEvent::DONT_REPEAT      => lang('No'),
		CalendarEvent::REPEAT_DAILY     => lang('Every Day'),
		CalendarEvent::REPEAT_WEEKLY    => lang('Every Week'),
		CalendarEvent::REPEAT_MONTHLY   => lang('Every Month'),
		CalendarEvent::REPEAT_YEARLY    => lang('Every Year'),
	);

	$possibilities = array();
	for ($i = 2; $i <= 30; $i++) {
		$possibilities[$i] = $i;
	} // for

	if ($value == CalendarEvent::REPEAT_DAILY) {
		$interval_param = 'D';
	} else if ($value == CalendarEvent::REPEAT_WEEKLY) {
		$interval_param = 'W';
	} else if ($value == CalendarEvent::REPEAT_MONTHLY) {
		$interval_param = 'M';
	} else if ($value == CalendarEvent::REPEAT_YEARLY) {
		$interval_param = 'Y';
	} else {
		$interval_param = null;
	} // if

	$selected = 0;

	if ($repeat_until instanceof DateValue && $interval_param) {
		$date = new DateTime($starts_on->toMySQL());
		while ($date->getTimestamp() <= $repeat_until->getTimestamp()) {
			$interval = new DateInterval('P1' . $interval_param);
			$date = $date->add($interval);
			$selected++;
		} // while
	} // if

	AngieApplication::useHelper('select_date', ENVIRONMENT_FRAMEWORK);
	$select_date_param = array(
		'name' => $name . '[repeat_until_value]['.CalendarEvent::REPEAT_OPTION_SELECT_DATE.']',
		'value' => $repeat_until instanceof DateValue ? $repeat_until->format('Y/m/d') : DateValue::now()->format('Y/m/d'),
		'skip_days_off' => false
	);

	if ($value == CalendarEvent::REPEAT_YEARLY) {
		$text = lang('years');
	} else if ($value == CalendarEvent::REPEAT_MONTHLY) {
		$text = lang('months');
	} else if ($value == CalendarEvent::REPEAT_WEEKLY) {
		$text = lang('weeks');
	} else {
		$text = lang('days');
	} // if

	$result = '';
	$result .= HTML::selectFromPossibilities($name . '[repeat_event]', $options, $value, $params);
	$result .= '<ul class="repeat_until" '. ($value == CalendarEvent::DONT_REPEAT ? 'style="display: none;"' : '') .'>';
	$result .=  '<li>' . HTML::radio($name . '[repeat_event_option]', $repeat_event_option == CalendarEvent::REPEAT_OPTION_FOREVER, array('label' => lang('Forever'), 'value' => CalendarEvent::REPEAT_OPTION_FOREVER)) . '</li>';
	$result .=  '<li>' . HTML::radio($name . '[repeat_event_option]', $repeat_event_option == CalendarEvent::REPEAT_OPTION_PERIODIC, array('label' => lang('For'), 'value' => CalendarEvent::REPEAT_OPTION_PERIODIC)) . HTML::selectFromPossibilities($name.'[repeat_until_value]['.CalendarEvent::REPEAT_OPTION_PERIODIC.']', $possibilities, $selected) . '<span class="repeat_until_period_text">' . $text . '</span></li>';
	$result .=  '<li>' . HTML::radio($name . '[repeat_event_option]', $repeat_event_option == CalendarEvent::REPEAT_OPTION_SELECT_DATE, array('label' => lang('Until'), 'value' => CalendarEvent::REPEAT_OPTION_SELECT_DATE)) . smarty_function_select_date($select_date_param, $smarty) . '</li>';
	$result .= '</ul>';

	return $result;
} // smarty_function_select_repeat_option