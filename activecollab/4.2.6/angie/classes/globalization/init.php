<?php

  /**
   * Globalization library initialization file
   *
   * @package angie.library.globalization
   */

  require_once ANGIE_PATH . '/classes/globalization/Globalization.class.php';
  require_once ANGIE_PATH . '/classes/globalization/GlobalizationAdapter.class.php';
  
  define('BUILT_IN_LOCALE', 'en_US.UTF-8');
  
  if(!defined('DEFAULT_LOCALE')) {
    define('DEFAULT_LOCALE', BUILT_IN_LOCALE);
  } // if
  
  setlocale(LC_ALL, DEFAULT_LOCALE);
  
  if(defined('GLOBALIZATION_ADAPTER')) {
    require_once APPLICATION_PATH . '/resources/' . GLOBALIZATION_ADAPTER . '.class.php';
    Globalization::useAdapter(GLOBALIZATION_ADAPTER);
  } else {
    require_once ANGIE_PATH . '/classes/globalization/GlobalizationAdapter.class.php';
    Globalization::useAdapter('GlobalizationAdapter');
  } // if