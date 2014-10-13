<?php

  /**
   * Error that's thrown when required PHP extension is not loaded
   * Enter description here ...
   * 
   * @package angie
   * @subpackage errors
   */
  class PhpExtensionDnxError extends Error {
  
    /**
     * Construct error instance 
     * @param string $extension
     * @param string $message
     */
    function __construct($extension, $message = null) {
      if(empty($message)) {
        $message = "'$extension' not loaded";
      } // if
      
      parent::__construct($message, array(
        'extension' => $extension, 
      ));
    } // __construct
    
  }