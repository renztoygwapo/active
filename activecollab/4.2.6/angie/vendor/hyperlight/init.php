<?php

  /**
   * Hyperlight for Angie initialization file
   * 
   * @package angie.vendor.hyperlight
   */

  // Hyperlight lib path
  define('HYPERLIGHT_FOR_ANGIE_PATH', ANGIE_PATH . '/vendor/hyperlight');
  
  AngieApplication::setForAutoload(array(
    'HyperlightForAngie' => HYPERLIGHT_FOR_ANGIE_PATH . '/HyperlightForAngie.class.php',
  	'Hyperlight' => HYPERLIGHT_FOR_ANGIE_PATH . '/hyperlight/hyperlight.php',
  	'CppLanguage' => HYPERLIGHT_FOR_ANGIE_PATH . '/hyperlight/languages/cpp.php',
		'CsharpLanguage' => HYPERLIGHT_FOR_ANGIE_PATH . '/hyperlight/languages/csharp.php',
		'CssLanguage' => HYPERLIGHT_FOR_ANGIE_PATH . '/hyperlight/languages/css.php',
		'PhpLanguage' => HYPERLIGHT_FOR_ANGIE_PATH . '/hyperlight/languages/php.php',
		'PythonLanguage' => HYPERLIGHT_FOR_ANGIE_PATH . '/hyperlight/languages/python.php',
		'VbLanguage' => HYPERLIGHT_FOR_ANGIE_PATH . '/hyperlight/languages/vb.php',
		'XmlLanguage' => HYPERLIGHT_FOR_ANGIE_PATH . '/hyperlight/languages/xml.php'
  ));