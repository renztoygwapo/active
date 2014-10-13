<?php

  /**
   * Tracked expenses home screen widget implementation
   * 
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  class TrackedExpensesHomescreenWidget extends TrackingReportHomescreenWidget {
  
    /**
     * Return widget name
     * 
     * @return string
     */
    function getName() {
      return lang('Tracked Expenses');
    } // getName
    
    /**
     * Return widget description
     * 
     * @return string
     */
    function getDescription() {
      return lang('Show tracked expenses based on given criteria');
    } // getDescription
  
    /**
     * Prepare report instance based on given criteria
     * 
     * @return TrackingReport
     */
    function getReport() {
      $report = parent::getReport();
      $report->setTypeFilter(TrackingReport::TYPE_FILTER_EXPENSES);
      return $report;
    } // getReport
    
  }