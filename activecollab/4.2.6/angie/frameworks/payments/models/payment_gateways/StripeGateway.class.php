<?php

  require_once PAYMENTS_FRAMEWORK_PATH . '/lib/stripe/Stripe.php';

  /**
   * Stripe payment class
   * 
   * @package angie.framework.payments
   * @subpackage models
   */
  class StripeGateway extends PaymentGateway {

    /**
     * Accepted CC type
     *
     * @var array
     */
    var $cc_types = array(
      array('name' => 'Visa', 'value' =>'Visa', 'attr' => array('selected' => 'selected')),
      array('name' => 'MasterCard', 'value' =>'MasterCard'),
      array('name' => 'Discover', 'value' =>'Discover'),
      array('name' => 'Amex', 'value' =>'Amex'),
    );

    /**
     * Accepted currencies
     *
     * @var array
     */
    var $supported_currencies = 'all';


    /**
     * Check for necessery extension like curl
     *
     * @return mixed
     */
    function checkEnvironment() {
      $requiredExtensions = array('curl');
      foreach ($requiredExtensions AS $ext) {
        if (!extension_loaded($ext)) {
          throw new Error('The Stripe library requires the ' . $ext . ' extension.');
        } //if
      }//foreach
      return true;
    } //canBeUsed

    /**
     * Return payment method string
     *
     */
    function getMethodString() {
      return 'Online Payment (Stripe)';
    }//getMethodString


    /**
     * Return gateway name
     * 
     * @return string
     */
    function getGatewayName() {
      return 'Stripe Gateway';
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
     * @param IUser $user
     * @return string
     */
    function renderOptions(IUser $user) {
      $smarty =& SmartyForAngie::getInstance();
      //$additional = $this->getSupportedCurrenciesTable();

//      $smarty->assign(array(
//       	'additional_info' => $additional
//       ));
      $form = $smarty->fetch(get_view_path('/stripe/_stripe_gateway_form','fw_payment_gateways_admin', PAYMENTS_FRAMEWORK));
      return $form;
    }//renderOptions

    /**
     * Return payment form
     *
     * @param IUser $user
     * @return string
     */
    function renderPaymentForm(IUser $user) {
       $smarty =& SmartyForAngie::getInstance();
       $smarty->assign(array(
       	'payment_gateway' => $this
       ));
       $form = $smarty->fetch(get_view_path('/payment_forms/_stripe_form','fw_payments',PAYMENTS_FRAMEWORK));
       return $form;
    }//renderPaymentForm


    /**
     * Get payment gateway api_key
     */
    function getApiKey() {
      return $this->getAdditionalProperty('api_key');
    } //getApiKey

    /**
     * Set payment gateway api_key
     *
     * @param $value
     */
    function setApiKey($value) {
      $this->setAdditionalProperty('api_key',$value);
    } //setApiKey

    
    /**
     * Payment gateway icon path
     * 
     * @var string
     */
    var $icon_path = "payment-gateways/stripe-payment.png";

    /**
     * Construct paypal direct payment object
     */
    function __construct() {
      $this->payment_gateway_type = STRIPE_PAYMENT;
    } //__construct

    /**
     * Do payment
     *
     * @param $payment_data
     * @param Currency $currency
     * @param null $invoice
     * @return PaypalDirectPayment
     */
    function makePayment($payment_data, Currency  $currency, $invoice = null) {
      Stripe::setApiKey($this->getApiKey());
      $params = array(
        'amount' => $this->prepareAmount($payment_data['amount']),
        'currency' => $currency->getCode(),
        'card' => array(
          'number' => $payment_data['credit_card_number'],
          'exp_month' => $payment_data['cc_expiration_month'],
          'exp_year'  => $payment_data['cc_expiration_year'],
          'cvc' => $payment_data['cc_cvc_number'] ? $payment_data['cc_cvc_number'] : '',
          'name' => $payment_data['name'] ? $payment_data['name'] : '',
          'address_line1' => $payment_data['address_line1'] ? $payment_data['address_line1'] : '',
          'address_city'  => $payment_data['city'] ? $payment_data['city'] : '',
          'address_zip' => $payment_data['zip'] ? $payment_data['zip'] : '',
          'address_state' => $payment_data['state'] ? $payment_data['state'] : '',
          'address_country' => $payment_data['country'] ? $payment_data['country'] : '',
        ),
        'description' => $invoice instanceof Invoice ? $invoice->getName() : '',
      );
      try {
        $response = Stripe_Charge::create($params);
        $payment = new StripePayment($response, $this);
        return $payment;
      } catch (Exception $e) {
        throw new Error($e->getMessage());
      } //try

    } //makePayment

    /**
     * Prepare amount - return amount in cents
     *
     * @param $amount
     * @return mix
     */
    function prepareAmount($amount) {
      return round_up($amount) * 100;
    } //prepareAmount

	
  } //StripeGateway