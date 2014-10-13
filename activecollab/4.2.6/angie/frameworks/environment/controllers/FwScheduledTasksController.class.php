<?php

  // Build on top of frontend controller
  AngieApplication::useController('frontend', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Framework level scheduled tasks controller
   * 
   * @package angie.frameworks.environment
   * @subpackage controllers
   */
  abstract class FwScheduledTasksController extends FrontendController {
  
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();

      if (AngieApplication::isOnDemand()) {
        Logger::log("Executing scheduled tasks via https is not allowed when instance is in OnDemand mode", Logger::ERROR);
        $this->response->notFound();
      } // if
      
      if(defined('PROTECT_SCHEDULED_TASKS') && PROTECT_SCHEDULED_TASKS) {
        $code = $this->request->get('code');
        if(empty($code) || strtoupper($code) != strtoupper(substr(APPLICATION_UNIQUE_KEY, 0, 5))) {
          $this->response->forbidden();
        } // if
      } // if
      
      set_time_limit(0);
    } // __construct
    
    /**
     * Trigger frequently event
     */
    function frequently() {
      $this->renderText('Frequently event started on ' . strftime(FORMAT_DATETIME) . '.<br />' ,false, false);
      
      EventsManager::trigger('on_frequently');
      ConfigOptions::setValue('last_frequently_activity', time());
      
      $this->renderText('Frequently event finished on ' . strftime(FORMAT_DATETIME) . '.');
    } // frequently
    
    /**
     * Trigger hourly tasks
     */
    function hourly() {
      $this->renderText('Hourly event started on ' . strftime(FORMAT_DATETIME) . '.<br />' ,false, false);
      
    	EventsManager::trigger('on_hourly');
    	ConfigOptions::setValue('last_hourly_activity', time());
    	
    	$this->renderText('Hourly event finished on ' . strftime(FORMAT_DATETIME) . '.');
    } // hourly
    
    /**
     * Trigger daily tasks
     */
    function daily() {
      $this->renderText('Daily event started on ' . strftime(FORMAT_DATETIME) . '.<br />' ,false, false);
      
    	EventsManager::trigger('on_daily');
    	ConfigOptions::setValue('last_daily_activity', time());
    	
    	$this->renderText('Daily event finished on ' . strftime(FORMAT_DATETIME) . '.');
    } // daily
    
  }