<?php

  /**
   * Files module on_rebuild_activity_log_actions event handler implementation
   * 
   * @package activeCollab.modules.files
   * @subpackage handlers
   */

  /**
   * Handle on_rebuild_activity_log_actions event
   * 
   * @param array $actions
   */
  function files_handle_on_rebuild_activity_log_actions(&$actions) {
    $actions[Router::assemble('activity_logs_admin_rebuild_files')] = lang('Rebuild file log entries');
    $actions[Router::assemble('activity_logs_admin_rebuild_file_versions')] = lang('Rebuild file version log entries');
    $actions[Router::assemble('activity_logs_admin_rebuild_text_document_versions')] = lang('Rebuild text document version log entries');
  } // files_handle_on_rebuild_activity_log_actions