<?php

  /**
   * System module on_rebuild_activity_log_actions event handler implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage handlers
   */

  /**
   * Handle on_rebuild_activity_log_actions event
   * 
   * @param array $actions
   */
  function system_handle_on_rebuild_activity_log_actions(&$actions) {
    $actions[Router::assemble('activity_logs_admin_rebuild_people')] = lang('Rebuild people log entries');
    $actions[Router::assemble('activity_logs_admin_rebuild_projects')] = lang('Rebuild project log entries');
    $actions[Router::assemble('activity_logs_admin_rebuild_milestones')] = lang('Rebuild milestone log entries');
  } // system_handle_on_rebuild_activity_log_actions