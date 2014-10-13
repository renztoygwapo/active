<?php

  /**
   * Tasks module on_rebuild_activity_log_actions event handler implementation
   * 
   * @package activeCollab.modules.tasks
   * @subpackage handlers
   */

  /**
   * Handle on_rebuild_activity_log_actions event
   * 
   * @param array $actions
   */
  function tasks_handle_on_rebuild_activity_log_actions(&$actions) {
    $actions[Router::assemble('activity_logs_admin_rebuild_tasks')] = lang('Rebuild task log entries');
  } // tasks_handle_on_rebuild_activity_log_actions