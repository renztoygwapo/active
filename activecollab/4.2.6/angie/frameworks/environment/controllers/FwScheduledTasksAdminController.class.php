<?php

  // Build on top of admin controller
  AngieApplication::useController('admin', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Scheduled tasks administration controller
   * 
   * @package angie.frameworks.environment
   * @subpackage controllers
   */
  abstract class FwScheduledTasksAdminController extends AdminController {

    /**
     * Execute before any action
     */
    function __before() {
      if (AngieApplication::isOnDemand()) {
        $this->response->badRequest();
      } // if

      parent::__before();
      $this->wireframe->javascriptAssign(array(
        'check_application_version_url' => AngieApplication::getCheckForUpdatesUrl(),
      ));
    } // __before
  
    /**
     * Show scheduled tasks configuration page
     */
    function index() {
      $options = ConfigOptions::getValue(array(
        'last_frequently_activity',
        'last_hourly_activity',
        'last_daily_activity'
      ));

      $tasks = array(
        'frequently' => array(
          'text' => lang('Frequently'),
          'last_activity' => $options['last_frequently_activity'] ? new DateTimeValue((integer) $options['last_frequently_activity']) : null,
        ),
        'hourly' => array(
          'text' => lang('Hourly'),
          'last_activity' => $options['last_hourly_activity'] ? new DateTimeValue((integer) $options['last_hourly_activity']) : null,
        ),
        'daily' => array(
          'text' => lang('Daily'),
          'last_activity' => $options['last_daily_activity'] ? new DateTimeValue((integer) $options['last_daily_activity']) : null,
        ),
      );

      EventsManager::trigger('on_available_scheduled_tasks', array(&$tasks));

      $this->response->assign('scheduled_tasks', $tasks);

//      $options = ConfigOptions::getValue(array(
//        'last_frequently_activity',
//        'last_hourly_activity',
//        'last_daily_activity'
//      ));
//
//      $values = array(
//    	  'last_frequently_activity' => (integer) $options['last_frequently_activity'],
//    	  'last_hourly_activity'     => (integer) $options['last_hourly_activity'],
//    	  'last_daily_activity'      => (integer) $options['last_daily_activity'],
//    	);
//
//    	// Convert non-NULL values into date time value objects
//    	foreach($values as $k => $v) {
//    	  if($v) {
//    	    $values[$k] = new DateTimeValue($v);
//    	  } // if
//    	} // foreach
//
//    	$this->response->assign($values);
    } // index
    
  }