<?php

  /**
   * Framework level payments manager class
   *
   * @package angie.frameworks.payments
   * @subpackage models
   */
  class FwPayments extends BasePayments {
    
    /**
     * Return payment statuses
     * 
     * @return array
     */
    static function getStatuses() {
      return array(
        Payment::STATUS_PAID => lang('Paid'),
        Payment::STATUS_CANCELED => lang('Canceled'),
        Payment::STATUS_PENDING => lang('Pending'),
        Payment::STATUS_DELETED => lang('Deleted'),
      ); 
    }//getStatuses

    /**
     * Return verbose payment type by given payment instance
     *
     * @param Payment|PaymentGateway $for
     * @return string
     */
    static function getVerbosePaymentType($for) {
      
      if($for instanceof PaymentGateway) {
        $gateway_class = get_class($for);
      } elseif($for instanceof Payment) {
        $gateway_class = $for->getGatewayType();
      } elseif (is_string($for)) {
        $gateway_class = $for;
      } else {
        $gateway_class = null;
      } // if

  
      switch($gateway_class) {
        case 'AuthorizeAimGateway':
          return 'Authorize AIM';
          break;
        case 'CustomPaymentGateway':
          return 'Custom Payment';
          break;
        case 'PaypalDirectGateway':
          return 'Paypal Direct';
          break;
        case 'PaypalExpressCheckoutGateway':
          return 'Paypal Express Checkout';
          break;
      } // if
    } // getVerbosePaymentType
    
    /**
     * Find payment by parent object
     * 
     * @param object $parent_object
     * @param string $status
     */
    static function findByObject($parent_object, $status = false) {
      if($status) {
        return Payments::find(array(
          'conditions' => array('parent_id = ? AND status = ?', $parent_object->getId(), $status),
          'order' => 'id',
        )); 
      } else {
        return Payments::find(array(
          'conditions' => array('parent_id = ?', $parent_object->getId()),
          'order' => 'id',
        )); 
      } //if
   } //findByObject
   
   
   /**
     * Find payment by gateway
     * 
     * @param object $parent_object
     * @param string $status
     */
    static function findByGateway(PaymentGateway $gateway, $status = false) {
      if($status) {
        return Payments::find(array(
          'conditions' => array('gateway_id = ? AND gateway_type = ? AND status = ?', $gateway->getId(), get_class($gateway), $status),
          'order' => 'id',
        )); 
      } else {
        return Payments::find(array(
          'conditions' => array('gateway_id = ? AND gateway_type = ?', $gateway->getId(), get_class($gateway)),
          'order' => 'id',
        )); 
      } //if
   } //findByGateway
   
   
    /**
     * Return payments by company
     *
     * @param Company $company
     * @return array
     */
    function findByCompany(Company $company) {
      $invoices_table = TABLE_PREFIX . 'invoices';
      $paymnets_table = TABLE_PREFIX . 'payments';
      
      return Payments::findBySQL("SELECT $paymnets_table.* FROM $invoices_table, $paymnets_table WHERE $paymnets_table.parent_id = $invoices_table.id AND $invoices_table.company_id = ? ORDER BY $paymnets_table.paid_on DESC", $company->getId());
    } // findByCompany
    
   /**
  	 * Return slice of payments definitions based on given criteria
  	 * 
  	 * @param integer $num
  	 * @param array $exclude
  	 * @param integer $timestamp
  	 * @return DBResult
  	 */
  	function getSliceByObject($parent_object, $num = 100, $exclude = null, $timestamp = null) {
  		if($exclude) {
  			return Payments::find(array(
  			  'conditions' => array('id NOT IN (?) AND parent_id = ?', $exclude,$parent_object->getId()), 
  			  'order' => 'id', 
  			  'limit' => $num,  
  			));
  		} else {
  			return Payments::find(array(
  			  'conditions' => array('parent_id = ?', $parent_object->getId()), 
  			  'order' => 'id', 
  			  'limit' => $num,  
  			));
  		} // if
  	} // getSlice
   
  	/**
  	 * Return total payments number for object
  	 * 
  	 * @param $object
  	 */
  	function getTotalNumberByObject($object) {
  	  return Payments::count(array('parent_id = ?', $object->getId()));
  	}//getTotalNumberByObject
  	
   /**
    * Return payment by parent object
    * 
    * @param object $parent_object
    * @param string $status
    */
   static function sumByObject($parent_object, $status = Payment::STATUS_PAID) {
     return (float) DB::executeFirstCell("SELECT SUM(amount) AS 'amount_paid' FROM " . TABLE_PREFIX . 'payments WHERE parent_id = ? AND status = ?', $parent_object->getId(),$status);
   } //sumByObject
   
   /**
    * Find payment by token
    * 
    * @param string $token
    */
   static function findByToken($token, $type = 'PaypalExpressCheckoutPayment') {
     
     $payments = Payments::find(array(
        'conditions' => array('type = ?', $type),
        'order' => 'id DESC'
      ));
      if(is_foreachable($payments)) {
        foreach($payments as $payment) {
          if($payment->getToken() == $token) {
            return $payment;
          }//if
        }//foreach
      }//if
      
   } //findByToken
   
  } //FwPayments