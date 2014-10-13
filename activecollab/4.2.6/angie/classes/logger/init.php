<?php

  /**
   * Initialize logger library
   *
   * @package angie.library.logger
   */
  
  define('LOGGER_LIB_PATH', ANGIE_PATH . '/classes/logger');
  
  require LOGGER_LIB_PATH . '/Logger.class.php';
  
  /**
   * Handle PHP error
   *
   * @param integer $errno
   * @param integer $errstr
   * @param string $errfile
   * @param integer $errline
   * @return null
   */
  function angie_error_handler($errno, $errstr, $errfile, $errline) {
    if($errno == 2048) {
      return; // Kill E_STRICT... Yeah, yeah, they have the best intentions...
    } // if
    
    if(!defined('E_STRICT')) {
      define('E_STRICT', 2048);
    } // if
    if(!defined('E_RECOVERABLE_ERROR')) {
      define('E_RECOVERABLE_ERROR', 4096);
    } // if
    
    $error_types = array (
      E_ERROR              => 'Error',
      E_WARNING            => 'Warning',
      E_PARSE              => 'Parsing Error',
      E_NOTICE             => 'Notice',
      E_CORE_ERROR         => 'Core Error',
      E_CORE_WARNING       => 'Core Warning',
      E_COMPILE_ERROR      => 'Compile Error',
      E_COMPILE_WARNING    => 'Compile Warning',
      E_USER_ERROR         => 'User Error',
      E_USER_WARNING       => 'User Warning',
      E_USER_NOTICE        => 'User Notice',
      E_STRICT             => 'Runtime Notice',
      E_RECOVERABLE_ERROR  => 'Catchable Fatal Error'
    ); // array
    
    $error_type = isset($error_types[$errno]) ? $error_types[$errno] : 'Unknown error';
    
    switch ($errno) {
      case E_USER_ERROR:
        Logger::log("[$error_type] $errstr (in $errfile on $errline)", Logger::ERROR);
        break;
      case E_USER_WARNING:
        Logger::log("[$error_type] $errstr (in $errfile on $errline)", Logger::WARNING);
        break;
      case E_USER_NOTICE:
        Logger::log("[$errno] $errstr (in $errfile on $errline)", Logger::INFO);
        break;
      default:
        Logger::log("[$error_type] $errstr (in $errfile on $errline)", Logger::ERROR);
        break;
    } // switch
  } // angie_error_handler
  set_error_handler('angie_error_handler');