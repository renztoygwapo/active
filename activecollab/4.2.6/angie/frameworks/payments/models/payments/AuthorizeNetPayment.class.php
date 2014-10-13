<?php

/**
 * AuthorizeNET payment class
 * 
 * @package angie.frameworks.payments
 * @subpackage models.payments
 * 
 */
class AuthorizeNetPayment extends Payment {
  
  
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
  function parseResponse() {
    if($this->response['response_code'] == 1) {
      $this->setIsError(false);
    } else {
      $this->setIsError(true);
      $this->setErrorMessage(urldecode($this->response['response_message']));
    }//if
    $this->setAdditionalProperties($this->response);
    $this->setPaidOn($this->response['timestamp'] ? $this->response['timestamp'] : time());
  }//parseResponse
  
}