<?php

  /**
   * checkbox_field helper implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Render checkbox field
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_checkbox_field($params, &$smarty) {
    $name = array_var($params, 'name', null, true);
//    $value = (boolean) array_var($params, 'value', false, true);
    $label = array_var($params, 'label', null, true);
    $checked = array_var($params, 'checked', false, true);
    
    if($label) {
      if(empty($params['id'])) {
        $params['id'] = HTML::uniqueId('checkbox_field');
      } // if
      
      if(isset($params['class'])) {
        $params['class'] .= ' auto checkbox_field';
      } else {
        $params['class'] = 'auto checkbox_field';
      } // if
      
      return HTML::openTag('label', array(
        'for' => $params['id'], 
        'class' => 'auto'
      )) . HTML::checkbox($name, $checked, $params) . ' <span class="checkbox_field_label">' . clean(lang($label)) . '</span></label>';
    } else {
      return HTML::checkbox($name, $checked, $params);
    } // if
  } // smarty_function_checkbox_field