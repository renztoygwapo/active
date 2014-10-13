<?php

  /**
   * Class for making custom payment
   * 
   * @package angie.frameworks.payments
   * @subpackage models.payments_gateways
   */
  class CustomPaymentGateway {
  
    /**
     * Payment gateway icon path
     * 
     * @var string
     */
    var $icon_path = "payment-gateways/custom-payment.png";
  
    /**
     * Return gateway name
     * 
     * @return string
     */
    function getGatewayName() {
      return lang('Custom Payment');
    } // getName
    
    /**
     * Return gateway description
     * 
     * @return string
     */
    function getGatewayDescription() {
      return lang('');
    } // getDescription
    
    /**
     * Render gateway form 
     * 
     * @param $user
     */
    function renderOptions(IUser $user) {
      $smarty =& SmartyForAngie::getInstance();
      //$form = $smarty->fetch(get_view_path('/paypal/_direct_gateway_form','fw_payment_gateways_admin',PAYMENTS_FRAMEWORK));
      //return $form;
    }//renderOptions
    
    /**
     * Return payment form
     * 
     * @param $user
     */
    function renderPaymentForm(IUser $user) {
       $smarty =& SmartyForAngie::getInstance();
       $smarty->assign(array(
       	'payment_gateway' => $this
       ));
       $form = $smarty->fetch(get_view_path('/payment_forms/_custom_payment_form','fw_payments',PAYMENTS_FRAMEWORK));
       return $form;
    }//renderPaymentForm
    
    /**
     * Get payment gateway icon path
     */
    function getIconPath() {
      return $this->icon_path;
    } //getIconPath
    
     /**
     * Do Payment
     * 
     * @param payment data array
     * 
     * @return CustomPayment
     */
    function makePayment($payment_data = null, Currency  $currency = null, $invoice = null) {
      $payment = new CustomPayment($this);
      return $payment;
    } //makePayment
    
  }