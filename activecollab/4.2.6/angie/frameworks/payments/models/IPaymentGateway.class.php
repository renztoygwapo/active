<?php

  /**
   * Payment gateway interface
   *
   * @package angie.frameworks.payments
   * @subpackage models
   */
  interface IPaymentGateway {
    
    /**
     * Returns payment gateway helper for parent object
     *
     * @return IPaymentGatewayImplementation
     */
    function paymentGateway();
    
  }