<?php

  /**
   * Invoice payments implementation
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class IInvoicePaymentsImplementation extends IPaymentsImplementation {
    
    /**
     * Construct invoice payment implementation
     * 
     * @param Invoice $object
     */
    function __construct(Invoice $object) {
      if($object instanceof Invoice) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'Invoice');
      } // if
    } // __construct

    /**
     * Return make payment URL for parent object
     *
     * @param integer $amount
     * @return string
     */
    function getUrl() {
      $params = $this->object->getRoutingContextParams();

      $params['company_id'] = $this->object->getCompanyId();

      return Router::assemble($this->object->getRoutingContext() . '_payments', $params);
    } // getUrl

    /**
     * Return make payment URL
     *
     * @param float $amount
     * @return string
     */
    function getAddUrl($amount = null) {
      $params = $this->object->getRoutingContextParams();

      $params['company_id'] = $this->object->getCompanyId();

      if($amount) {
        if(is_array($params)) {
          $params['amount'] = $amount;
        } else {
          $params = array('amount' => $amount);
        } // if
      } // if
      return Router::assemble($this->object->getRoutingContext() . '_payments_add', $params);
    } // getAddUrl

    /**
     * Return public invoice URL
     *
     * @return string
     */
    function getPublicUrl() {
      return Router::assemble('public_invoice', array(
          'invoice_id' => $this->object->getId(),
          'client_id' => $this->object->getCompanyId(),
          'invoice_hash' => $this->object->getHash())
      );
    } // getPublicUrl

    /**
     * Return amount left for paying
     * 
     * @return float
     */
    function getAmountToPay() {
      $taxed_total = str_replace(',','.',strval($this->object->getTotal(true)));
      $paid_amount = str_replace(',','.',strval($this->getPaidAmount()));
      $decimal_spaces = $this->getObjectCurrency() instanceof Currency ? $this->getObjectCurrency()->getDecimalSpaces() : 3;
      if(function_exists('bcsub')) {
        $left_to_pay = bcsub($taxed_total,$paid_amount,$decimal_spaces);
      } else {
        $left_to_pay = $taxed_total - $paid_amount;
      }//if
     
      if($left_to_pay < 0) {
        return 0; 
      } else {
        return $left_to_pay;
      } // if
    } // getAmountToPay
    
    /**
     * Return % amount left for paying
     * 
     * @return float
     */
    function getPercentPaid() {
      return (float) Globalization::formatNumber(($this->getPaidAmount() * 100) / $this->object->getTotal(true));
    } // getPercentPaid
    
    /**
     * Change invoice status
     * 
     * @param IUser $by
     * @param Payment $payment
     * @param $additional_params
     */
    function changeStatus(IUser $by, $payment = null, $additional_params = null) {
      try {
        DB::beginWork('Change invoice status @ ' . __CLASS__);
        
        if($this->getAmountToPay() == 0 && $this->object->getStatus() == INVOICE_STATUS_ISSUED) {
          $this->gag(); // Make sure that no notifications are sent, we'll send notifications with markAsPaid() invoice method
          $this->object->markAsPaid($by, $payment, $additional_params); // Mark as paid
        } elseif($this->getAmountToPay() > 0 && $this->object->getStatus() == INVOICE_STATUS_PAID) {
          $this->object->setStatus(INVOICE_STATUS_ISSUED, $this->object->getIssuedBy(), $this->object->getIssuedOn()); // Revert to issued
          $this->object->save();
        } // if
        
        DB::commit('Invoice status changed @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to update invoice status @ ' . __CLASS__);
        throw $e;
      } // try
    } // changeStatus
    
    /**
     * Check can payment be paid
     * 
     * @param float $amount_to_pay
     * @return boolean
     * 
     */
    function canMarkAsPaid($amount_to_pay) {
      if(function_exists('bcadd')) {
        $decimal_spaces = $this->getObjectCurrency() instanceof Currency ? $this->getObjectCurrency()->getDecimalSpaces() : 3;
        $will_be_paid = bcadd($this->getPaidAmount(), $amount_to_pay, $decimal_spaces);
      } else {
        $will_be_paid = $this->getPaidAmount() + $amount_to_pay;
      }//if
      if($this->getAmountToPay() == 0 || $this->object->getTotal(true) < $will_be_paid) {
        throw new Error(lang("If you add this payment you will run the maximum payment amount. No payment added"));
      } // if
      
      return true;
    } // canMarkAsPaid

    /**
     * Describe comment related information
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @param array $result
     */
    function describe(IUser $user, $detailed, $for_interface, &$result) {
      parent::describe($user, $detailed, $for_interface, $result);

      $result['payments']['paid_amount_percentage'] = $this->getPercentPaid();
      $result['payments']['show_payment_btn'] = $result['payments']['show_payment_btn'] && $this->object->getStatus() == INVOICE_STATUS_ISSUED;
    } // describe

    /**
     * Payment made event
     *
     * @param Payment $payment
     */
    function paymentMade($payment) {
      $this->object->recalculate(true);
      return parent::paymentMade($payment);
    } // paymentMade

    /**
     * Payment updated
     *
     * @param Payment $payment
     */
    function paymentUpdated($payment) {
      $this->object->recalculate(true);
      return parent::paymentUpdated($payment);
    } // paymentUpdated

    /**
     * Payment Removed
     *
     * @param Payment $payment
     */
    function paymentRemoved($payment) {
      $this->object->recalculate(true);
      return parent::paymentRemoved($payment);
    } // paymentRemoved
    
  }