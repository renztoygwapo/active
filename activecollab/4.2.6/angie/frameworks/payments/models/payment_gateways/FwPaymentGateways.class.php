<?php

  /**
   * Framework level payments gateways manager class
   *
   * @package angie.frameworks.payments
   * @subpackage models
   */
  abstract class FwPaymentGateways extends BasePaymentGateways {
  	
    /**
  	 * Return slice of incoming mailbox definitions based on given criteria
  	 * 
  	 * @param integer $num
  	 * @param array $exclude
  	 * @param integer $timestamp
  	 * @return DBResult
  	 */
  	function getSlice($num = 10, $exclude = null, $timestamp = null) {
  		if($exclude) {
  			return PaymentGateways::find(array(
  			  'conditions' => array('id NOT IN (?)', $exclude), 
  			  'order' => 'id', 
  			  'limit' => $num,  
  			));
  		} else {
  			return PaymentGateways::find(array(
  			  'order' => 'id', 
  			  'limit' => $num,  
  			));
  		} // if
  	} // getSlice
  	
  	
  	/**
  	 * Return all gateways with supported currency
  	 * 
  	 * @param $currency_code
  	 * @param $enabled
     * @return array
  	 */
  	static function findAllCurrencySupported($currency_code, $enabled = true) {
  	  $all_gateways = PaymentGateways::find(array(
  	    'conditions' => array('is_enabled = ?',$enabled)
  	  ));
  	  $supported_gateways = array();
      if(is_foreachable($all_gateways)) {
    	  foreach ($all_gateways as $gateway) {
          $currencies = $gateway->getSupportedCurrencies();
          if($currencies === 'all') {
            $supported_gateways[] = $gateway;
          } //if
    	    if(is_foreachable($currencies)) {
        	  if(array_key_exists($currency_code,$currencies)) {
        	    $supported_gateways[] = $gateway;
        	  } //if
    	    } //if
    	  } //foreach
  	  } //if
  	  return $supported_gateways;
  	} //findAllCurrencySupported
  	
  	/**
  	 * Return default payment gateway
  	 * 
  	 * @return PaymentGateway object
  	 */
  	static function findDefault() {
  	   return PaymentGateways::find(array(
        'conditions' => array("is_default = ?", 1),
  	    'one' => true  	    
      ));
    } //findDefault
   
   /**
  	 * Return enabled payment gateways
  	 * 
  	 * @return mixed
  	 */
  	static function findEnabled() {
  	   return PaymentGateways::find(array(
        'conditions' => array("is_enabled = ?", 1),
  	   ));
    } //findDefault
   
   
  }