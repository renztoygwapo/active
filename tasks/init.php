<?php

  /**
   * Initialize system so events can be triggered
   *
   * @package activeCollab
   * @subpackage tasks
   */

  if(DIRECTORY_SEPARATOR == '\\') {
    define('PUBLIC_PATH', str_replace('\\', '/', dirname(__FILE__)));
  } else {
    define('PUBLIC_PATH', dirname(__FILE__));
  } // if
  
  // Load configuration and initialize framework
  require_once realpath(PUBLIC_PATH . '/../config/config.php');
  require_once ANGIE_PATH . '/init.php';
  
  // Protect the scripts from tools that want to execute them, but don't have 
  // the required info
  if(defined('PROTECT_SCHEDULED_TASKS') && PROTECT_SCHEDULED_TASKS) {
    $code = array_var($argv, 1);
    if(empty($code) || strtoupper($code) != strtoupper(substr(APPLICATION_UNIQUE_KEY, 0, 5))) {
      die("Error: Invalid protection code!\n\nMake sure that you provide first 5 characters of your license key after file name:\n\n  ~ php event.php #CODE#\n\n");
    } // if
  } // if
  
  set_time_limit(0);
  
  // Bootstrap and handle HTTP request
  AngieApplication::bootstrapForCommandLineRequest(null, true, true, true);