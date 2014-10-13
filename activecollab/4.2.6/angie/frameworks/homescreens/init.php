<?php

  /**
   * Homescreens framework initialization file
   *
   * @package angie.frameworks.homescreens
   */
  
  const HOMESCREENS_FRAMEWORK = 'homescreens';
  const HOMESCREENS_FRAMEWORK_PATH = __DIR__;

  defined('HOMESCREENS_FRAMEWORK_INJECT_INTO') or define('HOMESCREENS_FRAMEWORK_INJECT_INTO', 'system'); // Inject framework into system module by default
  defined('HOMESCREENS_ADMIN_ROUTE_BASE') or define('HOMESCREENS_ADMIN_ROUTE_BASE', 'admin'); // Route base for all globalization administration routes
  
  AngieApplication::setForAutoload(array(
    'FwHomescreens' => HOMESCREENS_FRAMEWORK_PATH . '/models/FwHomescreens.class.php',

    'IHomescreen' => HOMESCREENS_FRAMEWORK_PATH . '/models/IHomescreen.class.php',
    'IHomescreenImplementation' => HOMESCREENS_FRAMEWORK_PATH . '/models/IHomescreenImplementation.class.php',

  	'FwHomescreenTab' => HOMESCREENS_FRAMEWORK_PATH . '/models/homescreen_tabs/FwHomescreenTab.class.php', 
    'FwHomescreenTabs' => HOMESCREENS_FRAMEWORK_PATH . '/models/homescreen_tabs/FwHomescreenTabs.class.php',
  
  	'FwHomescreenWidget' => HOMESCREENS_FRAMEWORK_PATH . '/models/homescreen_widgets/FwHomescreenWidget.class.php', 
    'FwHomescreenWidgets' => HOMESCREENS_FRAMEWORK_PATH . '/models/homescreen_widgets/FwHomescreenWidgets.class.php',
  
  	'WidgetsHomescreenTab' => HOMESCREENS_FRAMEWORK_PATH . '/models/homescreen_tabs/WidgetsHomescreenTab.class.php',
  	'SplitHomescreenTab' => HOMESCREENS_FRAMEWORK_PATH . '/models/homescreen_tabs/SplitHomescreenTab.class.php',
  	'CenterHomescreenTab' => HOMESCREENS_FRAMEWORK_PATH . '/models/homescreen_tabs/CenterHomescreenTab.class.php',
  	'LeftHomescreenTab' => HOMESCREENS_FRAMEWORK_PATH . '/models/homescreen_tabs/LeftHomescreenTab.class.php',
  	'RightHomescreenTab' => HOMESCREENS_FRAMEWORK_PATH . '/models/homescreen_tabs/RightHomescreenTab.class.php',
  ));