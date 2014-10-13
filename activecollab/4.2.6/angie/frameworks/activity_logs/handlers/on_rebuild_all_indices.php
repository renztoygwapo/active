<?php

  /**
   * on_rebuild_all_indices event handler implementation
   * 
   * @package angie.frameworks.activity_logs
   * @subpackage handlers
   */

  /**
   * Handle on_rebuild_all_indices event
   * 
   * @param array $steps
   * @param boolean $quick
   */
  function activity_logs_handle_on_rebuild_all_indices(&$steps, $quick) {
    if($quick) {
      return;
    } // if

    foreach(ActivityLogs::getRebuildActions() as $url => $text) {
      $steps[$url] = lang('Activity Logs') . ' / ' . $text;
    } // foreach
  } // activity_logs_handle_on_rebuild_all_indices