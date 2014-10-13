<?php

  /**
   * on_all_indices event handler implementation
   * 
   * @package angie.frameworks.activity_logs
   * @subpackage handlers
   */

  /**
   * Handle on_all_indices event
   * 
   * @param array $indices
   */
  function activity_logs_handle_on_all_indices(&$indices) {
    $indices[] = array(
      'name' => lang('Activity Logs'), 
      'description' => lang('Cached various system activities that are later used to display activity logs to users'), 
      'icon' => AngieApplication::getImageUrl('activity-logs-index.png', ACTIVITY_LOGS_FRAMEWORK),
      'size' => ActivityLogs::calculateSize(), 
      'rebuild_url' => Router::assemble('activity_logs_admin_rebuild'),
    );
  } // activity_logs_handle_on_all_indices