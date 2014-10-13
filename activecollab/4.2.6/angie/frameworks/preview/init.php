<?php

  /**
   * Preview framework initialization file
   *
   * @package angie.frameworks.preview
   */
  
  const PREVIEW_FRAMEWORK = 'preview';
  const PREVIEW_FRAMEWORK_PATH = __DIR__;

  if(!defined('PREVIEW_FRAMEWORK_INJECT_INTO')) {
    define('PREVIEW_FRAMEWORK_INJECT_INTO', 'system');
  } // if
  
  AngieApplication::setForAutoload(array(
    'IPreview' => PREVIEW_FRAMEWORK_PATH . '/models/IPreview.class.php', 
    'IPreviewImplementation' => PREVIEW_FRAMEWORK_PATH . '/models/IPreviewImplementation.class.php', 
    
    'FwThumbnails' => PREVIEW_FRAMEWORK_PATH . '/models/FwThumbnails.class.php', 
  ));