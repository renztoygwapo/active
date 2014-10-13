<?php

  /**
   * select_payment_method helper
   *
   * @package angie.framework.payments
   * @subpackage helpers
   */
  
  /**
   * Render select payments method control
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_payment_method($params, &$smarty) {
    
    $value = array_var($params, 'value', null);
    $name = array_required_var($params, 'name', true);
    $type= array_var($params,'type','custom');
    
    $payment_methods_common = ConfigOptions::getValue('payment_methods_common') ? ConfigOptions::getValue('payment_methods_common') : array();
    $payment_methods_cc = ConfigOptions::getValue('payment_methods_credit_card') ? ConfigOptions::getValue('payment_methods_credit_card') : array();
    $payment_methods_online = ConfigOptions::getValue('payment_methods_online') ? ConfigOptions::getValue('payment_methods_online') : array();

    if(is_foreachable($payment_methods_common)) {
      foreach ($payment_methods_common as $method) {
        $common_options[] = HTML::optionForSelect($method, $method, $method == $value);
      }//foreach
    }//if
    
    if(is_foreachable($payment_methods_cc)) {
      foreach ($payment_methods_cc as $method) {
        $cc_option[] = HTML::optionForSelect($method, $method, $method == $value);
      }//foreach
    }//if
    
    if(is_foreachable($payment_methods_online)) {
      foreach ($payment_methods_online as $method) {
        $online_option[] = HTML::optionForSelect($method, $method, $method == $value);
      }//foreach
    }//if
    
    if($common_options || $cc_option || $online_option) {
      $options = array(
        HTML::optionGroup(lang('Common'), $common_options),
        HTML::optionGroup(lang('Credit Card'), $cc_option),
        HTML::optionGroup(lang('Online Payment'), $online_option),
      );
    }//if
    
    if($options) {
      return HTML::select($name, $options, $params);
    } else {
      return lang('No Payment methods defined.');
    }
    
  } // smarty_function_select_payment_method