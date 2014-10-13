<?php

  /**
   * File does not exist exception
   *
   * @package angie.library.errors
   */
  class FileDnxError extends Error {
    
    /**
     * Construct the FileDnxError
     *
     * @param string $file_path
     * @param string $message
     */
    function __construct($file_path, $message = null) {
      if(is_null($message)) {
        $message = "File '$file_path' doesn't exists";
      } // if
      
      parent::__construct($message, array(
        'path' => $file_path, 
      ));
    } // __construct
  
  }