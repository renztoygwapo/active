<?php

  /**
   * Activity logs framework initialization file
   *
   * @package angie.frameworks.activity_logs
   */

  const ACTIVITY_LOGS_FRAMEWORK = 'activity_logs';
  const ACTIVITY_LOGS_FRAMEWORK_PATH = __DIR__;
  
  defined('ACTIVITY_LOGS_FRAMEWORK_INJECT_INTO') or define('ACTIVITY_LOGS_FRAMEWORK_INJECT_INTO', 'system');
  defined('ACTIVITY_LOGS_FRAMEWORK_ADMIN_ROUTE_BASE') or define('ACTIVITY_LOGS_FRAMEWORK_ADMIN_ROUTE_BASE', 'admin');
  
  AngieApplication::setForAutoload(array(
    'FwActivityLog' => ACTIVITY_LOGS_FRAMEWORK_PATH . '/models/activity_logs/FwActivityLog.class.php', 
    'FwActivityLogs' => ACTIVITY_LOGS_FRAMEWORK_PATH . '/models/activity_logs/FwActivityLogs.class.php', 
    
    'IActivityLogs' => ACTIVITY_LOGS_FRAMEWORK_PATH . '/models/IActivityLogs.class.php', 
    'IActivityLogsImplementation' => ACTIVITY_LOGS_FRAMEWORK_PATH . '/models/IActivityLogsImplementation.class.php',
   
    'RecentActivitiesHomescreenWidget' => ACTIVITY_LOGS_FRAMEWORK_PATH . '/models/homescreen_widgets/RecentActivitiesHomescreenWidget.class.php',

    // JavaScript callbacks
    'ParentActivityLogCallback' => ACTIVITY_LOGS_FRAMEWORK_PATH . '/models/javascript_callbacks/ParentActivityLogCallback.class.php', 
    'ParentCreatedActivityLogCallback' => ACTIVITY_LOGS_FRAMEWORK_PATH . '/models/javascript_callbacks/ParentCreatedActivityLogCallback.class.php', 
    'ParentCompletedActivityLogCallback' => ACTIVITY_LOGS_FRAMEWORK_PATH . '/models/javascript_callbacks/ParentCompletedActivityLogCallback.class.php', 
    'ParentReopenedActivityLogCallback' => ACTIVITY_LOGS_FRAMEWORK_PATH . '/models/javascript_callbacks/ParentReopenedActivityLogCallback.class.php', 
    'ParentMovedToArchiveActivityLogCallback' => ACTIVITY_LOGS_FRAMEWORK_PATH . '/models/javascript_callbacks/ParentMovedToArchiveActivityLogCallback.class.php', 
    'ParentMovedToTrashActivityLogCallback' => ACTIVITY_LOGS_FRAMEWORK_PATH . '/models/javascript_callbacks/ParentMovedToTrashActivityLogCallback.class.php', 
    'ParentRestoredFromArchiveActivityLogCallback' => ACTIVITY_LOGS_FRAMEWORK_PATH . '/models/javascript_callbacks/ParentRestoredFromArchiveActivityLogCallback.class.php', 
    'ParentRestoredFromTrashActivityLogCallback' => ACTIVITY_LOGS_FRAMEWORK_PATH . '/models/javascript_callbacks/ParentRestoredFromTrashActivityLogCallback.class.php',
  ));