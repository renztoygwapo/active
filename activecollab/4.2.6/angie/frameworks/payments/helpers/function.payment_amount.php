<?php

  /**
   * smarty_function_payment_amount helper
   *
   * @package angie.framework.payments
   * @subpackage helpers
   */
  
  /**
   * Render select payment amount control
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_payment_amount($params, &$smarty) {
    
    $set_as_readonly = array_var($params,'set_as_readonly', false);
    
    $value = array_var($params, 'value', 0);
    $name = array_var($params, 'name', 'payment[amount]');
    $currency = array_var($params, 'currency', null, true);
    
    if($set_as_readonly) {
      //allow only full
      AngieApplication::useHelper('money', GLOBALIZATION_FRAMEWORK, 'modifier');
      
      $control = '<label>' . Globalization::formatMoney($value, $currency) . '</label>';
      $control .= '<input type="hidden" name="' . $name . '" value="' . $value . '" />';
    } else {
      //allow partial
      AngieApplication::useHelper('money_field', GLOBALIZATION_FRAMEWORK);
      $control = smarty_function_money_field($params, $smarty);
    }//if
    
    return $control;
    
  } // smarty_function_select_milestone
