<?php

  /**
   * Initial datetime values
   *
   * @package angie.library.datetime
   */
  
  define('DATETIME_LIB_PATH', ANGIE_PATH . '/classes/datetime');
  
  require_once ANGIE_PATH . '/classes/datetime/DateValue.class.php';
  require_once ANGIE_PATH . '/classes/datetime/DateTimeValue.class.php';
  
  ini_set('date.timezone', 'GMT');
  if(function_exists('date_default_timezone_set')) {
    date_default_timezone_set('GMT');
  } else {
    @putenv('TZ=GMT'); // Don't throw a warning if system in safe mode
  } // if