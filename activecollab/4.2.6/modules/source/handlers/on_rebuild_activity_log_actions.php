<?php

  /**
   * Source module on_rebuild_activity_log_actions event handler implementation
   * 
   * @package activeCollab.modules.source
   * @subpackage handlers
   */

  /**
   * Handle on_rebuild_activity_log_actions event
   * 
   * @param array $actions
   */
  function source_handle_on_rebuild_activity_log_actions(&$actions) {
    $actions[Router::assemble('activity_logs_admin_rebuild_source')] = lang('Rebuild source log entries');
  } // source_handle_on_rebuild_activity_log_actions