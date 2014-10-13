<?php

  /**
   * Invoice based on task helper implementation
   * 
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  class IInvoiceBasedOnMilestoneImplementation extends IInvoiceBasedOnTrackedDataImplementation {

    /**
     * Query tracking records
     *
     * This function returns three elements: array of time records, array of expenses and project
     *
     * @param IUser $user
     * @return array
     */
    function queryRecords(IUser $user = null) {
      return array(
        TimeRecords::findByMilestone($user, $this->object, BILLABLE_STATUS_BILLABLE),
        Expenses::findByMilestone($user, $this->object, BILLABLE_STATUS_BILLABLE),
        $this->object->getProject(),
      );
    } // queryRecords
    
  }