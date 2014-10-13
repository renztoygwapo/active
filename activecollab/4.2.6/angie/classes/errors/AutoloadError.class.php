<?php

  /**
   * Autoload error implementation
   *
   * @package angie.library.errors
   */
  class AutoloadError extends Error {
    
    /**
     * Construct autoload error instance
     *
     * @param string $class
     * @param mixed $all_classes
     * @param string $message
     */
    function __construct($class, $all_classes = null, $message = null) {
      if(empty($message)) {
        $message = "Failed to load class '$class'";
      } // if
      
      $map = '';
      if($all_classes) {
        foreach($all_classes as $k => $v) {
          $map .= "$k @ $v\n";
        } // foreach
      } // if
      
      parent::__construct($message, array(
        'class' => $class, 
        'autoload_map' => $map, 
      ));
    } // __construct
    
  } //AutoloadError