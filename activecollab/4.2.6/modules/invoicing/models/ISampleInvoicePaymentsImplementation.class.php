<?php

  /**
   * Sample invoice payments implementation
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class ISampleInvoicePaymentsImplementation {
    
    /**
     * Construct sample invoice payment implementation
     * 
     * @param SampleInvoice $object
     */
    function __construct(SampleInvoice $object) {
      if(!($object instanceof SampleInvoice)) {
        throw new InvalidInstanceError('object', $object, 'SampleInvoice');
      } // if
    } // __construct

    /**
     * Return payments made to sample invoice object
     *
     * @return array
     */
    function getPayments() {
      return null;
    } // getPayments
    
  }