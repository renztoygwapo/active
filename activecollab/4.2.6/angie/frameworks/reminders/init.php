<?php

  /**
   * Reminders framework initlization file
   *
   * @package angie.frameworks.reminders
   */

  const REMINDERS_FRAMEWORK = 'reminders';
  const REMINDERS_FRAMEWORK_PATH = __DIR__;
  
  // Inject into specified module
  defined('REMINDERS_FRAMEWORK_INJECT_INTO') or define('REMINDERS_FRAMEWORK_INJECT_INTO', 'system');
  
  AngieApplication::setForAutoload(array(
    'IReminders' => REMINDERS_FRAMEWORK_PATH . '/models/IReminders.class.php', 
    'IRemindersImplementation' => REMINDERS_FRAMEWORK_PATH . '/models/IRemindersImplementation.class.php',
    'IUserRemindersImplementation' => REMINDERS_FRAMEWORK_PATH . '/models/IUserRemindersImplementation.class.php',

    'FwReminder' => REMINDERS_FRAMEWORK_PATH . '/models/reminders/FwReminder.class.php',
    'FwReminders' => REMINDERS_FRAMEWORK_PATH . '/models/reminders/FwReminders.class.php',

    'RemindersHomescreenWidget' => REMINDERS_FRAMEWORK_PATH . '/models/homescreen_widgets/RemindersHomescreenWidget.class.php',

    // Notifications
    'FwBaseReminderNotification' => REMINDERS_FRAMEWORK_PATH . '/notifications/FwBaseReminderNotification.class.php',
    'FwNudgeNotification' => REMINDERS_FRAMEWORK_PATH . '/notifications/FwNudgeNotification.class.php',
    'FwRemindNotification' => REMINDERS_FRAMEWORK_PATH . '/notifications/FwRemindNotification.class.php',
    'FwRemindSelfNotification' => REMINDERS_FRAMEWORK_PATH . '/notifications/FwRemindSelfNotification.class.php',
  ));

  DataObjectPool::registerTypeLoader('Reminder', function($ids) {
    return Reminders::findByIds($ids);
  });