<?php

  /**
   * Stripe class used for stripe payments
   *
   * @package angie.frameworks.payments
   * @subpackage models.payments
   *
   */
  class StripePayment extends Payment {

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
    if($this->response instanceof Stripe_Charge) {
      $this->setIsError(false);
      $this->setAdditionalProperties($this->response);
      $this->setPaidOn($this->response['created']);
    } else {
      $this->setIsError(true);
      $this->setErrorMessage($this->response['Error processing Stripe payment']);
    } //if
  }//parseResponse




}//PaypalPayment