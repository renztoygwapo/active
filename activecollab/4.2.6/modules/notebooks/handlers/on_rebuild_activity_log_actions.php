<?php

  /**
   * Notebooks module on_rebuild_activity_log_actions event handler implementation
   * 
   * @package activeCollab.modules.notebooks
   * @subpackage handlers
   */

  /**
   * Handle on_rebuild_activity_log_actions event
   * 
   * @param array $actions
   */
  function notebooks_handle_on_rebuild_activity_log_actions(&$actions) {
    $actions[Router::assemble('activity_logs_admin_rebuild_notebooks')] = lang('Rebuild notebook log entries');
    $actions[Router::assemble('activity_logs_admin_rebuild_notbook_pages')] = lang('Rebuild notebook page log entries');
  } // notebooks_handle_on_rebuild_activity_log_actions