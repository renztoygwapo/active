<?php

  /**
   * Route assemble error
   * 
   * This error is thrown when we fail to assembe URL based on default values 
   * and provided data
   * 
   * @package angie.library.router
   * @subpackage errors
   */
  class AssembleURLError extends Error {
  
    /**
     * Constructor
     *
     * @param string $route_string
     * @param array $assembly_data
     * @param array $default_data
     * @param string $part
     * @param string $message
     */
    function __construct($route_string, $assembly_data, $default_data = null, $part = null, $message = null) {
      if(is_null($message)) {
        $message = "Failed to assemble '$route_string' based on provided data";
      } // if

      if($route_string == 'project') {
        var_dump($assembly_data);
      }
      
      parent::__construct($message, array(
        'route_string' => $route_string, 
        'assembly_data' => $assembly_data, 
        'default_data' => $default_data, 
        'part' => $part, 
      ));
    } // __construct
  
  }