<?php
  
  /**
   * Freamework payments controller
   * 
   * @package angie.freameworks.payments
   * @subpackage controllers
   */
  abstract class FwPaymentsController extends Controller {

  	/**
  	 * Active invoice
  	 * 
  	 * @var Invoice
  	 */
  	protected $active_object = false;
  	
  	/**
  	 * Active payment
  	 * 
  	 * @var Payment
  	 */
  	protected $active_payment = false;
  	
  	/**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      $payment_id = $this->request->getId('payment_id');
      if($payment_id) {
        $this->active_payment = Payments::findById($payment_id);
      } //if
     
      $this->response->assign(array(
        'active_object' => $this->active_object,
        'active_payment' => $this->active_payment,
        'today' => new DateTimeValue()
      ));
    } // __before
    
    /**
     * List all payments for the given object
     */
    function payments() {
      $this->response->assign(array(
        'payment_list' => Payments::findByObject($this->active_object),
      ));
    } // payments
    
    /**
     * Add new payment
     */
    function payments_add() {
      if($this->request->isAsyncCall() || $this->request->isApiCall()) {
        
        if($this->active_object->payments()->canMake($this->logged_user)) {
          $payment_data = $this->request->post('payment');
            
          $this->response->assign(array(
            'payment_gateways' => PaymentGateways::findAllCurrencySupported($this->active_object->getCurrencyCode()),
            'default_gateway' => PaymentGateways::findDefault(),
            'payment_data'	=> $payment_data,
            'invoice_notify_on_payment' => ConfigOptions::getValue('invoice_notify_on_payment')
          ));
          
          if($this->request->isSubmitted()) { 
            try {
              
              $cc_number = trim($payment_data['credit_card_number']);
              if($cc_number) {
                $response = is_valid_cc($cc_number);
                if($response !== true) {
                  throw new Error($response);
                }//if
              }//if
              
              $payment_data['amount'] = str_replace(",","",$payment_data['amount']); 
              
              if(!is_numeric($payment_data['amount'])) {
                throw new Error('Total amount must be numeric value');
              }//if
              
              //check if this payment can proceed
              $this->active_object->payments()->canMarkAsPaid($payment_data['amount']);
              
              DB::beginWork('Creating new payment @ ' . __CLASS__);
                       
              $payment_gateway_id = $this->request->post('payment_gateway_id');
              
              if($payment_gateway_id && $payment_gateway_id > 0) {
                $active_payment_gateway = PaymentGateways::findById($payment_gateway_id);
                if(!$active_payment_gateway instanceof PaymentGateway) {
                  $this->response->notFound();
                }//if
              } else {
                $active_payment_gateway = new CustomPaymentGateway();
              }//if

              //if this method exists, please check is all necessery extension loaded
              if(method_exists($active_payment_gateway, 'checkEnvironment')) {
                $active_payment_gateway->checkEnvironment();
              }//if


              $this->active_payment = $active_payment_gateway->makePayment($payment_data, $this->active_object->getCurrency(), $this->active_object);
              
              if(!$this->active_payment->getIsError() && $this->active_payment instanceof Payment) {
                $this->active_payment->setParent($this->active_object);
                $this->active_payment->setAttributes($payment_data);
                $this->active_payment->setStatus(Payment::STATUS_PAID);
                $this->active_payment->setCurrencyId($this->active_object->getCurrency()->getId());
                
                //if we do express checkout 
                if($this->active_payment instanceof PaypalExpressCheckoutPayment) {
                 $this->active_payment->setStatus(Payment::STATUS_PENDING);
                 $this->active_payment->setReason(Payment::REASON_OTHER);
                 $this->active_payment->setReasonText(lang('Waiting response from paypal express checkout'));
                } //if
                
                $this->active_payment->save();

                $this->active_object->payments()->changeStatus($this->logged_user, $this->active_payment, $payment_data);
                $this->active_object->activityLogs()->logPayment($this->logged_user);

                // Notify if not gagged
                if(!($this->active_payment instanceof PaypalExpressCheckoutPayment) && !$this->active_object->payments()->isGagged()) {
                  AngieApplication::notifications()
                    ->notifyAbout(PAYMENTS_FRAMEWORK . '/new_payment', $this->active_object)
                    ->setPayment($this->active_payment)
                    ->sendToFinancialManagers();

                  $payer = $this->active_payment->getCreatedBy();
                
                  if($payer instanceof IUser && !$payer->isFinancialManager()) {
                    AngieApplication::notifications()
                      ->notifyAbout(PAYMENTS_FRAMEWORK . '/new_payment_to_payer', $this->active_object)
                      ->setPayment($this->active_payment)
                      ->sendToUsers($payer);
                  }//if
                }//if

                $this->active_object->payments()->paymentMade($this->active_payment);
               
                DB::commit('Payment created @ ' . __CLASS__);
                
                $this->response->respondWithData($this->active_payment, array('as' => 'payment', 'detailed' => true));
              } else {
                throw new Error($this->active_payment->getErrorMessage());
              } //if
            } catch (Exception $e) {
              DB::rollback('Failed to make payment @ ' . __CLASS__);
              $this->response->exception($e);
            }//try
          } //if
        } else {
         
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      }//if
    } // payments_add
    
    /**
     * View single
     */
    function payment_view() {
      if($this->active_payment->isNew()) {
        $this->response->notFound();
      } //if
    } // payment_view
    
    /**
     * Update single payment
     */
    function payment_edit() {
      if($this->request->isAsyncCall() || $this->request->isApiCall()) {
      
        if(!$this->active_object->payments()->canEdit($this->logged_user) || !$this->active_payment instanceof Payment) {
          $this->response->forbidden();
        } //if
        
        if($this->active_payment->isNew()) {
          $this->response->notFound();
        } //if
        
        // get parent of payment
        if(!$this->active_object) {
          if($this->active_payment->getParent()) {
            $this->active_object = $this->active_payment->getParent();
          } //if
        } //if
    	
        if($this->request->isSubmitted()) {
          try {
            $payment_data = $this->request->post('payment');
            
            if($payment_data['status'] == Payment::STATUS_PAID && !$this->active_payment->isPaid()){
              $this->active_object->payments()->canMarkAsPaid($this->active_payment->getAmount());
            } //if
            
            DB::beginWork('Updating payment @ ' . __CLASS__);

            if($this->active_payment instanceof CustomPayment) {
              $this->active_payment->setMethod($payment_data['method']);
              $this->active_payment->setPaidOn($payment_data['paid_on']);
            } //if

            $this->active_payment->setStatus($payment_data['status']);
            if($payment_data['status_reason']) {
              $this->active_payment->setReason($payment_data['status_reason']);
              $this->active_payment->setReasonText($payment_data['status_reason_text']);
            } else {
              $this->active_payment->setReason(null);
              $this->active_payment->setReasonText(null);
            } //if
            
            $this->active_payment->setComment($payment_data['comment']);
            $this->active_payment->save();

            $this->active_object->payments()->changeStatus($this->logged_user, $this->active_payment);
            $this->active_object->payments()->paymentUpdated($this->active_payment);

            DB::commit('Payment updated @ ' . __CLASS__);

            $this->response->respondWithData($this->active_payment, array('as' => 'payment', 'detailed' => true));
          } catch (Exception $e) {
            DB::rollback('Failed to update payment @ ' . __CLASS__);
            $this->response->exception($e);
          } //try
        } //if
      } else {
        $this->response->badRequest();
      }//if
    } // payment_edit
    
    /**
     * Delete single payment
     */
    function payment_delete() {
      if($this->request->isAsyncCall() || $this->request->isApiCall()) {
        try {
          if(!$this->active_object->payments()->canDelete($this->logged_user) || !$this->active_payment instanceof Payment) {
            $this->response->forbidden();
          } //if
          
          // get parent of payment
          if(!$this->active_object) {
            if($this->active_payment->getParent()) {
              $this->active_object = $this->active_payment->getParent();
            } //if
          } //if
            
          DB::beginWork('Deleting payment @ ' . __CLASS__);

          $this->active_payment->setStatus(Payment::STATUS_DELETED);
          $this->active_payment->save();

          $this->active_object->payments()->changeStatus($this->logged_user);
          $this->active_object->payments()->paymentRemoved($this->active_payment);

          DB::commit('Payment deleted @ ' . __CLASS__);
          
          $this->response->respondWithData($this->active_payment, array('as' => 'payment', 'detailed' => true));
      	} catch(Exception $e) {
      	  DB::rollback('Failed to delete payment @ ' . __CLASS__);
      	  $this->response->exception($e);
      	} // try
      } else {
        $this->response->badRequest();
      }//if
    } // payment_delete
     
  }