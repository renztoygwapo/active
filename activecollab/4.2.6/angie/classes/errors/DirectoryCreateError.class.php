<?php

  /**
   * Directory could not be created
   *
   * @package angie.library.errors
   */
  class DirectoryCreateError extends Error {
    
    /**
     * Construct the DirectoryCreateError
     *
     * @param string $directory_path
     * @param string $message
     */
    function __construct($directory_path, $message = null) {
      if(is_null($message)) {
        $message = "Directory '$directory_path' could not be created";
      } // if
      
      parent::__construct($message, array(
        'directory' => $directory_path, 
      ));
    } // __construct
  
  }