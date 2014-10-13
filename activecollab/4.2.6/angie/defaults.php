<?php

  /**
   * Default configuration option
   *
   * Options listed in this file can be overriden by the application via config/config.php or application level
   * defaults.php file
   *
   * @package angie
   */

  if(!defined('ROOT_URL') && php_sapi_name() == 'cli') {
    define('ROOT_URL', 'unknown'); // In case we are executing this command via CLI, for testing and initialization
  } // if

  defined('ADMIN_EMAIL') or define('ADMIN_EMAIL', false);
  defined('PUBLIC_FOLDER_NAME') or define('PUBLIC_FOLDER_NAME', 'public');
  defined('PUBLIC_AS_DOCUMENT_ROOT') or define('PUBLIC_AS_DOCUMENT_ROOT', false);
  defined('FORCE_QUERY_STRING') or define('FORCE_QUERY_STRING', true);
  defined('PATH_INFO_THROUGH_QUERY_STRING') or define('PATH_INFO_THROUGH_QUERY_STRING', true); // Force query string for hosts that does not support PATH_INFO or make problems with it
  defined('FORCE_ROOT_URL') or define('FORCE_ROOT_URL', true);

  defined('URL_BASE') or define('URL_BASE', ROOT_URL . '/index.php');
  defined('ASSETS_URL') or define('ASSETS_URL', ROOT_URL . '/assets');

  if(strpos(URL_BASE, 'index.php') === false) {
    if(PUBLIC_AS_DOCUMENT_ROOT) {
      define('UPGRADE_SCRIPT_URL', ROOT_URL . '/upgrade/index.php');
    } else {
      define('UPGRADE_SCRIPT_URL', ROOT_URL . '/public/upgrade/index.php');
    } // if
  } else {
    define('UPGRADE_SCRIPT_URL', str_replace('index.php', 'upgrade/index.php', URL_BASE));
  } // if

  if(!defined('ASSETS_PATH')) {
    defined('PUBLIC_PATH') or define('PUBLIC_PATH', realpath("../../public"));
    define('ASSETS_PATH', PUBLIC_PATH . '/assets');
  } // if

  defined('FORCE_INTERFACE') or define('FORCE_INTERFACE', false);
  defined('FORCE_DEVICE_CLASS') or define('FORCE_DEVICE_CLASS', false);
  defined('PROTECT_SCHEDULED_TASKS') or define('PROTECT_SCHEDULED_TASKS', true);
  defined('PROTECT_ASSETS_FOLDER') or define('PROTECT_ASSETS_FOLDER', false);

  defined('PURIFY_HTML') or define('PURIFY_HTML', true);
  defined('REMOVE_EMPTY_PARAGRAPHS') or define('REMOVE_EMPTY_PARAGRAPHS', true);
  defined('MAINTENANCE_MESSAGE') or define('MAINTENANCE_MESSAGE', null);
  defined('CREATE_THUMBNAILS') or define('CREATE_THUMBNAILS', true);
  defined('RESIZE_SMALLER_THAN') or define('RESIZE_SMALLER_THAN', 524288);
  defined('IMAGE_SIZE_CONSTRAINT') or define('IMAGE_SIZE_CONSTRAINT', '2240x1680');
  defined('COMPRESS_HTTP_RESPONSES') or define('COMPRESS_HTTP_RESPONSES', true);
  defined('COMPRESS_ASSET_REQUESTS') or define('COMPRESS_ASSET_REQUESTS', true);
  defined('PAGE_PLACEHOLDER') or define('PAGE_PLACEHOLDER', '-PAGE-');
  defined('NUMBER_FORMAT_DEC_SEPARATOR') or define('NUMBER_FORMAT_DEC_SEPARATOR', '.');
  defined('NUMBER_FORMAT_THOUSANDS_SEPARATOR') or define('NUMBER_FORMAT_THOUSANDS_SEPARATOR', ',');
  defined('DEFAULT_CSV_SEPARATOR') or define('DEFAULT_CSV_SEPARATOR', ',');
  defined('CACHE_PATH') or define('CACHE_PATH', ENVIRONMENT_PATH . '/cache');
  defined('COLLECTOR_CHECK_ETAG') or define('COLLECTOR_CHECK_ETAG', true);
  defined('ALLOW_JSONP') or define('ALLOW_JSONP', true);
  defined('AUTOLOAD_ALLOW_PATH_OVERRIDE') or define('AUTOLOAD_ALLOW_PATH_OVERRIDE', true);
  defined('TRACK_USER_BEHAVIOUR') or define('TRACK_USER_BEHAVIOUR', false);
  defined('CHECK_FOR_NEW_VERSION') or define('CHECK_FOR_NEW_VERSION', true);

  defined('USE_CACHE') or define('USE_CACHE', true);

  // Auto-detect cache backend
  if (!defined('CACHE_BACKEND')) {
    if (php_sapi_name() == 'cli') {
      define('CACHE_BACKEND', 'FileCacheBackend');
    } else {
      define('CACHE_BACKEND', extension_loaded('apc') && ini_get('apc.enabled') && class_exists('\APCIterator', false) ? 'APCCacheBackend' : 'FileCacheBackend');
    } // if
  } // if
  define('CACHE_LIFETIME', 172800);

  // Cookies
  define('USE_COOKIES', true);
  if(!defined('COOKIE_DOMAIN')) {
    $parts = parse_url(ROOT_URL);
    if(is_array($parts) && isset($parts['host'])) {
      define('COOKIE_DOMAIN', $parts['host']);
    } else {
      define('COOKIE_DOMAIN', '');
    } // if
  } // if

  define('COOKIE_PATH', '/');
  if(substr(ROOT_URL, 0, 5) == 'https') {
    define('COOKIE_SECURE', 1);
  } else {
    define('COOKIE_SECURE', 0);
  } // if
  define('COOKIE_PREFIX', 'ac');

  // ---------------------------------------------------
  //  MVC elements
  // ---------------------------------------------------

  defined('DEFAULT_MODULE') or define('DEFAULT_MODULE', 'system');
  defined('DEFAULT_CONTROLLER') or define('DEFAULT_CONTROLLER', 'backend');
  defined('DEFAULT_ACTION') or define('DEFAULT_ACTION', 'index');
  defined('DEFAULT_FORMAT') or define('DEFAULT_FORMAT', 'html');

  // ---------------------------------------------------
  //  Date and time froms
  // ---------------------------------------------------

  // Formats can be overriden with constants with same name that start with
  // USER_ (USER_FORMAT_DATE will override FORMAT_DATE)
  if(DIRECTORY_SEPARATOR == '\\') {
    defined('FORMAT_DATETIME') or define('FORMAT_DATETIME', '%b %#d. %Y, %I:%M %p');
    defined('FORMAT_DATE') or define('FORMAT_DATE', '%b %#d. %Y');
  } else {
    defined('FORMAT_DATETIME') or define('FORMAT_DATETIME', '%b %e. %Y, %I:%M %p');
    defined('FORMAT_DATE') or define('FORMAT_DATE', '%b %e. %Y');
  } // if

  defined('FORMAT_TIME') or define('FORMAT_TIME', '%I:%M %p');

  // ---------------------------------------------------
  //  Environment and paths
  // ---------------------------------------------------

  defined('ENVIRONMENT') or define('ENVIRONMENT', substr(ENVIRONMENT_PATH, strrpos(ENVIRONMENT_PATH, '/') + 1)); // Read environment name from environment path
  defined('COMPILE_PATH') or define('COMPILE_PATH', ENVIRONMENT_PATH . '/compile');
  defined('DEVELOPMENT_PATH') or define('DEVELOPMENT_PATH', ROOT . '/development');
  defined('UPLOAD_PATH') or define('UPLOAD_PATH', ENVIRONMENT_PATH . '/upload');
  defined('LIMIT_DISK_SPACE_USAGE') or define('LIMIT_DISK_SPACE_USAGE', null);
  defined('CUSTOM_PATH') or define('CUSTOM_PATH', ENVIRONMENT_PATH . '/custom');
  defined('IMPORT_PATH') or define('IMPORT_PATH', ENVIRONMENT_PATH . '/import');
  defined('THUMBNAILS_PATH') or define('THUMBNAILS_PATH', ENVIRONMENT_PATH . '/thumbnails');
  defined('WORK_PATH') or define('WORK_PATH', ENVIRONMENT_PATH . '/work');