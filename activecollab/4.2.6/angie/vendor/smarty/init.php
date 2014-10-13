<?php

  /**
   * Smarty for Angie initialization file
   * 
   * @package angie.vendor.smarty
   */

  const SMARTY_FOR_ANGIE_PATH = __DIR__; // Location of Smarty for Angie library

  AngieApplication::setForAutoload(array(
    'SmartyForAngie' => SMARTY_FOR_ANGIE_PATH . '/SmartyForAngie.class.php',
  ));
  
  require_once SMARTY_FOR_ANGIE_PATH . '/smarty/Smarty.class.php';
  
  // Register Smarty auto-loader
  AngieApplication::registerAutoloader('smartyAutoload');