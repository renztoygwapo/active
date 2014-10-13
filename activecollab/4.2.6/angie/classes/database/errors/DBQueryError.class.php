<?php

  /**
   * Database query error
   *
   * @package angie.library.database
   * @subpackage errors
   */
  class DBQueryError extends Error {
    
    /**
     * Construct the DBQueryError
     *
     * @param string $sql
     * @param integer $error_number
     * @param string $error_message
     * @param string $message
     * @return DBQueryError
     */
    function __construct($sql, $error_number, $error_message, $message = null) {
      if($message === null) {
        $message = "Query failed with message '$error_message' (SQL: $sql)";
      } // if
      
      parent::__construct($message, array(
        'sql' => $sql,
        'error_number' => $error_number,
        'error_message' => $error_message,
      ));
    } // __construct
  
  }