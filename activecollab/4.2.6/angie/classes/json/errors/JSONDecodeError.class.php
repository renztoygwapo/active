<?php

  /**
   * JSON decode error
   * 
   * @package angie.library.json
   */
  class JSONDecodeError extends Error {
    
    /**
     * Value that we failed to decode
     *
     * @var string
     */
    protected $json;
    
    /**
     * JSON error code
     *
     * @var integer
     */
    protected $error_code;
    
    /**
     * Construct error
     * 
     * @param string $json
     * @param integer $decode_error_code
     * @param string $message
     */
    function __construct($json, $decode_error_code, $message = null) {
      if($message === null) {
        switch($decode_error_code) {
          case JSON_ERROR_DEPTH:
            $message = 'The maximum stack depth has been exceeded';
            break;
          case JSON_ERROR_CTRL_CHAR:
            $message = 'Control character error, possibly incorrectly encoded';
            break;
          case JSON_ERROR_SYNTAX:
            $message = 'Syntax error';
            break;
          default:
            $message = 'Unknown JSON decode error';
        } // switch
      } // if
      
      return parent::__construct($message, array(
        'json' => $json,
        'decode_error_code' => $decode_error_code, 
      ));
    } // __construct
    
  }