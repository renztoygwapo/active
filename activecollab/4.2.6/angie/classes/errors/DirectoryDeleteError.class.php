<?php

  /**
   * Directory could not be deleted
   *
   * @package angie.library.errors
   */
  class DirectoryDeleteError extends Error {
    
    /**
     * Construct the DirectoryDeleteError
     *
     * @param string $directory_path
     * @param string $message
     */
    function __construct($directory_path, $message = null) {
      if(is_null($message)) {
        $message = "Directory '$directory_path' could not be deleted";
      } // if
      
      parent::__construct($message, array(
        'directory' => $directory_path, 
      ));
    } // __construct
  
  } // DirectoryDeleteError