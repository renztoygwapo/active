<?php

  /**
   * Invoice based on project helper implementation
   * 
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  class IInvoiceBasedOnProjectImplementation extends IInvoiceBasedOnTrackingReportResultImplementation {

    /**
     * Return report result
     *
     * @return TrackingReport
     */
    function prepareReport() {
      $report = new TrackingReport();
      $report->filterByProjects(array($this->object->getId()));
      $report->setGroupBy(TrackingReport::DONT_GROUP);
      $report->setSumByUser(false);

      return $report;
    } // prepareReport
     
  }