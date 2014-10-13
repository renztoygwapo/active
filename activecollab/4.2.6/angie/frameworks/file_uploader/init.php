<?php

  /**
   * Initalize file uploader framework
   *
   * @package angie.frameworks.file_uploader
   */

  define('FILE_UPLOADER_FRAMEWORK', 'file_uploader');
  define('FILE_UPLOADER_FRAMEWORK_PATH', ANGIE_PATH . '/frameworks/file_uploader');

  if (!defined('FILE_UPLOADER_RUNTIMES')) {
    define('FILE_UPLOADER_RUNTIMES', 'html5,html4');
  } // if
