<?php

  /**
   * Payments interface
   *
   * @package angie.frameworks.payments
   * @subpackage models
   */
  interface IPayments {
    
    /**
     * Returns payments helper for parent object
     *
     * @return IPaymentsImplementation
     */
    function payments();
    
  }