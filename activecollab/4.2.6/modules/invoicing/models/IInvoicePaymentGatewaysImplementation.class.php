<?php
  /**
   * Invoice payment gateways implementation
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class IInvoicePaymentGatewaysImplementation extends IPaymentGatewaysImplementation {
  	
  	/**
     * Construct payment gateway implementation
     *
     * @param Invoice $object
     */
    function __construct(Invoice $object) {
      $this->object = $object;
    } // __construct
    
   
    
  }
?>