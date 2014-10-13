<?php
  /**
   * Authorize payment gateway common class
   * 
   * @package angie.framework.payments
   * @subpackage models
   *
   */
  class AuthorizeGateway extends PaymentGateway {
   
    const ENDPOINT_URL = "https://secure.authorize.net/gateway/transact.dll";
    const TEST_URL = "https://test.authorize.net/gateway/transact.dll";
    const API_VERSION = "31.0";

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
      return 'Online Payment (Authorize)';
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
    );
 
    /**
     * Send HTTP POST Request
     *
     * @param	string	The API method name
     * @param	string	The POST Message fields in &name=value pair format
     * @return	array	Parsed HTTP Response body
     */
    function callService($nvp_str) {

      $x_delim_data = TRUE;
      $x_delim_char = "|";
  	  $x_relay_response = "FALSE";
  	  $x_type = "AUTH_CAPTURE";
  	  $x_method = "CC";
  
      // Set up your API credentials, PayPal end point, and API version.
      $api_login_id = urlencode($this->getApiLoginId());
      $transaction_id = urlencode($this->getTransactionId());
      if($this->getGoLive() == 1) {  
        $url_endpoint = AuthorizeGateway::ENDPOINT_URL;
      } else {
        $url_endpoint = AuthorizeGateway::TEST_URL;
      } //if
      $version = urlencode(AuthorizeGateway::API_VERSION);
      $time = time();
     
      // Set the API operation, version, and API signature in the request
      $nvpreq = "x_delim_data=$x_delim_data&x_delim_char=$x_delim_char&x_fp_timestamp=$time&x_relay_response=$x_relay_response&x_type=$x_type&x_method=$x_method&x_version=$version&x_login=$api_login_id&x_tran_key=$transaction_id$nvp_str";
      
      $request = curl_init($url_endpoint); // initiate curl object
      curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
      curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
      curl_setopt($request, CURLOPT_POSTFIELDS, $nvpreq); // use HTTP POST to send form data
      curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment this line if you get no gateway response.  
      
      $post_response = curl_exec($request); // execute curl post and store results in $post_response
      
      // additional options may be required depending upon your server configuration
      if(!$post_response) {
        throw new InvalidParamError("Authorize service call failed: ".curl_error($request).'('.curl_errno($request).')');
      } //if
      
      curl_close ($request); // close curl object
      
      $http_response_ar = explode($x_delim_char,$post_response);
      $http_parsed_response_ar = array();
      $http_parsed_response_ar['response_code'] = $http_response_ar[0];
      $http_parsed_response_ar['response_reason_code'] = $http_response_ar[2];
      $http_parsed_response_ar['response_message'] = $http_response_ar[3];
      $http_parsed_response_ar['transaction_id'] = $http_response_ar[6];
      $http_parsed_response_ar['amount'] = $http_response_ar[9];
      $http_parsed_response_ar['currency_code'] = 'USD';
      $http_parsed_response_ar['tax_amount'] = $http_response_ar[32];
      
      if(0 == sizeof($http_parsed_response_ar)) {
      	exit("Invalid HTTP Response for POST request($nvpreq) to $url_endpoint.");
      } //if
      
      return $http_parsed_response_ar;
    } //callService
  
  } //AythorizePayment