<?php

  /**
   * Database connection error
   *
   * @package angie.library.database
   * @subpackage errors
   */
  class DBConnectError extends Error {
  
    /**
     * Construct the DBConnectError
     * 
     * @param string $host
     * @param string $user
     * @param string $pass
     * @param string $database
     * @param string $message
     */
    function __construct($host, $user, $pass, $database, $message = null) {
      if(is_null($message)) {
        $message = 'Failed to connect to database';
      } // if
      
      parent::__construct($message, array(
        'host' => $host, 
        'user' => $user, 
        'password' => $pass ? make_string(strlen($pass), '*') : '',
        'database_name' => $database, 
      ));
    } // __construct
  
  }