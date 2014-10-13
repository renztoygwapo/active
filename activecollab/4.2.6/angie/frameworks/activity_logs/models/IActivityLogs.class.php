<?php

  /**
   * Activity logs interface
   *
   * @package angie.frameworks.activity_logs
   * @subpackage models
   */
  interface IActivityLogs {
    
    /**
     * Return activity logs implementation for this object
     *
     * @return IActivityLogsImplementation
     */
    public function activityLogs();
    
  }