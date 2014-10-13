<?php

  /**
   * API subscription error
   * 
   * @package angie.frameworks.authentication
   * @subpackage models
   */
  class ApiClientSubscriptionError extends Error {
    
    /**
     * Subscription error code
     *
     * @var integer
     */
    private $subscription_error_code;
  
    /**
     * Construct API subscription error
     * 
     * @param integer $code
     * @param string $message
     */
    function __construct($code, $message = null) {
      switch($code) {
        case ApiClientSubscriptions::ERROR_CLIENT_NOT_SET:
          $message = 'Client information missing'; break;
        case ApiClientSubscriptions::ERROR_USER_DOES_NOT_EXIST:
          $message = 'User does not exist'; break;
        case ApiClientSubscriptions::ERROR_INVALID_PASSWORD:
          $message = 'Invalid password'; break;
        case ApiClientSubscriptions::ERROR_NOT_ALLOWED:
          $message = 'API subscriptions not allowed for this user'; break;
        default:
          $code = ApiClientSubscriptions::ERROR_OPERATION_FAILED;
          $message = 'Operation failed';
      } // switch
      
      $this->subscription_error_code = $code;
      
      parent::__construct($message, array(
        'code' => $code, 
      ));
    } // __construct
    
    /**
     * Return subscription error code
     * 
     * @return integer
     */
    function getSubscriptionErrorCode() {
      return $this->subscription_error_code;
    } // getSubscriptionErrorCode
    
  }