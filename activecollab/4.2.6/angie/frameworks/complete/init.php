<?php

  /**
   * Complete framework initialization file
   *
   * @package angie.frameworks.complete
   */

  const COMPLETE_FRAMEWORK = 'complete';
  const COMPLETE_FRAMEWORK_PATH = __DIR__;

  defined('COMPLETE_FRAMEWORK_INJECT_INTO') or define('COMPLETE_FRAMEWORK_INJECT_INTO', 'system');

  AngieApplication::setForAutoload(array(
    'IComplete' => COMPLETE_FRAMEWORK_PATH . '/models/IComplete.class.php',
    'ICompleteImplementation' => COMPLETE_FRAMEWORK_PATH . '/models/ICompleteImplementation.class.php',
    'PriorityInspectorTitlebarWidget' => COMPLETE_FRAMEWORK_PATH . '/models/PriorityInspectorTitlebarWidget.class.php',

    // Notifications
    'FwObjectCompletedNotification' => COMPLETE_FRAMEWORK_PATH . '/notifications/FwObjectCompletedNotification.class.php',
    'FwObjectReopenedNotification' => COMPLETE_FRAMEWORK_PATH . '/notifications/FwObjectReopenedNotification.class.php',
  ));