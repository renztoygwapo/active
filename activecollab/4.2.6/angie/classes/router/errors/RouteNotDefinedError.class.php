<?php

  /**
   * Route not defined error
   *
   * @package angie.library.router
   * @subpackage errors
   */
  class RouteNotDefinedError extends Error {
    
    /**
     * Construct route not defined error instance
     *
     * @param string $name
     * @param string $message
     * @return RouteNotDefinedError
     */
    function __construct($name, $message = null) {
      if($message === null) {
        $message = "Route '$name' is not defined";
      } // if
      
      parent::__construct($message, array(
        'name' => $name, 
      ));
    } // __construct
    
  }