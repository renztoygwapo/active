<?php

  /**
   * Show time tracked by any users in the past 30 days
   * 
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  class TrackedTimeHomescreenWidget extends TrackingReportHomescreenWidget {
    
    /**
     * Return widget name
     * 
     * @return string
     */
    function getName() {
      return lang('Tracked Time');
    } // getName
    
    /**
     * Return widget description
     * 
     * @return string
     */
    function getDescription() {
      return lang('Show tracked time based on given criteria');
    } // getDescription
  
    /**
     * Prepare report instance based on given criteria
     * 
     * @return TrackingReport
     */
    function getReport() {
      $report = parent::getReport();
      $report->setTypeFilter(TrackingReport::TYPE_FILTER_TIME);
      return $report;
    } // getReport
    
  }