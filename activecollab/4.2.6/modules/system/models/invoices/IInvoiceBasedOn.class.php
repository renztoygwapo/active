<?php
  
  /**
   * Invoice interface
   *
   * @package modules.invoicing
   * @subpackage models
   */
  interface IInvoiceBasedOn {
    
    /**
     * Returns invoice helper for parent object
     *
     * @return IInvoiceBasedOnImplementation
     */
    function &invoice();
    
  }