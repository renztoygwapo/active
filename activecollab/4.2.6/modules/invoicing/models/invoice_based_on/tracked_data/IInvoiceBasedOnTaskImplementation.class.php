<?php

  /**
   * Invoice based on task helper implementation
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class IInvoiceBasedOnTaskImplementation extends IInvoiceBasedOnTrackedDataImplementation {

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
        $this->object->tracking()->getTimeRecords($user, BILLABLE_STATUS_BILLABLE),
        $this->object->tracking()->getExpenses($user, BILLABLE_STATUS_BILLABLE),
        $this->object->getProject(),
      );
    } // queryRecords
    
  }