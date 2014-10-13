<?php

  /**
   * Invoicing module on_rebuild_activity_log_actions event handler implementation
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage handlers
   */

  /**
   * Handle on_rebuild_activity_log_actions event
   * 
   * @param array $actions
   */
  function invoicing_handle_on_rebuild_activity_log_actions(&$actions) {
    $actions[Router::assemble('activity_logs_admin_rebuild_invoicing')] = lang('Rebuild invoicing log entries');
  } // invoicing_handle_on_rebuild_activity_log_actions