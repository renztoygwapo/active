<?php

  /**
   * select_datetime helper implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Select date and time widget implementation
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_datetime($params, &$smarty) {
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    $name = array_required_var($params, 'name', true);
    
    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('select_datetime');
    } // if
    
    $params['type'] = 'text';
    $params['autocomplete'] = 'off';
    
    $formatted_value = array_var($params, 'value', null, true);
    if($formatted_value instanceof DateValue) {
      $formatted_value = date('Y/m/d H:i', $formatted_value->getTimestamp());
    } // if
    
    $params['value'] = $formatted_value;
    
    $start_date = array_var($params, 'start_date', "2000/01/01", true);
    if($start_date instanceof DateValue) {
      $start_date = date('Y/m/d', $start_date->getTimestamp());
    } // if
    
    $end_date = array_var($params, 'end_date', '2050/01/01', true);
    if($end_date instanceof DateValue) {
      $end_date = date('Y/m/d', $end_date->getTimestamp());
    } // if
    
    $start_year = explode('/', $start_date);
    $start_year = $start_year[0];
    
    $end_year = explode('/', $end_date);
    $end_year = $end_year[0];
    
    if(isset($params['first_week_day'])) {
      $first_week_day = (integer) $params['first_week_day'];
    } elseif(Authentication::getLoggedUser() instanceof User) {
      $first_week_day = ConfigOptions::getValueFor('time_first_week_day', Authentication::getLoggedUser(), 0);
    } else {
      $first_week_day = ConfigOptions::getValue('time_first_week_day', 0);
    } // if
    
    $params['class'] = isset($params['class']) ? $params['class'] . ' input_text input_datetime' : 'input_text input_datetime';
    
    $hours_step = array_var($params, 'hours_step', null, true);
    $minutes_step = array_var($params, 'minutes_step', null, true);
    
    $default_hours = array_var($params, 'default_hours', 0);
    $default_minutes = array_var($params, 'default_minutes', 0);
    
    $result = '<div class="select_datetime">' . HTML::input($name, $formatted_value, $params);
    $datepicker_options = '{
			dateFormat : "yy/mm/dd",
			timeFormat: "hh:mm tt",
	    separator: " ",
			minDate : new Date("'.$start_date.'"),
			maxDate : new Date("'.$end_date.'"),
			showAnim : "blind",
			duration : 0,
			changeYear: true,
			showOn: "both",
			buttonImage: "'.AngieApplication::getImageUrl('icons/16x16/calendar.png', SYSTEM_MODULE).'",
			buttonImageOnly: true,
			firstDay: '.$first_week_day.',
			hideIfNoPrevNext : true,
			yearRange : "'.$start_year.':'.$end_year.'"';
        
    if($formatted_value) {
    	$datepicker_options .= ',defaultDate : new Date("'.$formatted_value.'")';
    } // if
    
    if($hours_step) {
    	$datepicker_options .= ',stepHour : ' . $hours_step;
    } // if
    
    if($minutes_step) {
    	$datepicker_options .= ',stepMinute : ' . $minutes_step;
    } // if
    
    if($default_hours) {
    	$datepicker_options .= ',hour : ' . $default_hours;
    } // if
    
    if($default_minutes) {
    	$datepicker_options .= ',minute : ' . $default_minutes;
    }
    
    $datepicker_options.='}';

    AngieApplication::useWidget('ui_date_picker', ENVIRONMENT_FRAMEWORK);
    AngieApplication::useWidget('ui_time_picker', ENVIRONMENT_FRAMEWORK);
    
    $result .= '<script type="text/javascript">$("#' . $params['id'] . '").datetimepicker('.$datepicker_options.');</script>';
    
    return $result . '</div>';
  } // smarty_function_select_datetime