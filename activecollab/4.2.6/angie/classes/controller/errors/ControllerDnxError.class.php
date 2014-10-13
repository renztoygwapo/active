<?php

  /**
   * Controller does not exist error, thrown when controller is missing
   * 
   * @package angie.library.controller
   * @subpackage errors
   */
  class ControllerDnxError extends Error {
  
    /**
     * Construct the ControllerDnxError
     *
     * @access public
     * @param void
     * @return ControllerDnxError
     */
    function __construct($controller, $message = null) {
      if(empty($message)) {
        $message = "Controller '$controller' is missing";
      } // if
      
      parent::__construct($message, array(
        'controller' => $controller,
      ));
    } // __construct
    
  } //ControllerDnxError