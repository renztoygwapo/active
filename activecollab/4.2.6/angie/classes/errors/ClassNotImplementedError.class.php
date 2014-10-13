<?php

  /**
   * Class not implemented error
   *
   * This error is thrown when we expect a certain class to be defined but we 
   * failed to find it. Optional parameter is location where we expect to find 
   * class definition
   * 
   * @package angie.library.errors
   */
  class ClassNotImplementedError extends Error {
    
    /**
     * Constructor
     * 
     * $expected_location is provided only if expected location is known
     *
     * @param string $class
     * @param string $expected_location
     * @param string $message
     */
    function __construct($class, $expected_location = null, $message = null) {
      if($message === null) {
        $message = "Class '$class' is not implemented";
        if($expected_location) {
          $message .= ". Expected location: '$expected_location'";
        } // if
      } // if
      
      parent::__construct($message, array(
        'class' => $class, 
        'location' => $expected_location, 
      ));
    } // __construct
  
  }  //ClassNotImplementedError