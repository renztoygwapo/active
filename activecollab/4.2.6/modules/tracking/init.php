<?php

  /**
   * Init tracking module
   *
   * @package activeCollab.modules.tracking
   */
  
  const TRACKING_MODULE = 'tracking';
  const TRACKING_MODULE_PATH = __DIR__;

  // Tracking object billable status
  const BILLABLE_STATUS_NOT_BILLABLE = 0;
  const BILLABLE_STATUS_BILLABLE = 1;
  const BILLABLE_STATUS_PENDING_PAYMENT = 2;
  const BILLABLE_STATUS_PAID = 3;

  // Job type activity
  const JOB_TYPE_INACTIVE = 0;
  const JOB_TYPE_ACTIVE = 1;
  
  AngieApplication::useModel(array(
    'job_types', 
    'expense_categories', 
    'time_records', 
    'expenses', 
    'estimates',
  ), TRACKING_MODULE);
  
  AngieApplication::setForAutoload(array(
    'TrackingObject' => TRACKING_MODULE_PATH . '/models/tracking_objects/TrackingObject.class.php', 
    'TrackingObjects' => TRACKING_MODULE_PATH . '/models/tracking_objects/TrackingObjects.class.php',
   
    'ITrackingObjectActivityLogsImplementation' => TRACKING_MODULE_PATH . '/models/ITrackingObjectActivityLogsImplementation.class.php', 
    
    'ProjectTimesheet' => TRACKING_MODULE_PATH . '/models/ProjectTimesheet.class.php',
    
    'ITrackingImplementation' => TRACKING_MODULE_PATH . '/models/ITrackingImplementation.class.php',

    'TimeRecordCreatedActivityLogCallback' => TRACKING_MODULE_PATH . '/models/javascript_callbacks/TimeRecordCreatedActivityLogCallback.class.php',
    'ExpenseCreatedActivityLogCallback' => TRACKING_MODULE_PATH . '/models/javascript_callbacks/ExpenseCreatedActivityLogCallback.class.php',
  
  	'TrackingExporter' => TRACKING_MODULE_PATH . '/models/TrackingExporter.class.php', 
   
  	// Inspector
  	'ITrackingInspectorImplementation' => TRACKING_MODULE_PATH . '/models/ITrackingInspectorImplementation.class.php',
  	'TrackingInspectorWidget' => TRACKING_MODULE_PATH . '/models/TrackingInspectorWidget.class.php', 
  	'EstimateInspectorProperty' => TRACKING_MODULE_PATH . '/models/EstimateInspectorProperty.class.php',

    'TrackingReport' => TRACKING_MODULE_PATH . '/models/TrackingReport.class.php',
  	
  	// Homescreen widgets
  	'TrackingReportHomescreenWidget' => TRACKING_MODULE_PATH . '/models/homescreen_widgets/TrackingReportHomescreenWidget.class.php', 
  	'TrackedTimeHomescreenWidget' => TRACKING_MODULE_PATH . '/models/homescreen_widgets/TrackedTimeHomescreenWidget.class.php', 
  	'TrackedExpensesHomescreenWidget' => TRACKING_MODULE_PATH . '/models/homescreen_widgets/TrackedExpensesHomescreenWidget.class.php', 
  	'MyTimeHomescreenWidget' => TRACKING_MODULE_PATH . '/models/homescreen_widgets/MyTimeHomescreenWidget.class.php',
  ));
  
  DataObjectPool::registerTypeLoader('TimeRecord', function($ids) {
    return TimeRecords::findByIds($ids, STATE_TRASHED);
  });
  
  DataObjectPool::registerTypeLoader('Expense', function($ids) {
    return Expenses::findByIds($ids, STATE_TRASHED);
  });

  DataObjectPool::registerTypeLoader('JobType', function($ids) {
    return JobTypes::findByIds($ids);
  });