<?php

  /**
   * General database error
   *
   * @package angie.library.database
   * @subpackage errors
   */
  class DBError extends Error {
    
    /**
     * Construct the DBQueryError
     *
     * @param integer $error_number
     * @param string $error_message
     * @param string $message
     * @return DBQueryError
     */
    function __construct($error_number, $error_message, $message = null) {
      if($message === null) {
        $message = "Problem with database interaction. Database said: '$error_message'";
      } // if
      
      parent::__construct($message, array(
        'error_number' => $error_number,
        'error_message' => $error_message,
      ));
    } // __construct
  
  }

?>