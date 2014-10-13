<?php

  /**
   * Invoice based on tracking report result
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  abstract class IInvoiceBasedOnTrackingReportResultImplementation extends IInvoiceBasedOnTrackedDataImplementation {

    /**
     * Return report result
     *
     * @return TrackingReport
     */
    abstract function prepareReport();

    /**
     * Query tracking records
     *
     * This function returns three elements: array of time records, array of expenses and project
     *
     * @param IUser $user
     * @return array
     * @throws Error
     */
    function queryRecords(IUser $user = null) {
      $report = $this->prepareReport();

      if($report instanceof TrackingReport) {
        $report_results = $report->run($user);

        if($report_results) {
          $time_record_ids = $expense_ids = array();

          foreach ($report_results[0]['records'] as $result) {
            if($result['billable_status'] == BILLABLE_STATUS_BILLABLE) { //use only billable
              if($result['type'] == 'TimeRecord') {
                $time_record_ids[] = $result['id'];
              } elseif($result['type'] == 'Expense') {
                $expense_ids[] = $result['id'];
              } // if
            } // if
          } // foreach

          $time_records = count($time_record_ids) ? TimeRecords::findByIds($time_record_ids) : null;
          $expenses = count($expense_ids) ? Expenses::findByIds($expense_ids) : null;

          $project_id = null;

          if($report->getProjectFilter() == TrackingReport::PROJECT_FILTER_SELECTED) {
            $project_ids = $report->getProjectIds();

            if(count($project_ids) == 1) {
              $project_id = first($project_ids);
            } // if
          } // if

          return array($time_records, $expenses, $project_id);
        } else {
          return array(null, null, null);
        } // if
      } else {
        throw new Error('Failed to prepare report');
      } // if
    } // queryRecords

  }