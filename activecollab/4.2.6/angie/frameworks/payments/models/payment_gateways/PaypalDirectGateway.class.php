<?php
  /**
   * Paypal direct payment class
   * 
   * @package angie.framework.payments
   * @subpackage models
   */
  class PaypalDirectGateway extends PaypalGateway {
    
    /**
     * Return gateway name
     * 
     * @return string
     */
    function getGatewayName() {
      return 'Paypal Direct Payment ' . lang('Gateway');
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
      $additional = $this->getSupportedCurrenciesTable();

      $smarty->assign(array(
       	'additional_info' => $additional
       ));
      $form = $smarty->fetch(get_view_path('/paypal/_direct_gateway_form','fw_payment_gateways_admin',PAYMENTS_FRAMEWORK));
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
       $form = $smarty->fetch(get_view_path('/payment_forms/_paypal_direct_form','fw_payments',PAYMENTS_FRAMEWORK));
       return $form;
    }//renderPaymentForm
    
    
    /**
     * Payment gateway icon path
     * 
     * @var string
     */
    var $icon_path = "payment-gateways/paypal-checkout.png";
    
    /**
     * Field needed for http request maped with user form fields
     * 
     * @var array
     */
    private $request_fields = array(
      'CREDITCARDTYPE' => 'credit_card_type',
      'EXPDATE' => 'cc_expiration_date',
      'ACCT' => 'credit_card_number',
      'CVV2' => 'cc_cvv2_number',
      'FIRSTNAME' => 'first_name',
      'LASTNAME' => 'last_name',
      'STREET' => 'address1',
      'CITY' => 'city',
      'STATE' => 'state',
      'ZIP' => 'zip',
      'COUNTRYCODE' => 'country',
     );
     
    /**
     * Construct paypal direct payment object
     */
    function __construct() {
      $this->payment_gateway_type = PAYPAL_DIRECT_PAYMENT;
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
      $payment_type = urlencode('Sale');
      $amount = urlencode(round_up($payment_data['amount']));
      $currency = urlencode($currency->getCode());
      $nvp_string = "&PAYMENTACTION=$payment_type&AMT=$amount&CURRENCYCODE=$currency";
      $nvp_string .= $this->makeNVPString($payment_data);
      $response = $this->callService(PaypalGateway::PAYPAL_DIRECT_PAYMENT_METHOD,$nvp_string);
      $payment = new PaypalDirectPayment($response,$this);
      return $payment;
    } //makePayment

    /**
     * Return NVP string from array
     *
     * @param $data
     * @return bool|string
     */
    function makeNVPString($data) {
      if(!is_foreachable($data)) {
        return false;
      } //if
      foreach($this->request_fields as $name => $value) {
        if($name == 'EXPDATE') {
          $data[$value] = $data['cc_expiration_month'].$data['cc_expiration_year'];
        } //if
          $nvp_string .= '&' . $name . '=' . urlencode($data[$value]);
      } //foreach
      return $nvp_string;
    }//makeNVPString
    
	
  } //PayPalDirectPayment