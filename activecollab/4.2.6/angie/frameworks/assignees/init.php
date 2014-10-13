<?php

  /**
   * Assignees framework initialization file
   * 
   * @package angie.frameworks.assignees
   */
  
  const ASSIGNEES_FRAMEWORK = 'assignees';
  const ASSIGNEES_FRAMEWORK_PATH = __DIR__;
  
  // Settings
  defined('ASSIGNEES_FRAMEWORK_INJECT_INTO') or define('ASSIGNEES_FRAMEWORK_INJECT_INTO', 'system');
  
  AngieApplication::setForAutoload(array(
    'FwAssignments' => ASSIGNEES_FRAMEWORK_PATH . '/models/assignments/FwAssignments.class.php', 
    
    'IAssignees' => ASSIGNEES_FRAMEWORK_PATH . '/models/IAssignees.class.php', 
    'IAssigneesImplementation' => ASSIGNEES_FRAMEWORK_PATH . '/models/IAssigneesImplementation.class.php',
   
    'FwAssignmentLabel' => ASSIGNEES_FRAMEWORK_PATH . '/models/FwAssignmentLabel.class.php', 

  	'AssigneesInspectorWidget' => ASSIGNEES_FRAMEWORK_PATH . '/models/AssigneesInspectorWidget.class.php',
  	'AssigneesInspectorProperty' => ASSIGNEES_FRAMEWORK_PATH . '/models/AssigneesInspectorProperty.class.php',

    // Notifications
  	'FwNotifyNewAssigneeNotification' => ASSIGNEES_FRAMEWORK_PATH . '/notifications/FwNotifyNewAssigneeNotification.class.php',
  	'FwNotifyOldAssigneeNotification' => ASSIGNEES_FRAMEWORK_PATH . '/notifications/FwNotifyOldAssigneeNotification.class.php',
  ));
