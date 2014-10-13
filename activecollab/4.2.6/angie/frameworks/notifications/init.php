<?php

  /**
   * Notifications framework intialization file
   *
   * @package angie.frameworks.notifications
   */

  const NOTIFICATIONS_FRAMEWORK = 'notifications';
  const NOTIFICATIONS_FRAMEWORK_PATH = __DIR__;

  defined('NOTIFICATIONS_FRAMEWORK_INJECT_INTO') or define('NOTIFICATIONS_FRAMEWORK_INJECT_INTO', 'system');

  AngieApplication::setForAutoload(array(
    'AngieNotificationsDelegate' => NOTIFICATIONS_FRAMEWORK_PATH . '/models/AngieNotificationsDelegate.class.php',

    'FwNotification' => NOTIFICATIONS_FRAMEWORK_PATH . '/models/notifications/FwNotification.class.php',
    'FwNotifications' => NOTIFICATIONS_FRAMEWORK_PATH . '/models/notifications/FwNotifications.class.php',

    'NotificationChannel' => NOTIFICATIONS_FRAMEWORK_PATH . '/models/channels/NotificationChannel.class.php',
    'WebInterfaceNotificationChannel' => NOTIFICATIONS_FRAMEWORK_PATH . '/models/channels/WebInterfaceNotificationChannel.class.php',
  ));