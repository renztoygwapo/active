<?php

  /**
   * Tracking module on_rebuild_activity_log_actions event handler implementation
   * 
   * @package activeCollab.modules.tracking
   * @subpackage handlers
   */

  /**
   * Handle on_rebuild_activity_log_actions event
   * 
   * @param array $actions
   */
  function tracking_handle_on_rebuild_activity_log_actions(&$actions) {
    $actions[Router::assemble('activity_logs_admin_rebuild_tracking')] = lang('Rebuild time and expense log entries');
  } // tracking_handle_on_rebuild_activity_log_actions