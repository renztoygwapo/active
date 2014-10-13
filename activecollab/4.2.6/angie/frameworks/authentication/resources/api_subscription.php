<?php

  /**
   * Handle API subscription
   * 
   * @package angie.frameworks.authentication
   * @subpackage resources
   */

  if(defined('ANGIE_API_CALL')) {
    try {
      if(isset($_POST['api_subscription']) && is_array($_POST['api_subscription'])) {
        $key = ApiClientSubscriptions::subscribe(
          isset($_POST['api_subscription']['email']) ? $_POST['api_subscription']['email'] : '', 
          isset($_POST['api_subscription']['password']) ? $_POST['api_subscription']['password'] : '', 
          isset($_POST['api_subscription']['client_name']) ? $_POST['api_subscription']['client_name'] : '', 
          isset($_POST['api_subscription']['client_vendor']) ? $_POST['api_subscription']['client_vendor'] : '', 
          isset($_POST['api_subscription']['read_only']) && $_POST['api_subscription']['read_only']
        );
      } else {
        throw new ApiClientSubscriptionError(ApiClientSubscriptions::ERROR_CLIENT_NOT_SET);
      } // if
      
      header("HTTP/1.1 200 OK");
      header("Content-Type: text/plain; charset=UTF-8");
      
      print "API key: $key";
    } catch(Exception $e) {
      if($e instanceof ApiClientSubscriptionError && $e->getSubscriptionErrorCode() != ApiClientSubscriptions::ERROR_OPERATION_FAILED) {
        if($e->getSubscriptionErrorCode() == ApiClientSubscriptions::ERROR_NOT_ALLOWED) {
          header("HTTP/1.1 403 Forbidden");
          header("Content-Type: text/plain; charset=UTF-8");
        } else {
          header("HTTP/1.1 500 Operation Failed");
          header("Content-Type: text/plain; charset=UTF-8");
        } // if
      } else {
        header("HTTP/1.1 400 Bad Request");
        header("Content-Type: text/plain; charset=UTF-8");
      } // if
      
      print 'Error Code: ' . ($e instanceof ApiClientSubscriptionError ? $e->getSubscriptionErrorCode() : 0) . "\n";
    } // try
    
  // No go if we are not inside of the API call
  } else {
    header("HTTP/1.1 400 Bad Request");
    header("Content-Type: text/plain; charset=UTF-8");
  } // if
  
  die();