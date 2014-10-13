<?php

  /**
   * Not implemented error
   * 
   * @package angie.library.errors
   */
  class NotImplementedError extends Error {
  
    /**
     * Constructor
     *
     * @param string $method
     * @param string $message
     * @return NotImplementedError
     */
    function __construct($method, $message = null) {
      if($message === null) {
        $message = "You are trying to use a method that is not implemented - $method()";
      } // if
      
      parent::__construct($message, array(
        'method' => $method, 
      ));
    } // __construct
  
  }