<?php

  /**
   * Directory could not be created
   *
   * @package angie.library.errors
   */
  class DirectoryNotWritableError extends Error {
    
    /**
     * Construct the DirectoryNotWritableError
     *
     * @param string $directory_path
     * @param string $message
     */
    function __construct($directory_path, $message = null) {
      if(is_null($message)) {
        $message = "Directory '$directory_path' is not writable";
      } // if
      
      parent::__construct($message, array(
        'directory' => $directory_path, 
      ));
    } // __construct
  
  } // DirectoryCreateError