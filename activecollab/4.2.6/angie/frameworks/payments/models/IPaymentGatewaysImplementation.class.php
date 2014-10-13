<?php
  /**
   * Payment gateway helper implementation
   *
   * @package angie.frameworks.payments
   * @subpackage models
   */
  abstract class IPaymentGatewaysImplementation {
    
    /**
     * Parent object
     *
     * @var IPayments
     */
    protected $object;
    
    /**
     * Construct payment gateway helper
     *
     * @param IPaymentGateway $object
     */
    function __construct(IPaymentGateway  $object) {
      $this->object = $object;
    } // __construct
    
        
  }