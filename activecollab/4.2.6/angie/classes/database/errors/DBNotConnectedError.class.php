<?php

  /**
   * Not connected to database error
   *
   * @package angie.library.database
   * @subpackage errors
   */
  class DBNotConnectedError extends Error {
    
    /**
     * Construct not connected error
     *
     * @param string $message
     */
    function __construct($message = null) {
      if(empty($message)) {
        $message = 'Not connected to database';
      } // if
      
      parent::__construct($message);
    } // __construct
    
  }