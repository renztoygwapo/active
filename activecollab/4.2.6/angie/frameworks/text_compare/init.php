<?php

  /**
   * Text compare framework initialization file
   *
   * @package angie.framework.text_compare
   */
  
  const TEXT_COMPARE_FRAMEWORK = 'text_compare';
  const TEXT_COMPARE_FRAMEWORK_PATH = __DIR__;
  
  // Inject text compare framework in system module by default
  defined('TEXT_COMPARE_FRAMEWORK_INJECT_INTO') or define('TEXT_COMPARE_FRAMEWORK_INJECT_INTO', 'system');