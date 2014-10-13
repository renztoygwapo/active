<?php

  /**
   * Subtasks framework initialization file
   * 
   * @package angie.framework.subtasks
   */
  
  const SUBTASKS_FRAMEWORK = 'subtasks';
  const SUBTASKS_FRAMEWORK_PATH = __DIR__;
  
  // Inject subtasks framework into given module
  defined('SUBTASKS_FRAMEWORK_INJECT_INTO') or define('SUBTASKS_FRAMEWORK_INJECT_INTO', 'system');
  
  AngieApplication::setForAutoload(array(
    'FwSubtask' => SUBTASKS_FRAMEWORK_PATH . '/models/subtasks/FwSubtask.class.php', 
    'FwSubtasks' => SUBTASKS_FRAMEWORK_PATH . '/models/subtasks/FwSubtasks.class.php',
    
    'ISubtasks' => SUBTASKS_FRAMEWORK_PATH . '/models/ISubtasks.class.php', 
    'ISubtasksImplementation' => SUBTASKS_FRAMEWORK_PATH . '/models/ISubtasksImplementation.class.php',

    'ISubtaskActivityLogsImplementation' => SUBTASKS_FRAMEWORK_PATH . '/models/ISubtaskActivityLogsImplementation.class.php',
    'ISubtaskAssigneesImplementation' => SUBTASKS_FRAMEWORK_PATH . '/models/ISubtaskAssigneesImplementation.class.php',
    'ISubtaskCompleteImplementation' => SUBTASKS_FRAMEWORK_PATH . '/models/ISubtaskCompleteImplementation.class.php',
  	'ISubtaskInspectorImplementation' => SUBTASKS_FRAMEWORK_PATH . '/models/ISubtaskInspectorImplementation.class.php',

    // Notifications
  	'FwBaseSubtaskNotification' => SUBTASKS_FRAMEWORK_PATH . '/notifications/FwBaseSubtaskNotification.class.php',
  	'FwNewSubtaskNotification' => SUBTASKS_FRAMEWORK_PATH . '/notifications/FwNewSubtaskNotification.class.php',
  	'FwSubtaskCompletedNotification' => SUBTASKS_FRAMEWORK_PATH . '/notifications/FwSubtaskCompletedNotification.class.php',
  	'FwSubtaskReopenedNotification' => SUBTASKS_FRAMEWORK_PATH . '/notifications/FwSubtaskReopenedNotification.class.php',
  	'FwNotifyNewSubtaskAssigneeNotification' => SUBTASKS_FRAMEWORK_PATH . '/notifications/FwNotifyNewSubtaskAssigneeNotification.class.php',
  	'FwNotifyOldSubtaskAssigneeNotification' => SUBTASKS_FRAMEWORK_PATH . '/notifications/FwNotifyOldSubtaskAssigneeNotification.class.php',

	  // Calendar Event Context
	  'ISubtaskCalendarEventContextImplementation' => SUBTASKS_FRAMEWORK_PATH . '/models/ISubtaskCalendarEventContextImplementation.class.php',
  ));

  DataObjectPool::registerTypeLoader('Subtask', function($ids) {
    return Subtasks::findByIds($ids);
  });