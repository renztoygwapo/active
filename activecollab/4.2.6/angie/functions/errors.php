<?php

  /**
   * Error related functions
   *
   * @package angie.functions
   */

  /**
   * Check if specific variable is error object
   *
   * @param mixed $var Variable that need to be checked
   * @return boolean
   */
  function is_error($var) {
    return $var instanceof Error;
  } // is_error
  
  /**
   * Show nice error output
   *
   * @param Error $error
   * @param boolean $die
   */
  function dump_error($error, $die = true) {
    static $css_rendered = false;
    
    if(!headers_sent()) {
      header("HTTP/1.1 409 Conflict (error dump)");
    } // if
    
    if($error instanceof Error || $error instanceof Exception) {
      include ANGIE_PATH . '/templates/dump_error.php';
    } else {
      print '$error is not valid <i>Error</i> instance!';
    } // if
    
    if($die) {
      die();
    } // if
  } // dump_error