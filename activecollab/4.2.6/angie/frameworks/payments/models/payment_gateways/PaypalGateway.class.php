<?php

  /**
   * Paypal common class
   * 
   * @package angie.framework.payments
   * @subpackage models
   *
   */
  class PaypalGateway extends PaymentGateway {
    
    const PAYPAL_DIRECT_PAYMENT_METHOD = 'DoDirectPayment';
    const PAYPAL_SET_EXPRESS_CHECKOUT_METHOD = 'SetExpressCheckout';
    const PAYPAL_GET_EXPRESS_CHECKOUT_METHOD = 'GetExpressCheckoutDetails';
    const PAYPAL_DO_EXPRESS_CHECKOUT_METHOD = 'DoExpressCheckoutPayment';
    
    const ENDPOINT_URL = "https://api-3t.paypal.com/nvp";
    const TEST_URL = "https://api-3t.sandbox.paypal.com/nvp";
    const TEST_REDIRECT_URL = "https://www.sandbox.paypal.com/webscr&cmd=_express-checkout";
    const REDIRECT_URL = "https://www.paypal.com/webscr&cmd=_express-checkout";
    const API_VERSION = "56.0";

    /**
     * Check for necessery extension like curl
     *
     * @return mixed
     */
    function checkEnvironment() {
      if(!extension_loaded('curl')) {
        throw new Error('Required \'CURL\' extension isn\'t loaded. Please contact your server administrator.');
      }//if
      return true;
    } //canBeUsed

    
    /**
     * Return payment method string
     * 
     */
    function getMethodString() {
      return 'Online Payment (PayPal)';
    }//getMethodString
    
    
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
    var $supported_currencies = array(
      'USD' => 'U.S. Dollar',
      'EUR' => 'Euro',
      'AUD' => 'Australian Dollar',
      'CAD' => 'Canadian Dollar',
      'JPY' => 'Japanese Yen',
      'GBP' => 'Pound Sterling',
    );
  
    /**
       * Send HTTP POST Request
       *
       * @param	string	The API method name
       * @param	string	The POST Message fields in &name=value pair format
       * @return	array	Parsed HTTP Response body
       */
      function callService($method_name, $nvp_str) {
      	  
      	// Set up your API credentials, PayPal end point, and API version.
      	$api_username = urlencode($this->getApiUsername());
      	$api_password = urlencode($this->getApiPassword());
      	$api_signature = urlencode($this->getApiSignature());
      	if($this->getGoLive() == 1) {
      	  $api_endpoint = PaypalGateway::ENDPOINT_URL;
      	} else {
      	  $api_endpoint = PaypalGateway::TEST_URL;
      	} //if
      	$version = urlencode(PaypalGateway::API_VERSION);
      
      	// Set the curl parameters.
      	$ch = curl_init();
      	curl_setopt($ch, CURLOPT_URL, $api_endpoint);
      	curl_setopt($ch, CURLOPT_VERBOSE, 1);
      
      	// Turn off the server and peer verification (TrustManager Concept).
      	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
      
      	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      	curl_setopt($ch, CURLOPT_POST, 1);
      
      	// Set the API operation, version, and API signature in the request.
      	$nvpreq = "METHOD=$method_name&VERSION=$version&PWD=$api_password&USER=$api_username&SIGNATURE=$api_signature$nvp_str";
        
      	// Set the request as a POST FIELD for curl.
      	curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
      
      	// Get response from the server.
      	$http_response = curl_exec($ch);
        if(!$http_response) {
      		throw new InvalidParamError("$method_name failed: ".curl_error($ch).'('.curl_errno($ch).')');
      	} //if
      
      	// Extract the response details.
      	$http_response_ar = explode("&", $http_response);
        
      	$http_parsed_response_ar = array();
      	foreach ($http_response_ar as $i => $value) {
      		$tmp_ar = explode("=", $value);
      		if(sizeof($tmp_ar) > 1) {
      			$http_parsed_response_ar[$tmp_ar[0]] = urldecode($tmp_ar[1]);
      		} //if
      	} //if
        if((0 == sizeof($http_parsed_response_ar)) || !array_key_exists('ACK', $http_parsed_response_ar)) {
      		exit("Invalid HTTP Response for POST request($nvpreq) to $api_endpoint.");
      	} //if
      
      	return $http_parsed_response_ar;
      } //makePayPalPayment
    
    
    
    } //PaypalPayment