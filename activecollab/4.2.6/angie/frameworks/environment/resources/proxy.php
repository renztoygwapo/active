<?php

  /**
   * Route public request to appropriate handler
   */

  // Make sure that request is routed through /instance/proxy.php
  if(defined('PROXY_HANDLER_REQUEST') && PROXY_HANDLER_REQUEST) {
    if(isset($_GET['proxy'])) {
      $proxy_name = $_GET['proxy'] ? trim($_GET['proxy']) : null;
      unset($_GET['proxy']);
    } else {
      $proxy_name = null;
    } // if
    
    if(isset($_GET['module'])) {
      $module = $_GET['module'] ? trim($_GET['module']) : null;
      unset($_GET['module']);
    } else {
      $module = null;
    } // if
        // Validate input
    if(($proxy_name && preg_match('/\W/', $proxy_name) == 0) && ($module && preg_match('/\W/', $module) == 0)) {
      $proxy_class = str_replace(' ', '', ucwords(str_replace('_', ' ', $proxy_name))) . 'Proxy';

      require_once ANGIE_PATH . '/classes/ProxyRequestHandler.class.php';
    
      $possible_paths = array(
        APPLICATION_PATH . "/modules/$module/proxies/$proxy_class.class.php",  
        ANGIE_PATH . "/frameworks/$module/proxies/$proxy_class.class.php",  
        CUSTOM_PATH . "/modules/$module/proxies/$proxy_class.class.php",  
      );
      
      foreach($possible_paths as $possible_path) {
        if(is_file($possible_path)) {
          require_once $possible_path;
          
          if(class_exists($proxy_class)) {
            @session_start();
            
            $proxy = new $proxy_class($_GET);
            if($proxy instanceof ProxyRequestHandler) {
              $proxy->execute();
              die();
            } // if
          } // if
        } // if
      } // foreach
    } // if
  } // if
  
  // Invalid call or no handler found
  header("HTTP/1.0 404 Not Found");
  die();