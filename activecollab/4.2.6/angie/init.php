<?php

  /**
   * Initialize Angie
   * 
   * @package angie
   */
  
  if(defined('ANGIE_INITED') && ANGIE_INITED) {
    return;
  } // if

  define('ANGIE_INITED', true);

  // Environment path is used by many environment classes. If not
  // defined do it now
  if(!defined('ANGIE_PATH')) {
    define('ANGIE_PATH', dirname(__FILE__));
  } // if

  // ---------------------------------------------------
  //  Check PHP compatibility
  // ---------------------------------------------------
  
  if(version_compare(PHP_VERSION, '5.3.3', '<')) {
    header("HTTP/1.1 503 Service Unavailable");
    print '<h3>Service Unavailable</h3>';
    print '<p>' . APPLICATION_NAME .' requires PHP 5.3.3 to work. This system runs an older version (PHP ' . PHP_VERSION . ')</p>';
    die();
  } // if
  
  // ---------------------------------------------------
  //  Low level maintenance mode message
  // ---------------------------------------------------
  
  if(defined('MAINTENANCE_MESSAGE') && MAINTENANCE_MESSAGE && !defined('IGNORE_MAINTENANCE_MESSAGE')) {
    header("HTTP/1.1 503 Service Unavailable");
    print '<h3>Service Unavailable</h3>';
    print '<p>Info: ' . MAINTENANCE_MESSAGE . '</p>';
    print '<p>&copy;' . date('Y');
    if(!LICENSE_COPYRIGHT_REMOVED) {
      print '. Powered by <a href="http://www.activecollab.com" title="activeCollab - Project Management and Collaboration Tool">activeCollab</a>';
    } // if
    print '.</p>';
    die();
  } // if

  // ---------------------------------------------------
  //  Patch REQUEST_URI on IIS
  // ---------------------------------------------------

  if(php_sapi_name() != 'cli' && !isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'], 1);

    if(isset($_SERVER['QUERY_STRING'])) {
      $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
    } // if
  } // if

  // ---------------------------------------------------
  //  Redirect in case of different ROOT_URL
  // ---------------------------------------------------

  // In case of web server requests make sure that requested comes from the same domain and same protocol
  // as defined in ROOT_URL constant. If not, assemble url with right domain and protocol and redirect the request
  // to it. This solves a lot of bugs related to the cross-domain ajax and cookie issues
  if (php_sapi_name() != 'cli' && FORCE_ROOT_URL) {
    // get requested host and scheme
    $request_url = strtolower($_SERVER['HTTP_HOST'] ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']);
    if (strpos($request_url, ':') !== false) {
      $parsed_request_url = parse_url($request_url);
      $requested_host = isset($parsed_request_url['host']) ? $parsed_request_url['host'] : null;
      $requested_port = isset($parsed_request_url['port']) ? $parsed_request_url['port'] : 80;
    } else {
      $requested_host = $request_url;
      $requested_port = 80;
    }

    $requested_scheme = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) || (isset($_SERVER['HTTP_X_REAL_PORT']) && $_SERVER['HTTP_X_REAL_PORT'] == 443)) ? 'https' : 'http';

    // get the configured host and
    $parsed_root_url = parse_url(ROOT_URL);
    $configured_host = isset($parsed_root_url['host']) ? strtolower($parsed_root_url['host']) : null;
    $configured_port = isset($parsed_root_url['port']) ? strtolower($parsed_root_url['port']) : 80;
    $configured_scheme = isset($parsed_root_url['scheme']) ? strtolower($parsed_root_url['scheme']) : null;

    if ($requested_host != $configured_host || $requested_scheme != $configured_scheme) {
      // get the request uri
      $request_uri = $_SERVER['REQUEST_URI'];
      // make sure it begins with slash
      if (substr($request_uri, 0, 1) != '/') {
        $request_uri .= '/' . $request_uri;
      } // if
      // assemble redirect url
      $redirect_url = $configured_scheme . '://' . $configured_host . $request_uri;
      // do the redirection
      header("HTTP/1.1 301 Moved Permanently");
      header("Location: $redirect_url");
      die();
    } // if
  } // if

  // ---------------------------------------------------
  //  Prepare PHP
  // ---------------------------------------------------
  
  define('CAN_USE_ZIP', extension_loaded('zlib'));

  if(php_sapi_name() == 'cli') {
    set_time_limit(0); // Make sure that all CLI commands go without execution limit
  } // if
  
  // ---------------------------------------------------
  //  Prepare application env and auto-loader
  // ---------------------------------------------------
  
  require_once ANGIE_PATH . '/classes/application/init.php';
  
  // ---------------------------------------------------
  //  Functions and constants
  // ---------------------------------------------------
  
  require_once ANGIE_PATH . '/classes/IDescribe.class.php';
  require_once ANGIE_PATH . '/classes/json/IJSON.class.php';
  
  require_once ANGIE_PATH . '/classes/Error.class.php';
  require_once ANGIE_PATH . '/classes/errors/AutoloadError.class.php';
  require_once ANGIE_PATH . '/classes/errors/SetForAutoloadError.class.php';

  require_once ANGIE_PATH . '/constants.php';
  
  require_once ANGIE_PATH . '/functions/general.php';
  require_once ANGIE_PATH . '/functions/errors.php';
  require_once ANGIE_PATH . '/functions/files.php';
  require_once ANGIE_PATH . '/functions/utf.php';
  require_once ANGIE_PATH . '/functions/web.php';
  
  // Debug
  if(AngieApplication::isInDevelopment() || AngieApplication::isInDebugMode()) {
    require_once ANGIE_PATH . '/vendor/benchmark/init.php';
    BenchmarkForAngie::start();
  } // if
  
  AngieApplication::setForAutoload(array(
  
    // Classes
    'XmlEncoder' => ANGIE_PATH . '/classes/XmlEncoder.class.php', 
    'Urlify' => ANGIE_PATH . '/classes/Urlify.class.php',

    // Errors
    'FileDnxError' => ANGIE_PATH . '/classes/errors/FileDnxError.class.php', 
    'FileCreateError' => ANGIE_PATH . '/classes/errors/FileCreateError.class.php', 
    'FileCopyError' => ANGIE_PATH . '/classes/errors/FileCopyError.class.php', 
    'FileDeleteError' => ANGIE_PATH . '/classes/errors/FileDeleteError.class.php', 
    'DirectoryCreateError' => ANGIE_PATH . '/classes/errors/DirectoryCreateError.class.php',
    'DirectoryDeleteError' => ANGIE_PATH . '/classes/errors/DirectoryDeleteError.class.php', 
    'DirectoryNotWritableError' => ANGIE_PATH . '/classes/errors/DirectoryNotWritableError.class.php', 
    'InvalidParamError' => ANGIE_PATH . '/classes/errors/InvalidParamError.class.php', 
    'InvalidInstanceError' => ANGIE_PATH . '/classes/errors/InvalidInstanceError.class.php', 
    'NotImplementedError' => ANGIE_PATH . '/classes/errors/NotImplementedError.class.php', 
    'PhpExtensionDnxError' => ANGIE_PATH . '/classes/errors/PhpExtensionDnxError.class.php', 
    'ClassNotImplementedError' => ANGIE_PATH . '/classes/errors/ClassNotImplementedError.class.php',
  
  	'AssembleURLError' => ANGIE_PATH . '/classes/router/errors/AssembleURLError.class.php', 
  	'RouteNotDefinedError' => ANGIE_PATH . '/classes/router/errors/RouteNotDefinedError.class.php', 
  	'RoutingError' => ANGIE_PATH . '/classes/router/errors/RoutingError.class.php',

    'Color' => ANGIE_PATH . '/classes/color/Color.class.php',
    'ColorUtil' => ANGIE_PATH . '/classes/color/ColorUtil.class.php'
  ));
  
  // Classes
  require_once ANGIE_PATH . '/classes/Inflector.class.php';
  require_once ANGIE_PATH . '/classes/Flash.class.php';
  require_once ANGIE_PATH . '/classes/Pager.class.php';
  require_once ANGIE_PATH . '/classes/html/HTML.class.php';
  require_once ANGIE_PATH . '/classes/Cookies.class.php';
  require_once ANGIE_PATH . '/classes/NamedList.class.php';
  require_once ANGIE_PATH . '/classes/EventsManager.class.php';
  require_once ANGIE_PATH . '/classes/IDescribe.class.php';
  require_once ANGIE_PATH . '/classes/IReadOnly.class.php';

  require_once ANGIE_PATH . '/classes/router/Router.class.php';
  require_once ANGIE_PATH . '/classes/router/Route.class.php';
  require_once ANGIE_PATH . '/classes/router/IRoutingContext.class.php';
  
  require_once ANGIE_PATH . '/classes/captcha/Captcha.class.php';

    
  // Libraries
  require_once ANGIE_PATH . '/classes/html/init.php';
  require_once ANGIE_PATH . '/classes/logger/init.php';
  require_once ANGIE_PATH . '/classes/controller/init.php';
  require_once ANGIE_PATH . '/classes/database/init.php';
  require_once ANGIE_PATH . '/classes/datetime/init.php';
  require_once ANGIE_PATH . '/classes/globalization/init.php';
  require_once ANGIE_PATH . '/classes/json/init.php';
  require_once ANGIE_PATH . '/classes/mailboxmanager/init.php';
  
  // Vendor
  require_once ANGIE_PATH . '/vendor/htmlpurifier/init.php';
  require_once ANGIE_PATH . '/vendor/swiftmailer/init.php';
  require_once ANGIE_PATH . '/vendor/smarty/init.php';
  require_once ANGIE_PATH . '/vendor/stash/init.php';
  require_once ANGIE_PATH . '/vendor/vcard/init.php';
	require_once ANGIE_PATH . '/vendor/hyperlight/init.php';
	require_once ANGIE_PATH . '/vendor/simplehtmldom/init.php';
  require_once ANGIE_PATH . '/vendor/horde_diff/init.php';