<?php

  /**
   * Register autoloader error
   * 
   * @package angie.library.errors
   */
  class RegisterAutoloaderError extends Error {
  
    /**
     * Construct register autoloader error
     * 
     * @param mixed $autoloader
     */
    function __construct($autoloader, $message = null) {
      if(empty($message)) {
        $message = 'Failed to register autoloader';
      } // if
      
      parent::__construct($message, array(
        'autoloader' => is_array($autoloader) && isset($autoloader[0]) && isset($autoloader[1]) ? $autoloader[0] . '::' . $autoloader[1] : $autoloader,
      ));
    } // __construct
    
  }