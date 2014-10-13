<?php

  // Extend public controller
  AngieApplication::useController('frontend', SYSTEM_MODULE);

  /**
   * Project invoices public controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class PublicInvoicesController extends FrontendController {

    /**
     * Selected invoice
     *
     * @var Invoice
     */
    protected $active_invoice;

    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();

      $client_id = $this->request->get('client_id');
      $invoice_id = $this->request->get('invoice_id');
      $invoice_hash = $this->request->get('invoice_hash');
      if($client_id && $invoice_id && $invoice_hash) {
        $this->active_invoice = Invoices::find(array(
          'conditions' => array('company_id = ? AND id = ? AND hash = ? AND status IN (?)', $client_id, $invoice_id, $invoice_hash, array(INVOICE_STATUS_ISSUED, INVOICE_STATUS_PAID)),
          'one' => true
        ));
      } else {
        $this->response->notFound();
      } // if

      if($this->active_invoice instanceof Invoice) {

        if(!$this->active_invoice->payments()->canMakePublicPayment()) {
          $this->response->notFound();
        } //if

        $this->smarty->assign(array(
          'active_object' => $this->active_invoice,
          'today' => new DateTimeValue()
        ));
      } else {
        $this->response->notFound();
      } // if
    } // __constructor

    /**
     * Pay requested invoice
     */
    function pay() {
      if($this->active_invoice->isLoaded()) {

        if($this->request->isSubmitted()) {
          try {
            $payment_data = $this->request->post('payment');
            $payment_gateway_id = $this->request->post('payment_gateway_id');

            $payment_data['payment_gateway_id'] = $payment_gateway_id;

            $payer_email = $payment_data['payer_email'];
            if($payer_email && is_valid_email($payer_email)) {
              $payer = Users::findByEmail($payer_email);
              if(!$payer instanceof User) {
                $payer = new AnonymousUser($payer_email, $payer_email);
              } //if
            } //if

            if(!$payer instanceof IUser) {
              throw new Error('Please enter valid Email address');
            } //if

            $this->response->assign(array(
              'payment_data'	=> $payment_data,
            ));

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
              $this->active_invoice->payments()->canMarkAsPaid($payment_data['amount']);

              DB::beginWork('Creating new payment @ ' . __CLASS__);

              if($payment_gateway_id && $payment_gateway_id > 0) {
                $active_payment_gateway = PaymentGateways::findById($payment_gateway_id);
                if(!$active_payment_gateway instanceof PaymentGateway) {
                  $this->response->notFound();
                }//if
              } else {
                throw new Error('Please select preferred payment option');
              }//if

              //if this method exists, please check is all necessery extension loaded
              if(method_exists($active_payment_gateway, 'checkEnvironment')) {
                $active_payment_gateway->checkEnvironment();
              }//if

              $active_payment = $active_payment_gateway->makePayment($payment_data, $this->active_invoice->getCurrency(), $this->active_invoice);

              if(!$active_payment->getIsError() && $active_payment instanceof Payment) {

                $active_payment->setIsPublic(true);
                $active_payment->setCreatedBy($payer);
                $active_payment->setParent($this->active_invoice);
                $active_payment->setAttributes($payment_data);
                $active_payment->setStatus(Payment::STATUS_PAID);
                $active_payment->setCurrencyId($this->active_invoice->getCurrency()->getId());

                //if we do express checkout
                if($active_payment instanceof PaypalExpressCheckoutPayment) {
                  $active_payment->setStatus(Payment::STATUS_PENDING);
                  $active_payment->setReason(Payment::REASON_OTHER);
                  $active_payment->setReasonText(lang('Waiting response from paypal express checkout'));
                } //if

                $active_payment->save();

                //TO-Do check this
                $this->active_invoice->payments()->changeStatus($payer, $active_payment, $payment_data);
                $this->active_invoice->activityLogs()->logPayment($payer);

                // Notify if not gagged
                if(!($active_payment instanceof PaypalExpressCheckoutPayment) && !$this->active_invoice->payments()->isGagged()) {
                  AngieApplication::notifications()
                    ->notifyAbout(PAYMENTS_FRAMEWORK . '/new_payment', $this->active_invoice)
                    ->setPayment($active_payment)
                    ->sendToFinancialManagers();
                }//if

                if($payer instanceof IUser && !$payer->isFinancialManager()) {
                  AngieApplication::notifications()
                    ->notifyAbout(PAYMENTS_FRAMEWORK . '/new_payment_to_payer', $this->active_invoice)
                    ->setPayment($active_payment)
                    ->sendToUsers($payer);
                }//if

                $this->active_invoice->payments()->paymentMade($active_payment);

                DB::commit('Payment created @ ' . __CLASS__);
                if($active_payment instanceof PaypalExpressCheckoutPayment) {
                  $this->response->redirectToUrl($active_payment->getRedirectUrl());
                } //if
              } else {
                throw new Error($active_payment->getErrorMessage());
              } //if

            $this->response->assign(array(
              'active_object'	=> $this->active_invoice,
            ));
          } catch (Exception $e) {
            DB::rollback('Failed to make payment @ ' . __CLASS__);
            $this->response->assign('errors', $e);
          }//try
        } //if
      } else {
        $this->response->notFound();
      } // if
    } // pay


    /**
     * Show PDF version of the quote
     */
    function pdf() {
      if($this->active_invoice->isLoaded()) {

        require_once INVOICING_MODULE_PATH . '/models/InvoicePDFGenerator.class.php';
        InvoicePDFGenerator::download($this->active_invoice, lang(':invoice_id.pdf', array('invoice_id' => $this->active_invoice->getName())));
        die();
      } else {
        $this->response->notFound();
      } // if
    } // pdf

  }