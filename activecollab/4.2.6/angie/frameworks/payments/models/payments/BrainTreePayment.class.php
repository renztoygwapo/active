<?php

  /**
   * BrainTree class used for braintree payments
   *
   * @package angie.frameworks.payments
   * @subpackage models.payments
   *
   */
  class BrainTreePayment extends Payment {

  /**
   * Construct object
   *
   * @param null $response
   * @param PaymentGateway $gateway
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
    if($this->response->success) {
      $this->setIsError(false);
      $this->setAdditionalProperties($this->response);
      $this->setPaidOn($this->response->transaction->createdAt->getTimestamp());
    } else {
      $this->setIsError(true);
      $this->setErrorMessage($this->response->message);
    } //if
  }//parseResponse




}//BrainTreePayment