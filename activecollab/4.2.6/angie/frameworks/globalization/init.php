<?php

  /**
   * Globalization framework initialization file
   *
   * @package angie.frameworks.globalization
   */
  
  const GLOBALIZATION_FRAMEWORK = 'globalization';
  const GLOBALIZATION_FRAMEWORK_PATH = __DIR__;
  

  if(!defined('GLOBALIZATION_FRAMEWORK_INJECT_INTO')) {
    define('GLOBALIZATION_FRAMEWORK_INJECT_INTO', 'system'); // Inject framework into system module by default
  } // if

  if(!defined('GLOBALIZATION_ADMIN_ROUTE_BASE')) {
    define('GLOBALIZATION_ADMIN_ROUTE_BASE', 'admin'); // Route base for all globalization administration routes
  } // if
  
  // Functions
  require_once __DIR__ . '/functions.php';
  
  AngieApplication::setForAutoload(array(
    'FwCurrency' => GLOBALIZATION_FRAMEWORK_PATH . '/models/currencies/FwCurrency.class.php',
    'FwCurrencies' => GLOBALIZATION_FRAMEWORK_PATH . '/models/currencies/FwCurrencies.class.php',
   
    'FwDayOff' => GLOBALIZATION_FRAMEWORK_PATH . '/models/day_offs/FwDayOff.class.php', 
    'FwDayOffs' => GLOBALIZATION_FRAMEWORK_PATH . '/models/day_offs/FwDayOffs.class.php',
   
    'FwLanguage' => GLOBALIZATION_FRAMEWORK_PATH . '/models/languages/FwLanguage.class.php', 
    'FwLanguages' => GLOBALIZATION_FRAMEWORK_PATH . '/models/languages/FwLanguages.class.php', 
  ));

  DataObjectPool::registerTypeLoader('Language', function($ids) {
    return Languages::findByIds($ids);
  });

  DataObjectPool::registerTypeLoader('Currency', function($ids) {
    return Currencies::findByIds($ids);
  });