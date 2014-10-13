<?php

  /**
   * Subscriptions framework initialization file
   *
   * @package angie.frameworks.subscriptions
   */
  
  const SUBSCRIPTIONS_FRAMEWORK = 'subscriptions';
  const SUBSCRIPTIONS_FRAMEWORK_PATH = __DIR__;
  
  // By default, inject subscriptions framework in system module
  defined('SUBSCRIPTIONS_FRAMEWORK_INJECT_INTO') or define('SUBSCRIPTIONS_FRAMEWORK_INJECT_INTO', 'system');
  
  AngieApplication::setForAutoload(array(
    'ISubscriptions' => SUBSCRIPTIONS_FRAMEWORK_PATH . '/models/ISubscriptions.class.php', 
    'ISubscriptionsImplementation' => SUBSCRIPTIONS_FRAMEWORK_PATH . '/models/ISubscriptionsImplementation.class.php', 
    
    'FwSubscription' => SUBSCRIPTIONS_FRAMEWORK_PATH . '/models/subscriptions/FwSubscription.class.php', 
    'FwSubscriptions' => SUBSCRIPTIONS_FRAMEWORK_PATH . '/models/subscriptions/FwSubscriptions.class.php',

  	'SubscribeInspectorIndicator' => SUBSCRIPTIONS_FRAMEWORK_PATH . '/models/SubscribeInspectorIndicator.class.php'
  ));