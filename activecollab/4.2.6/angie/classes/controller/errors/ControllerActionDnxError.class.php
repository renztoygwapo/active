<?php

  /**
   * Controller action does not exist error
   * 
   * @package angie.library.controller
   * @subpackage errors
   */
  class ControllerActionDnxError extends Error {
  
    /**
     * Constructor
     *
     * @param string $controller
     * @param string $action
     * @param string $message
     */
    function __construct($controller, $action, $message = null) {
      if(empty($message)) {
        $message = "Invalid controller action $controller::$action()";
      } // if
      
      parent::__construct($message, array(
        'controller' => $controller, 
        'action' => $action, 
      ));
    } // __construct
  
  } //ControllerActionDnxError