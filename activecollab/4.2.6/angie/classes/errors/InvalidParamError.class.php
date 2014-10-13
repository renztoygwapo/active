<?php

  /**
   * Invalid param error
   * 
   * @package angie.library.errors
   */
  class InvalidParamError extends Error {
  
    /**
     * Construct the InvalidParamError
     * 
     * @param string $var_name Variable name
     * @param string $var_value Variable value that broke the code
     * @param string $message
     */
    function __construct($var_name, $var_value, $message = null) {
      if($message === null) {
        $message = "$$var_name is not valid param value";
      } // if
      
      parent::__construct($message, array(
        'var_name' => $var_name, 
        'var_value' => $var_value, 
      ));
    } // __construct
  
  }