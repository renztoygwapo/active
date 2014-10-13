<?php

  /**
   * Authorize AIM model
   * 
   * @package angie.framework.payments
   * @subpackage models
   *
   */
  class AuthorizeAimGateway extends AuthorizeGateway {

    /**
     * Return gateway name
     * 
     * @return string
     */
    function getGatewayName() {
      return 'Authorize Aim ' . lang('Gateway');
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
      $additional = $this->getSupportedCurrenciesTable();
      $smarty->assign(array(
       	'additional_info' => $additional
       ));
      $form = $smarty->fetch(get_view_path('/authorize_net/_gateway_form','fw_payment_gateways_admin',PAYMENTS_FRAMEWORK));
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
       $form = $smarty->fetch(get_view_path('/payment_forms/_authorize_aim_form','fw_payments',PAYMENTS_FRAMEWORK));
       return $form;
    }//renderPaymentForm
   
    /**
     * Payment gateway icon path
     * 
     * @var string
     */
    var $icon_path = "payment-gateways/authorize.png";
    
    /**
     * Field needed for http request maped with user form fields
     * 
     * @var array
     */
    private $request_fields = array(
      'x_card_num' => 'credit_card_number',
      'x_exp_date' => 'cc_expiration_date',
      'x_first_name' => 'first_name',
      'x_last_name' => 'last_name',
      'x_address' => 'address1',
      'x_state' => 'state',
      'x_zip' => 'zip',
     );
    
    /**
     * Construct paypal direct payment object
     */
    function __construct() {
      $this->payment_gateway_type = AUTHORIZE_AIM;
    } //__construct
    
    /**
     * Make Authorize.net payment
     * 
     * @param payment data array
     * @param $currency Currency
     */
    function makePayment($payment_data, Currency  $currency, $invoice = null) {
      $amount = urlencode($payment_data['amount']);
      $currency = urlencode($currency->getCode());
      $nvp_string = "&x_amount=$amount";
      $nvp_string .= $this->makeNVPString($payment_data);
      $response = $this->callService($nvp_string);
      $payment = new AuthorizeNetPayment($response,$this);
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
	    if($name == 'x_exp_date') {
	      //$data[$value] = $data['cc_expiration_month'] . $data['cc_expiration_year'];
	       $data[$value] = '012015';
	    } //if
        $nvp_string .= '&' . $name . '=' . urlencode($data[$value]);
	  } //foreach 
	  return $nvp_string;
	}//makeNVPString
	
	
    
  } //AuthorizeAIM