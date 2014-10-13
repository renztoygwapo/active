<?php

  /**
   * Discussions module on_rebuild_activity_log_actions event handler implementation
   * 
   * @package activeCollab.modules.discussions
   * @subpackage handlers
   */

  /**
   * Handle on_rebuild_activity_log_actions event
   * 
   * @param array $actions
   */
  function discussions_handle_on_rebuild_activity_log_actions(&$actions) {
    $actions[Router::assemble('activity_logs_admin_rebuild_discussions')] = lang('Rebuild discussion log entries');
  } // discussions_handle_on_rebuild_activity_log_actions