<?php

  /**
   * Unknown configuration option definition
   *
   * @package angie.library.config_options
   * @subpackage errors
   */
  class ConfigOptionDnxError extends Error {
    
    /**
     * Thrown when $name configuration option does not exist
     *
     * @param string $name
     * @param string $message
     * @return ConfigOptionDnxError
     */
    function __construct($name, $message = null) {
      if(empty($message)) {
        $message = "Configuration option '$name' does not exist";
      } // if
      
      parent::__construct($message, array(
        'option_name' => $name,
      ));
    } // __construct
    
  }

?>