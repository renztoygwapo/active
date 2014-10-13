<?php
 // Build on top of admin controller
  AngieApplication::useController('frontend', SYSTEM_MODULE);
  
  /**
   * Make return payment controller implementation
   * 
   * @package angie.frameworks.payments
   * @subpackage controllers
   */
  class FwMakeReturningPaymentController extends FrontendController {
  
    /**
     * Object which we paid (i.e Invoice)
     * 
     * @var $active_object Object
     */
    protected $active_object;
    
    /**
     * Active Payment
     * 
     * @var Payment
     */
    protected $active_payment;
  
  	/**
  	 * Return from gateway
  	 */
  	function paypal_express_checkout_return() {
  	  try {
  	    $token = $this->request->get('token');
    	  $payer_id = $this->request->get('PayerID');
    	  $this->active_payment = Payments::findByToken($token);
    	  if(!$this->active_payment instanceof Payment) {
    	    $this->response->forbidden();
    	  } //if
        $is_public = $this->active_payment->getIsPublic();
    	  
    	  // get parent of payment
    	  if(!$this->active_object) {
    	    if($this->active_payment->getParent()) {
    	      $this->active_object = $this->active_payment->getParent();
    	    } //if
    	  } //if
    	 
        $active_payment_gateway = $this->active_payment->getGateway();
        
        $payment_type = urlencode("Sale");
        $amount = round_up($this->active_payment->getAmount());
  	    $currency = $this->active_payment->getCurrency()->getCode();
  	    $nvp_string = "&TOKEN=$token&PAYERID=$payer_id&PAYMENTACTION=$payment_type&AMT=$amount&CURRENCYCODE=$currency";
  	    $response = $active_payment_gateway->callService(PaypalGateway::PAYPAL_DO_EXPRESS_CHECKOUT_METHOD,$nvp_string);
  	    
  	    $this->active_payment->parseResponse($response);
	    
        if(!$this->active_payment->getIsError() && $this->active_payment instanceof Payment) {
          DB::beginWork('Do express checkout payment @ ' . __CLASS__);
          
          $this->active_payment->setAdditionalProperty('payer_id',$payer_id);
          
          $this->active_payment->setStatus(Payment::STATUS_PAID);
          $this->active_payment->setReason(Payment::REASON_OTHER);
          $this->active_payment->setReasonText(NULL);
          
          $this->active_payment->save();

          $this->active_object->payments()->changeStatus($this->active_payment->getCreatedBy(), $this->active_payment);

          // If not gagged, notify financial managers and payee
          if(!$this->active_object->payments()->isGagged()) {
            AngieApplication::notifications()
              ->notifyAbout(PAYMENTS_FRAMEWORK . '/new_payment', $this->active_object)
              ->setPayment($this->active_payment)
              ->sendToFinancialManagers();

            // Notify customer about the payment
            $paid_by = $this->active_payment->getCreatedBy();

            if($paid_by instanceof IUser && !$paid_by->isFinancialManager()) {
              AngieApplication::notifications()
                ->notifyAbout(PAYMENTS_FRAMEWORK . '/new_payment_to_payer', $this->active_object)
                ->setPayment($this->active_payment)
                ->sendToUsers($paid_by);
            } // if
          } // if

          DB::commit('Do express checkout paid @ ' . __CLASS__);
          
          $this->flash->success(lang('Payment has been processed'));
          if($is_public) {
            //if is paid via public form
            $this->response->redirectToUrl($this->active_object->payments()->getPublicUrl());
          } else{
            if($this->logged_user->isFinancialManager()) {
              $this->response->redirectToUrl($this->active_object->getViewUrl());
            } else {
              $this->response->redirectToUrl($this->active_object->getCompanyViewUrl());
            } // if
          } //if
        } else { 
          $this->flash->error($this->active_payment->getErrorMessage());
        } // if
      } catch (Exception $e) {
        DB::rollback('Failed to make payment @ ' . __CLASS__);
        $this->response->exception($e);
      } //if
    } //paypal_express_checkout_return
		
  	/**
  	 * Cancel from gateway
  	 */
  	function cancel_from_gateway() {
  	  try {
  	    $token = $this->request->get('token');
      	$payer_id = $this->request->get('PayerID');
    	  
      	//find payment by token
  	    $this->active_payment = Payments::findByToken($token);
  	    if(!$this->active_payment instanceof Payment) {
  	      $this->response->forbidden();
  	    } //if
        $is_public = $this->active_payment->getIsPublic();
    	  
  	    // get parent of payment
  	    if(!$this->active_object) {
  	      if($this->active_payment->getParent()) {
  	        $this->active_object = $this->active_payment->getParent();
  	      } //if
  	    } //if
    	    
      	DB::beginWork('Cancaling new payment @ ' . __CLASS__);
      	$this->active_payment->setStatus(Payment::STATUS_CANCELED);
      	$this->active_payment->save();
    	  DB::commit('Payment cancaled @ ' . __CLASS__);
    	  
  	    $this->flash->success(lang('Payment has been canceled.'));
        if($is_public) {
          //if is paid via public form
          $this->response->redirectToUrl($this->active_object->payments()->getPublicUrl());
        } else {
          if($this->logged_user->isFinancialManager()) {
            $this->response->redirectToUrl($this->active_object->getViewUrl());
          } else {
            $this->response->redirectToUrl($this->active_object->getCompanyViewUrl());
          } // if
        } //if

      } catch (Exception $e) {
  	    DB::rollback('Failed to cancel payment @ ' . __CLASS__);
  	    $this->response->exception($e);
      } //try
  	} //cancel_from_gateway
  	
  }