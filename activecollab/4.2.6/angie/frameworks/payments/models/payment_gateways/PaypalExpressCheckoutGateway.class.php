<?php
  /**
   * Paypal express checkout payment class
   * 
   * @package angie.framework.payments
   * @subpackage models
   */
  class PaypalExpressCheckoutGateway extends PaypalGateway {

    /**
     * Accepted currencies
     *
     * @var array
     */
    var $supported_currencies = array(
      'USD' => 'U.S. Dollar',
      'EUR' => 'Euro',
      'AUD' => 'Australian Dollar',
      'CAD' => 'Canadian Dollar',
      'JPY' => 'Japanese Yen',
      'GBP' => 'Pound Sterling',
      'CZK' => 'Czech Koruna',
      'DKK' => 'Danish Krone',
      'HKD' => 'Hong Kong Dollar',
      'HUF' => 'Hungarian Forint',
      'NOK' => 'Norwegian Krone',
      'NZD' => 'New Zealand Dollar',
      'PLN' => 'Polish Zloty',
      'SGD' => 'Singapore Dollar',
      'SEK' => 'Swedish Krona',
      'CHF' => 'Swiss Franc'
    );

    /**
     * Return gateway name
     * 
     * @return string
     */
    function getGatewayName() {
      return 'Paypal Express Checkout ' . lang('Gateway');
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
     * 
     */
    function renderOptions(IUser $user, $is_payment = false) {
      $smarty =& SmartyForAngie::getInstance();
      $additional = $this->getSupportedCurrenciesTable();
      $smarty->assign(array(
       	'additional_info' => $additional
       ));
      $form = $smarty->fetch(get_view_path('/paypal/_express_checkout_gateway_form','fw_payment_gateways_admin',PAYMENTS_FRAMEWORK));
      return $form;
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
       $form = $smarty->fetch(get_view_path('/payment_forms/_paypal_express_checkout_form','fw_payments',PAYMENTS_FRAMEWORK));
       return $form;
    }//renderPaymentForm
    
   
    /**
     * Payment gateway icon path
     * 
     * @var string
     */
    var $icon_path = "payment-gateways/paypal-express.png";
    
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
     * Construct express checkout payment object
     */
    function __construct() {
      $this->payment_gateway_type = PAYPAL_EXPRESS_CHECKOUT;
    } //__construct

      
    /**
     * Return return url  
     * 
     */
    function getReturnUrl() {
      return Router::assemble('admin_paypal_express_checkout_return_url');
    } //getReturnUrl
    
    /**
     * Return cancel url
     */
    function getCancelUrl() {
      return Router::assemble('admin_paypal_express_checkout_cancel_url');
    } //getCancelUrl


    /**
     * SetExpressCheckout create payment and retrive token from paypal
     *
     * @param $payment_data
     * @param Currency $currency
     * @param Invoice $invoice
     * @return PaypalExpressCheckoutPayment
     */
    function makePayment($payment_data, Currency  $currency, Invoice $invoice = null) {
      $payment_type = urlencode('Sale');	
      $amount = urlencode(round_up($payment_data['amount']));
      $currency = urlencode($currency->getCode());
      $return_ur = $this->getReturnUrl();
      $cancel_url = urlencode($this->getCancelUrl());
      if($invoice instanceof Invoice) {
        $invoice_name = $invoice->getName();
      } //if
      $nvp_string = "&LANDINGPAGE=Billing&SOLUTIONTYPE=Sole&PAYMENTACTION=$payment_type&ReturnUrl=$return_ur&CANCELURL=$cancel_url&AMT=$amount&CURRENCYCODE=$currency&L_DESC0=$invoice_name&L_AMT0=$amount&L_QTY0=1";
      $response = $this->callService(PaypalGateway::PAYPAL_SET_EXPRESS_CHECKOUT_METHOD,$nvp_string);
      $response['AMT'] = $amount;
      $response['CURRENCYCODE'] = $currency;
      $payment = new PaypalExpressCheckoutPayment($response,$this);
      return $payment;
    } //makePayment
    
    
    /**
	 * Return NVP string from array
	 * 
	 * @return mixed
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
    
  } //PaypalExpressCheckout