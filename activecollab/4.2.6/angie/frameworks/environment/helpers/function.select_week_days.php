<?php

  /**
   * Render select week days widget
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_week_days($params, &$smarty) {
    $value = array_var($params, 'value', 0, true);
    if(!is_array($value)) {
      $value = array();
    } // if
    
    $id = array_var($params, 'id');
    if(empty($id)) {
      $id = HTML::uniqueId('select_week_days_widget');
    } // if
    
    $name = array_var($params, 'name');
    
    $days = Globalization::getDayNames();
    
    $required = array_var($params, 'required', false, true) || (isset($params['class']) && in_array('required', explode(' ', $params['class'])));
    if($required) {
      $result = '<div id="' . $id . '" class="select_week_days validate_callback select_weekdays_value_present">';
    } else {
      $result = '<div id="' . $id . '" class="select_week_days">';
    } // if
    
    foreach($days as $key => $day) {
      $result .= '<div class="select_week_day">' . open_html_tag('input', array(
        'type' => 'checkbox', 
        'name' => $name . '[]', 
        'value' => $key, 
        'id' => $id . '_' . $key, 
        'class' => 'inline', 
        'checked' => in_array($key, $value), 
      )) . ' ' . label_tag($day, $id . '_' . $key, false, array('class' => 'inline'), '') . '</div>';
    } // foreach
    
    return $result . '</div>';
  } // smarty_function_select_week_days