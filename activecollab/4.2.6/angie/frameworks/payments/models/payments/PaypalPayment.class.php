<?php

/**
 * Paypal class used for paypal payments
 * 
 * @package angie.frameworks.payments
 * @subpackage models.payments
 * 
 */
abstract class PaypalPayment extends Payment {
  
  
    /**
     * Construct paypal payment object
     * 
     * @param array $response
     */
    function __construct($response = null, PaymentGateway $gateway = null) {
      
      if($response) {
       $this->response = $response;
        $this->parseResponse();
        if($gateway instanceof PaymentGateway) {
          $this->setGateway($gateway);
        }//if
      }//if
    }//__construct
    
  
  /**
   * Parse response from service
   * 
   */
  function parseResponse($response = null) {
    if($response) {
      //if returning from gateway 
      $this->response = $response;
    }//if
    if(strtoupper($this->response['ACK']) == 'SUCCESS' || strtoupper($this->response['ACK']) == 'SUCCESSWITHWARNING') {
      $this->setIsError(false);
      
    } else {
      $this->setIsError(true);
      $e = '<ol>';
      for($i=0;$i<5;$i++) {
        if($this->response["L_LONGMESSAGE$i"] && strtolower($this->response["L_SEVERITYCODE$i"]) == 'error') {
          $e .= '<li>' . $this->response["L_LONGMESSAGE$i"] . '</li>';
        } //if
      } //for
      $e .= '</ol>';
      $this->setErrorMessage($e);
    }//if
    $this->setAdditionalProperties($this->response);
    $this->setPaidOn($this->response['TIMESTAMP']);
  }//parseResponse
  
}//PaypalPayment