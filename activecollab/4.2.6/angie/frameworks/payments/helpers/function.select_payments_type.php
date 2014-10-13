<?php

  /**
   * select_payments_type helper
   *
   * @package angie.framework.payments
   * @subpackage helpers
   */
  
  /**
   * Render select payments type control
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_payments_type($params, &$smarty) {
    
    $allow_payments = ConfigOptions::getValue('allow_payments');
    if($allow_payments > 0) {
      $name = array_required_var($params, 'name', true);
      
      AngieApplication::useHelper('display_payments_type','payments');
      
      
      $possibilities = array(
        Payment::USE_SYSTEM_DEFAULT => smarty_function_display_payments_type(array('value' => Payment::USE_SYSTEM_DEFAULT)),
        Payment::DO_NOT_ALLOW => smarty_function_display_payments_type(array('value' => Payment::DO_NOT_ALLOW)), 
        Payment::ALLOW_FULL => smarty_function_display_payments_type(array('value' => Payment::ALLOW_FULL)), 
      );
      
      $selected = array_var($params, 'selected', Payment::USE_SYSTEM_DEFAULT, true);
      
      if($allow_payments > 1) {
        $possibilities[Payment::ALLOW_PARTIAL] = smarty_function_display_payments_type(array('value' => Payment::ALLOW_PARTIAL));
      } // if
      
      return HTML::selectFromPossibilities($name, $possibilities, $selected, $params);
    } else {
      return lang('Payments are disabled');
    }//if
  } // smarty_function_select_payments_type