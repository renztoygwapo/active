<?php

  /**
   * Route public request to appropriate handler
   * 
   * @package activeCollab.instance
   */

  define('CONFIG_PATH', realpath(dirname(__FILE__) . '/../config'));
  define('PUBLIC_PATH', realpath(dirname(__FILE__)));
  
  if(is_file(CONFIG_PATH . '/config.php')) {
    define('PROXY_HANDLER_REQUEST', true);
    
    require_once CONFIG_PATH . '/config.php';
    require_once ANGIE_PATH . '/frameworks/environment/resources/proxy.php';
  } else {
    header("HTTP/1.0 404 Not Found");
  } // if
  
  die();