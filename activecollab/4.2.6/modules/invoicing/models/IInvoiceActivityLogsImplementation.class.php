<?php

  /**
   * Invoice activity logs helper implementation
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class IInvoiceActivityLogsImplementation extends IActivityLogsImplementation {
  
    
    /**
     * Create issue log for parent invoice
     * 
     * @param IUser $by
     */
    function logIssuing(IUser $by) {
      return ActivityLogs::log($this->object, 'invoice/issued', $by, $this->getTarget('issued'), $this->getComment('issued'));
    } // logIssuing
    
    /**
     * Log when paret invoice is fully paid
     * 
     * @param IUser $by
     */
    function logPaid(IUser $by) {
      return ActivityLogs::log($this->object, 'invoice/paid', $by, $this->getTarget('paid'), $this->getComment('paid'));
    } // logPaid
    
    /**
     * Log when parent invoice is canceled
     * 
     * @param IUser $by
     */
    function logCancelation(IUser $by) {
      return ActivityLogs::log($this->object, 'invoice/canceled', $by, $this->getTarget('canceled'), $this->getComment('canceled'));
    } // logCancelation
    
    /**
     * Log when new payment is made
     * 
     * @param IUser $by
     */
    function logPayment(IUser $by) {
      return ActivityLogs::log($this->object, 'invoice/new_payment', $by, $this->getTarget('new_payment'), $this->getComment('new_payment'));
    } // logPayment
    
  }