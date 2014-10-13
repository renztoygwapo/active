<?php

  /**
   * Routing error
   *
   * Routing error is thrown when we fail to match request string with array of 
   * defined routes
   * 
   * @package angie.library.router
   * @subpackage errors
   */
  class RoutingError extends Error {
    
    /**
    * Constructor
    *
    * @param string $request_string
    * @param string $message
    * @return Angie_Router_Error_Match
    */
    function __construct($request_string, $message = null) {
      if(is_null($message)) {
        $message = "String '$request_string' does not match any of mapped routes";
      } // if
      
      parent::__construct($message, array(
        'request_string' => $request_string, 
      ));
    } // __construct
  
  }