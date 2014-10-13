<?php

	/**
   * Payment gateways on_payment_methods handler
   *
   * @package activeCollab.modules.payments
   * @subpackage handlers
   */
  
  /**
   * Handler
   *
   * @param NamedList $payment_methods
   * @param User $logged_user
   */
  function payments_handle_on_payment_methods(&$payment_methods) {
    
    $payment_methods[] = array(
  	  'name' => 'payment_methods_common',
  	  'label' => lang('Custom Payment Methods'),
  	  'value' => ConfigOptions::getValue('payment_methods_common')
  	);
  	
  	$payment_methods[] = array(
  	  'name' => 'payment_methods_credit_card',
  	  'label' => lang('Credit Card Payment Methods'),
  	  'value' => ConfigOptions::getValue('payment_methods_credit_card')
  	);
  	
  	$payment_methods[] = array(
  	  'name' => 'payment_methods_online',
  	  'label' => lang('Online Payment Methods'),
  	  'value' => ConfigOptions::getValue('payment_methods_online')
  	);
  	
    
  }