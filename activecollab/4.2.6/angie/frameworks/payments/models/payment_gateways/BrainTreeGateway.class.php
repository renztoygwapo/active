<?php

  require_once PAYMENTS_FRAMEWORK_PATH . '/lib/braintree/Braintree.php';
  
  /**
   * Braint Tree gateway class
   * 
   * @package angie.framework.payments
   * @subpackage models
   */
  class BrainTreeGateway extends PaymentGateway {

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
          throw new Error('The BrainTree library requires the ' . $ext . ' extension.');
        } //if
      }//foreach
      return true;
    } //canBeUsed

    /**
     * Return payment method string
     *
     */
    function getMethodString() {
      return 'Online Payment (BrainTree)';
    }//getMethodString


    /**
     * Return gateway name
     * 
     * @return string
     */
    function getGatewayName() {
      return 'BrainTree Gateway';
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
     * Set supported currencies from additional params
     *
     * @return array
     */
    function getSupportedCurrencies() {
      $supported = $this->getMerchantAccountIds();
      if(is_foreachable($supported)) {
        foreach($supported as $code => $merchant_account_id) {
          if(trim($merchant_account_id) != '') {
            $tmp[$code] = Currencies::findByCode($code)->getName();
          } //if
        } //foreach
      } //if
      return $this->supported_currencies = $tmp;
    } //setSupportedCurrencies

    /**
     * Render gateway form
     *
     * @param IUser $user
     * @return string
     */
    function renderOptions(IUser $user) {
      $smarty =& SmartyForAngie::getInstance();

      $smarty->assign(array(
       	'currency_code_map' => Currencies::getIdCodeMap()
       ));
      $form = $smarty->fetch(get_view_path('/braintree/_braintree_gateway_form','fw_payment_gateways_admin', PAYMENTS_FRAMEWORK));
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
       $form = $smarty->fetch(get_view_path('/payment_forms/_braintree_form','fw_payments',PAYMENTS_FRAMEWORK));
       return $form;
    }//renderPaymentForm


    /**
     * Get payment gateway merchant_key
     */
    function getMerchantKey() {
      return $this->getAdditionalProperty('merchant_key');
    } //getMerchantKey

    /**
     * Set payment gateway merchant_key
     *
     * @param $value
     */
    function setMerchantKey($value) {
      $this->setAdditionalProperty('merchant_key',$value);
    } //setMerchantKey

    /**
     * Get payment gateway public_key
     */
    function getPublicKey() {
      return $this->getAdditionalProperty('public_key');
    } //getPublicKey

    /**
     * Set payment gateway public_key
     *
     * @param $value
     */
    function setPublicKey($value) {
      $this->setAdditionalProperty('public_key',$value);
    } //setPublicKey

    /**
     * Get payment gateway private_key
     */
    function getPrivateKey() {
      return $this->getAdditionalProperty('private_key');
    } //getPrivateKey

    /**
     * Set payment gateway private_key
     *
     * @param $value
     */
    function setPrivateKey($value) {
      $this->setAdditionalProperty('private_key',$value);
    } //setPrivateKey

     /**
     * Get payment gateway merchant_account_ids
     */
    function getMerchantAccountIds() {
      return $this->getAdditionalProperty('merchant_account_ids');
    } //getMerchantAccountIds

    /**
     * Set payment gateway merchant_account_ids
     *
     * @param $value
     */
    function setMerchantAccountIds($value) {
      $this->setAdditionalProperty('merchant_account_ids',$value);
    } //setMerchantAccountIds

    /**
     * Payment gateway icon path
     * 
     * @var string
     */
    var $icon_path = "payment-gateways/braintree-payment.png";

    /**
     * Construct payment gateway object
     */
    function __construct() {
      $this->payment_gateway_type = BRAINTREE_PAYMENT;
    } //__construct

    /**
     * Do payment
     *
     * @param $payment_data
     * @param Currency $currency
     * @param Invoice $invoice
     * @return PaypalDirectPayment
     */
    function makePayment($payment_data, Currency  $currency, Invoice $invoice = null) {
      $enviroment = $this->getGoLive() == 1 ? 'production' : 'sandbox';
      Braintree_Configuration::environment($enviroment);
      Braintree_Configuration::merchantId($this->getMerchantKey());
      Braintree_Configuration::publicKey($this->getPublicKey());
      Braintree_Configuration::privateKey($this->getPrivateKey());

      $merchant_account_ids = $this->getMerchantAccountIds();
      $merchant_account_id = $merchant_account_ids[$invoice->getCurrencyCode()];
      if(!$merchant_account_id) {
        throw new Error("Your BrainTree account doesn't have merchant account that support payments in " . $invoice->getCurrencyCode() . " or you didn't define it in a
        Administrator->Payments Settings page.");
      } //if

      $params = array(
        'amount' => $this->prepareAmount($payment_data['amount']),
        'merchantAccountId' => $merchant_account_id,
        'creditCard' => array(
          'number' => $payment_data['credit_card_number'],
          'expirationMonth' => $payment_data['cc_expiration_month'],
          'expirationYear'  => $payment_data['cc_expiration_year'],
          'cvv' => $payment_data['cc_cvc_number'] ? $payment_data['cc_cvc_number'] : '',
          'cardholderName' => $payment_data['name'] ? $payment_data['name'] : '',
        ),
        'billing' => array(
          'firstName' => $payment_data['first_name'],
          'lastName' => $payment_data['last_name'],
          'streetAddress' => $payment_data['address_line1'],
          'locality' => $payment_data['city'],
          'region' => $payment_data['state'],
          'postalCode' => $payment_data['zip'],
          'countryCodeAlpha2' => $payment_data['country'],
        ),
        'options' => array(
          'submitForSettlement' => true
        )
      );
      try {
        $response = Braintree_Transaction::sale($params);
        return new BrainTreePayment($response, $this);
      } catch(Exception $e) {
        if($e->getMessage()) {
          $error = $e->getMessage();
        } else {
          $error = 'Something went wrong with calling BrainTree service, please check your Braintree gateway settings.';
        } //if
        throw new Error($error);
      } //if

    } //makePayment

    /**
     * Prepare amount - return amount in cents
     *
     * @param $amount
     * @return mix
     */
    function prepareAmount($amount) {
      return round_up($amount);
    } //prepareAmount

	
  } //StripeGateway