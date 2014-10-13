<?php

  /**
   * File create error implementation
   * 
   * @package angie.library.errors
   */
  class FileCreateError extends Error {
  
    /**
     * Construct the FileCreateError
     *
     * @param string $file_path
     * @param string $message
     */
    function __construct($file_path, $message = null) {
      if(is_null($message)) {
        $message = "File '$file_path' could not be created";
      } // if
      
      parent::__construct($message, array(
        'file_path' => $file_path, 
      ));
    } // __construct
    
  }