<?php

  /**
   * Payments implementation that can be attached to any object
   *
   * @package angie.frameworks.payments
   * @subpackage models
   */
  abstract class IPaymentsImplementation {
    
    /**
     * Parent object
     *
     * @var Invoice
     */
    protected $object;
    
    /**
     * Construct paymetns helper
     *
     * @param IPayments $object
     */
    function __construct(IPayments $object) {
      $this->object = $object;
    } // __construct


    /**
     * Determine if payments are enabled
     * 
     * @return boolean
     */
    function isEnabled() {
      $allow_payment = ConfigOptions::getValue('allow_payments');
      $object_payment = $this->object->getAllowPayments();
      $payment_for_object = ConfigOptions::getValue('allow_payments_for_invoice');
      return $allow_payment > 0 && (($object_payment == Payment::USE_SYSTEM_DEFAULT && $payment_for_object > 0) || $object_payment > 0); 
    } //isEnabled
    
    /**
     * Return true if partial payment enabled
     * 
     * @return boolean
     */
    function isPartialEnabled() {
      $allow_payment = ConfigOptions::getValue('allow_payments');
      $object_payment = $this->object->getAllowPayments();
      $payment_for_object = ConfigOptions::getValue('allow_payments_for_invoice');
      return $allow_payment > 1 && (($object_payment == Payment::USE_SYSTEM_DEFAULT && $payment_for_object > 1) || $object_payment > 1); 
    } //isPartialEnabled

    /**
     * Returns true if $user can make a new payment to this object
     *
     * @param User $user
     * @return bool
     */
    function canMake(User $user) {
      return $user->isFinancialManager() || (Invoices::canAccessCompanyInvoices($user, $this->object->getCompany()) && $this->isEnabled() && $this->hasEnabledGateways());
    } //canMake

    /**
     * Can make public payment
     *
     * @param IUser $user
     * @return boolean
     */
    function canMakePublicPayment(IUser $user = null) {
      return $this->hasDefinedGateways() && !$this->object->isCreditInvoice() && $this->isEnabled();
    } //canMakePublicPayment

    /**
     * Returns true if $user can edit payment to this object
     *
     * @param User $user
     * @return bool
     */
    function canEdit(User $user) {
      return $user->isFinancialManager();
    } //canEdit

    /**
     * Returns true if $user can delete payment from this object
     *
     * @param User $user
     * @return bool
     */
    function canDelete(User $user) {
      return $user->isFinancialManager();
    } //canDelete

    /**
     * Returns true if $user can view payment details from this object
     *
     * @param User $user
     * @return bool
     */
    function canView(User $user) {
      return $user->isFinancialManager();
    } //canView

    /**
     * Return true if user can make partial payments to this object
     *
     * @return bool
     */
    function canMakePartial() {
      return $this->isPartialEnabled();
    } //canMakePartial
    
    /**
     * Returns true if there is defined payment gateway in the system
     * 
     * @return boolean
     */
    function hasDefinedGateways() {
      return PaymentGateways::findAllCurrencySupported($this->object->getCurrencyCode());
    } //hasDefinedGateways

    /**
     * Get Currency
     *
     * @return Currency
     */
    function getObjectCurrency() {
      if (method_exists($this->object, 'getCurrency')) {
        return $this->object->getCurrency();
      } else {
        return null;
      } // if
    } // getObjectCurrency
    
    /**
     * Returns true if there is enabled gateways, otherwise return false
     * 
     * @return boolean
     */
    function hasEnabledGateways() {
      return boolval(PaymentGateways::findEnabled());
    }//hasEnabledGateways
    
    /**
     * List of proceeded payments
     * 
     * @var array
     */
    private $gateway_payments = false;
    
    /**
     * Return payments made to the parent object
     * 
     * @return array
     */
    function getPayments() {
      if($this->gateway_payments === false) {
        $this->gateway_payments = Payments::getSliceByObject($this->object);
      } //if
      
      return $this->gateway_payments;
    } //getPayments
    
    /**
     * Return total number of payments for this object
     */
    function getTotalPayments() {
      return Payments::getTotalNumberByObject($this->object);
    } //getTotalPayments
    
    /**
     * Return total amount paied for parent object
     */
    function getPaidAmount() {
      return (float) DB::executeFirstCell("SELECT SUM(amount) AS 'amount_paid' FROM " . TABLE_PREFIX . 'payments WHERE parent_id = ? AND parent_type = ? AND status = ?', $this->object->getId(), get_class($this->object), Payment::STATUS_PAID);
    } //getPaidAmount

    // ---------------------------------------------------
    //  Gag
    // ---------------------------------------------------

    /**
     * Notifications gagged flag
     *
     * @var bool
     */
    private $gagged = false;

    /**
     * Returns true if this implementation is gagged
     *
     * @return bool
     */
    function isGagged() {
      return $this->gagged;
    } // isGagged

    /**
     * Gag notifications
     */
    function gag() {
      $this->gagged = true;
    } // gag

    /**
     * Ungag notifications
     */
    function ungag() {
      $this->gagged = false;
    } // ungag
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------

    /**
     * Return make payment URL for parent object
     *
     * @return string
     */
    function getUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_payments', $this->object->getRoutingContextParams());
    } // getUrl
    
    /**
     * Return make payment URL
     * 
     * @param float $amount
     * @return string
     */
    function getAddUrl($amount = null) {
      $params = $this->object->getRoutingContextParams();
      
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
     * Delete all payments for this object
     *
     * @return boolean
     */
    function delete() {
      return DB::execute('DELETE FROM ' . TABLE_PREFIX . 'payments WHERE parent_type = ? AND parent_id = ?', get_class($this->object), $this->object->getId());
    } //delete

    /**
     * Payment made event
     *
     * @param $payment
     * @return bool
     */
    function paymentMade($payment) {
      return true;
    } // paymentMade

    /**
     * Payment updated
     *
     * @param $payment
     * @return bool
     */
    function paymentUpdated($payment) {
      return true;
    } // paymentMade

    /**
     * Payment Removed
     *
     * @param $payment
     * @return bool
     */
    function paymentRemoved($payment) {
      return true;
    } // paymentRemoved

    // ---------------------------------------------------
    //  Describe
    // ---------------------------------------------------

    /**
     * Describe comment related information
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @param array $result
     */
    function describe(IUser $user, $detailed, $for_interface, &$result) {
      $result['payments'] = array(
        'paid_amount'       => $this->getPaidAmount(),
        'total_payments'    => $this->getTotalPayments(),
        'permissions'       => array(
          'can_view'            => $this->canView($user),
          'can_edit'            => $this->canEdit($user),
          'can_delete'          => $this->canDelete($user)
        ),
        'show_payment_btn'  => ($this->hasDefinedGateways() && $this->canMake($user) || ($user->isFinancialManager())),
        'add_url'           => $this->getAddUrl()
      );
    } // describe
  
  }