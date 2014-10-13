<?php

  /**
   * Documents module on_rebuild_activity_log_actions event handler implementation
   * 
   * @package activeCollab.modules.documents
   * @subpackage handlers
   */

  /**
   * Handle on_rebuild_activity_log_actions event
   * 
   * @param array $actions
   */
  function documents_handle_on_rebuild_activity_log_actions(&$actions) {
    $actions[Router::assemble('activity_logs_admin_rebuild_documents')] = lang('Rebuild global documents log entries');
  } // documents_handle_on_rebuild_activity_log_actions