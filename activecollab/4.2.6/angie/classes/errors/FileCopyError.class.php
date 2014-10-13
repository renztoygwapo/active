<?php

  /**
   * File copy error
   *
   * @package angie.library.errors
   */
  class FileCopyError extends Error {
    
    /**
     * Construct the FileDnxError
     *
     * @param string $from
     * @param string $to
     * @param string $message
     */
    function __construct($from, $to, $message = null) {
      if(is_null($message)) {
        $message = "Failed to copy file from '$from' to '$to'";
      } // if
      
      parent::__construct($message, array(
        'from' => $from, 
        'to' => $to, 
      ));
    } // __construct
    
  }

?>