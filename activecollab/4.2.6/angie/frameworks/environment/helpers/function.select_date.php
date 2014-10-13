<?php

  /**
   * Render date picker
   *
   * Parameters:
   * 
   * - id - Control ID
   * - value - datetime value that is select, NULL means today
   * - start_date - datetime value of start date, NULL means no start date
   * - end_date - datetime value of last selectable day, NULL means no end date
   * - first_week_day - 7 for Sunday, 1 for Monday
   * - show_timezone - whether to show or hide timezone information
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_date($params, &$smarty) {
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    
    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('select_date');
    } // if
    
    $params['autocomplete'] = 'off';
    
    $formatted_value = array_var($params, 'value', null, true);
    if($formatted_value instanceof DateValue) {
      $formatted_value = date('Y/m/d', $formatted_value->getTimestamp());
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
    
    $params['class'] = isset($params['class']) ? $params['class'] . ' input_text input_date' : 'input_text input_date';
    $params['type'] = 'text';

    
    // Web interface
    if($interface == AngieApplication::INTERFACE_DEFAULT) {
      $result = '<div class="select_date">' . HTML::input($params['name'], $formatted_value, $params);
      
      // Prepare options 
      $datepicker_options = '{
  			dateFormat : "yy/mm/dd",
  			minDate : new Date("'.$start_date.'"),
  			maxDate : new Date("'.$end_date.'"),
  			showAnim : "blind",
  			duration : 0,
  			changeYear: true,
  			showOn: "both",
  			buttonImage: "' . AngieApplication::getImageUrl('icons/16x16/calendar.png', ENVIRONMENT_FRAMEWORK) . '",
  			buttonImageOnly: true,
  			buttonText : App.lang("Select Date"),
  			firstDay: '.$first_week_day.',
  			hideIfNoPrevNext : true,
  			yearRange : "'.$start_year.':'.$end_year.'"';
          
      if($formatted_value) {
      	$datepicker_options.= ',defaultDate : new Date("'.$formatted_value.'")';
      } // if

			$skip_days_off = array_var($params, 'skip_days_off', true);
      if ($skip_days_off) {
				$datepicker_options.= ',beforeShowDay: App.noWeekendsAndDaysOff';
      } // if
      
      $datepicker_options.='}';

      AngieApplication::useWidget('ui_date_picker', ENVIRONMENT_FRAMEWORK);
      
      return $result . '<script type="text/javascript">$("#' . $params['id'] . '").datepicker(' . $datepicker_options . ');</script></div>';
      
    // Mobile interface
    } elseif($interface == AngieApplication::INTERFACE_PHONE) {
    	 $additional_params = array(
    		'type' => 'date',
				'data-role' => 'datebox',
				'data-options' => '{"mode" : "datebox" , "dateFormat" : "YYYY/MM/DD", "minYear" : "'.$start_year.'", "maxYear" : "'.$end_year.'", "noButtonFocusMode" : "true", "centerHoriz": true, "centerHoriz": true}'
    	);
    	
    	$params = array_merge($params, $additional_params);
    	
      return '<div class="select_date">' . HTML::input($params['name'], $formatted_value, $params) . '</div>';
    } // if
  } // smarty_function_select_date