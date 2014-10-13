<?php

  /**
   * on_activity_log_callbacks event handler
   * 
   * @package activeCollab.modules.tracking
   * @subpackage helpers
   */

  /**
   * Handle on_activity_log_callbacks event
   * 
   * @param array $callbacks
   */
  function tracking_handle_on_activity_log_callbacks(&$callbacks) {
    $callbacks['time_record/created'] = new TimeRecordCreatedActivityLogCallback();
    $callbacks['expense/created'] = new ExpenseCreatedActivityLogCallback();
  } // tracking_handle_on_activity_log_callbacks