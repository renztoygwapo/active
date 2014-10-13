<?php

  /**
   * Custom payment class
   *
   * @package angie.frameworks.payments
   * @subpackage models.payments
   */
  class CustomPayment extends Payment {

    /**
     * Construct paypal payment object
     *
     * @param array $response
     */
    function __construct(CustomPaymentGateway $gateway) {
      $this->setIsError(false);
      $this->setGatewayId(0);
      $this->setGatewayType(get_class($gateway));
    } // __construct

  }