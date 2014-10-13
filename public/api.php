<?php

  /**
   * Public API file
   * 
   * @package activeCollab
   */

  const ANGIE_API_CALL = true;
  
  if(DIRECTORY_SEPARATOR == '\\') {
    define('PUBLIC_PATH', str_replace('\\', '/', dirname(__FILE__)));
  } else {
    define('PUBLIC_PATH', dirname(__FILE__));
  } // if
  
  define('CONFIG_PATH', realpath(PUBLIC_PATH . '/../config'));
  
  // Bootstrap and handle HTTP request
  if(is_file(CONFIG_PATH . '/config.php')) {
    require_once CONFIG_PATH . '/config.php';
    require_once ANGIE_PATH . '/init.php';
    
    // Subscription
    if(isset($_POST['api_subscription']) && is_array($_POST['api_subscription'])) {
      AngieApplication::bootstrapForApiSubscription();
      
      require AUTHENTICATION_FRAMEWORK_PATH . '/resources/api_subscription.php';
      
    // Regular API request
    } else {
      AngieApplication::bootstrapForHttpRequest();
      AngieApplication::handleHttpRequest();
    } // if
  } else {
    header('HTTP/1.1 404 Not Found');
    print '<h1>HTTP/1.1 404 Not Found</h1>';
    die();
  } // if