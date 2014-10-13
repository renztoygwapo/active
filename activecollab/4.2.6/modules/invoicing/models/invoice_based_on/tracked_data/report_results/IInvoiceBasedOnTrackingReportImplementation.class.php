<?php

  /**
   * Invoice based on tracking report helper implementation
   * 
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  class IInvoiceBasedOnTrackingReportImplementation extends IInvoiceBasedOnTrackingReportResultImplementation {

    /**
     * Return report result
     *
     * @return TrackingReport
     */
    function prepareReport() {
      $this->object->setGroupBy(TrackingReport::DONT_GROUP);
      $this->object->setSumByUser(false);

      return $this->object;
    } // prepareReport
    
  }