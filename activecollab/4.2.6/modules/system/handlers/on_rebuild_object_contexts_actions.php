<?php

  /**
   * on_rebuild_object_contexts_actions event handler
   * 
   * @package activeCollab.modules.system
   * @subpackage handlers
   */

  /**
   * Handle on_rebuild_object_contexts_actions event
   * 
   * @param array $actions
   */
  function system_handle_on_rebuild_object_contexts_actions(&$actions) {
    $actions[Router::assemble('object_contexts_admin_rebuild_people')] = lang('Rebuild people contexts');
    $actions[Router::assemble('object_contexts_admin_rebuild_projects')] = lang('Rebuild project contexts');
    $actions[Router::assemble('object_contexts_admin_rebuild_milestones')] = lang('Rebuild milestone object contexts');
  } // system_handle_on_rebuild_object_contexts_actions