<?php

  /**
   * radio_field helper implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Render radio field
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_radio_field($params, &$smarty) {
    $name = array_var($params, 'name', null, true);
    $checked = (boolean) array_var($params, 'checked', false, true);
    
    if(array_key_exists('value', $params)) {
      if(is_bool($params['value'])) {
        $params['value'] = $params['value'] ? 1 : 0;
      } // if
      
      if(array_key_exists('pre_selected_value', $params) && $params['pre_selected_value'] == $params['value']) {
        $checked = 1;
      } // if
    } // if
    
    $label = array_var($params, 'label', null, true);
    
    if($label) {
      if(empty($params['id'])) {
        $params['id'] = HTML::uniqueId('radio_field');
      } // if
      
      if(isset($params['class'])) {
        $params['class'] .= ' auto radio_field';
      } else {
        $params['class'] = 'auto radio_field';
      } // if
      
      return HTML::openTag('label', array(
        'for' => $params['id'], 
        'class' => 'auto'
      )) . HTML::radio($name, $checked, $params) . ' <span class="radio_field_label">' . clean(lang($label)) . '</span></label>';
    } else {
      return HTML::radio($name, $checked, $params);
    } // if
  } // smarty_function_radio_field