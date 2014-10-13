<?php

  /**
   * yes_no helper implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Render yes - no widget
   * 
   * Parameters:
   * 
   * - name - name used for radio group
   * - value - if TRUE Yes will be selected, No will be selected otherwise
   * - yes - yes lang, default is 'Yes'
   * - no - no lang, default is 'No'
   * - id - ID base, if not present script will generate one
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_yes_no($params, &$smarty) {
    $name = array_required_var($params, 'name');
    
    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('yes_no');
    } // if
    
    $value = (boolean) array_var($params, 'value', false, true);
    $mode = array_var($params, 'mode', null, true);
    
    if(array_var($params, 'on_off', false, true)) {
      $yes_display = lang("On");
      $no_display = lang("Off");
    } else {
      $yes_text = array_var($params, 'yes_text', false, true);
      $no_text = array_var($params, 'no_text', false, true);
      
      $yes_display = $yes_text === false ? lang('Yes') : $yes_text;
      $no_display = $no_text === false ? lang('No') : $no_text;
    } // if
    
    // Render as select box
    if($mode == 'select') {
      return HTML::selectFromPossibilities($name, array(
        1 => $yes_display, 
        0 => $no_display, 
      ), $value, $params);
      
    // Render as radio group
    } else {
      $yes_input_attributes = $no_input_attributes = array(
        'name' => $name,
        'type' => 'radio',
        'class' => 'inline',
        'disabled' => (boolean) array_var($params, 'disabled')
      ); // array
         
      $yes_input_attributes['id'] = $params['id'] . 'YesInput';
      $yes_input_attributes['value'] = '1';
      
      $no_input_attributes['id']  = $params['id'] . 'NoInput';
      $no_input_attributes['value'] = '0';
      $no_input_attributes['class'] = 'inline';
      
      if($value) {
        $yes_input_attributes['checked'] = 'checked';
      } else {
        $no_input_attributes['checked'] = 'checked';
      } // if
      
      $yes = HTML::openTag('label', array('for' => $yes_input_attributes['id'], 'class' => 'inline')) . HTML::openTag('input', $yes_input_attributes) . array_var($params, 'yes', $yes_display) . '</label>';
      $no = HTML::openTag('label', array('for' => $no_input_attributes['id'], 'class' => 'inline')) . HTML::openTag('input', $no_input_attributes) . array_var($params, 'no', $no_display) . '</label>';
      
      if(isset($params['label']) && $params['label']) {
        return HTML::label($params['label'], null, null, array('class' => 'main_label'), '') . "<span class=\"yes_no\">$yes $no</span>";
      } else {
        return "<span class=\"yes_no\">$yes $no</span>";
      } // if
    } // if
  } // smarty_function_yes_no