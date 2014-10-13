<?php

  /**
   * Download framework initialization file
   *
   * @package angie.frameworks.download
   */
  
  const DOWNLOAD_FRAMEWORK = 'download';
  const DOWNLOAD_FRAMEWORK_PATH = __DIR__;

  defined('DOWNLOAD_FRAMEWORK_INJECT_INTO') or define('DOWNLOAD_FRAMEWORK_INJECT_INTO', 'system'); // Inject download framework into specified module
  
  const DOWNLOAD_PREVIEW_IMAGE = 'image';
  const DOWNLOAD_PREVIEW_VIDEO = 'video';
  const DOWNLOAD_PREVIEW_FLASH = 'flash';
  const DOWNLOAD_PREVIEW_AUDIO = 'audio';
  
  AngieApplication::setForAutoload(array(
    'IDownload' => DOWNLOAD_FRAMEWORK_PATH . '/models/IDownload.class.php', 
    'IDownloadImplementation' => DOWNLOAD_FRAMEWORK_PATH . '/models/IDownloadImplementation.class.php', 
    'IDownloadPreviewImplementation' => DOWNLOAD_FRAMEWORK_PATH . '/models/IDownloadPreviewImplementation.class.php', 
  ));