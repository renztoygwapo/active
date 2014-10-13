<?php

  /**
   * Instance dependent defaults file
   *
   * @package activeCollab
   */
  
  if(!defined('ENVIRONMENT_PATH')) {
    define('ENVIRONMENT_PATH', str_replace('\\', '/', realpath(dirname(__FILE__) . '/..')));
  } // if

  // Use raw, unpacked files (for development and testing)
  if(defined('USE_UNPACKED_FILES') && USE_UNPACKED_FILES) {
    require_once ROOT . '/' . APPLICATION_VERSION . '/resources/defaults.php';

  // Use production package (phar)
  } else {
    if(!in_array('phar', stream_get_wrappers())) {
      die('PHP Environment Error: Required "phar" stream wrapper is not enabled! Please enable it and refresh the page.');
    } // if

    require_once 'phar://' . ROOT . '/' . APPLICATION_VERSION . '.phar';
    require_once 'phar://ActiveCollab-' . APPLICATION_VERSION  . '.phar/resources/defaults.php';
  } // if