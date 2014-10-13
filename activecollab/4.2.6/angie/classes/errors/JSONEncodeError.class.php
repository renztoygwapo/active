<?php

  /**
   * JSON error
   * 
   * This error is throw when Services_JSON fails to encode specific value to 
   * JSON string
   * 
   * @package angie.library.errors
   */
  class JSONEncodeError extends Error {
  
    /**
     * Construct JSON error instance
     *
     * @param mixed $var
     * @param string $message
     */
    function __construct($var, $message = null) {
      if($message === null) {
        $message = 'Failed to encode specified value to JSON string';
      } // if
      
      parent::__construct($message, array(
        'value' => $var, 
      ));
    } // __construct
  
  }

?>