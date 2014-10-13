<?php

  /**
   * Init tasks module
   *
   * @package activeCollab.modules.tasks
   */
  
  const TASKS_MODULE = 'tasks';
  const TASKS_MODULE_PATH = __DIR__;

  require_once __DIR__ . '/resources/autoload_model.php';
  
  AngieApplication::setForAutoload(array(
    'Task' => TASKS_MODULE_PATH . '/models/tasks/Task.class.php',
    'Tasks' => TASKS_MODULE_PATH . '/models/tasks/Tasks.class.php',
    
  	'TasksProjectExporter' => TASKS_MODULE_PATH . '/models/TasksProjectExporter.class.php',
  	'IncomingMailTaskAction' => TASKS_MODULE_PATH . '/models/IncomingMailTaskAction.class.php',

  	'TasksFilterHomescreenWidget' => TASKS_MODULE_PATH . '/models/homescreen_widgets/TasksFilterHomescreenWidget.class.php', 
  	'MyTasksHomescreenWidget' => TASKS_MODULE_PATH . '/models/homescreen_widgets/MyTasksHomescreenWidget.class.php', 
  	'DelegatedTasksHomescreenWidget' => TASKS_MODULE_PATH . '/models/homescreen_widgets/DelegatedTasksHomescreenWidget.class.php', 
  	'UnassignedTasksHomescreenWidget' => TASKS_MODULE_PATH . '/models/homescreen_widgets/UnassignedTasksHomescreenWidget.class.php',

    'ITaskCategoryImplementation' => TASKS_MODULE_PATH . '/models/task_categories/ITaskCategoryImplementation.class.php',
    'ITaskCommentsImplementation' => TASKS_MODULE_PATH . '/models/comments/ITaskCommentsImplementation.class.php',
    'ITaskCustomFieldsImplementation' => TASKS_MODULE_PATH . '/models/ITaskCustomFieldsImplementation.class.php',
    'ITaskSearchItemImplementation' => TASKS_MODULE_PATH . '/models/ITaskSearchItemImplementation.class.php',
    'ITaskSharingImplementation' => TASKS_MODULE_PATH . '/models/ITaskSharingImplementation.class.php',
    'ITaskStateImplementation' => TASKS_MODULE_PATH . '/models/ITaskStateImplementation.class.php',
    'IRelatedTasksImplementation' => TASKS_MODULE_PATH . '/models/IRelatedTasksImplementation.class.php',

    'TaskCategory' => TASKS_MODULE_PATH . '/models/task_categories/TaskCategory.class.php',
    'TaskComment' => TASKS_MODULE_PATH . '/models/comments/TaskComment.class.php',
  
  	'ITaskInspectorImplementation' => TASKS_MODULE_PATH . '/models/ITaskInspectorImplementation.class.php',
    'RelatedTasksInspectorProperty' => TASKS_MODULE_PATH . '/models/RelatedTasksInspectorProperty.class.php',

    'AggregatedTasksReport' => TASKS_MODULE_PATH . '/models/AggregatedTasksReport.class.php',

    'TasksAnalyzerReport' => TASKS_MODULE_PATH . '/models/TasksAnalyzerReport.class.php',
    'OpenVsCompletedTasksReport' => TASKS_MODULE_PATH . '/models/OpenVsCompletedTasksReport.class.php',
    'WeeklyCreatedTasksReport' => TASKS_MODULE_PATH . '/models/WeeklyCreatedTasksReport.class.php',
    'WeeklyCompletedTasksReport' => TASKS_MODULE_PATH . '/models/WeeklyCompletedTasksReport.class.php',

    'NewTaskNotification' => TASKS_MODULE_PATH . '/notifications/NewTaskNotification.class.php',
    'NewTaskFromFormForStaffNotification' => TASKS_MODULE_PATH . '/notifications/NewTaskFromFormForStaffNotification.class.php',
    'NewTaskFromFormForAuthorNotification' => TASKS_MODULE_PATH . '/notifications/NewTaskFromFormForAuthorNotification.class.php',

	  // Calendar Event Context
	  'ITaskCalendarEventContextImplementation' => TASKS_MODULE_PATH . '/models/ITaskCalendarEventContextImplementation.class.php',

	  'RefreshMyTasksCallback' => TASKS_MODULE_PATH . '/models/javascript_callbacks/RefreshMyTasksCallback.class.php',
  ));
  
  DataObjectPool::registerTypeLoader('Task', function($ids) {
    return Tasks::findByIds($ids, STATE_TRASHED, VISIBILITY_PRIVATE);
  });

  DataObjectPool::registerTypeLoader('TaskComment', function($ids) {
    return Comments::findByIds($ids);
  });

  DataObjectPool::registerTypeLoader('PublicTaskForm', function($ids) {
    return PublicTaskForms::findByIds($ids);
  });