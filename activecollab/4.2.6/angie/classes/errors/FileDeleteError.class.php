<?php

  /**
   * File delete error
   *
   * @package angie.library.errors
   */
  class FileDeleteError extends Error {
    
    /**
     * Construct the FileDeleteError
     *
     * @param mixed $files
     * @param string $message
     */
    function __construct($file, $message = null) {
    	
      if(is_null($message)) {
      	if (is_foreachable($file)) {
        	$message = "Failed to delete following files: " . implode(', ', $file);
      	} else {
      		$message = "Failed to delete following file: {$file}";
      	} // if
      } // if
      
      parent::__construct($message, array(
        'files' => $file
      ));
      
    } // __construct
    
  }

?>