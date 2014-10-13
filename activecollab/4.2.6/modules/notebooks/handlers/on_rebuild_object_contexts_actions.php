<?php

  /**
   * on_rebuild_object_contexts_actions event handler
   * 
   * @package activeCollab.modules.notebooks
   * @subpackage handlers
   */

  /**
   * Handle on_rebuild_object_contexts_actions event
   * 
   * @param array $actions
   */
  function notebooks_handle_on_rebuild_object_contexts_actions(&$actions) {
    $actions[Router::assemble('object_contexts_admin_rebuild_notebooks')] = lang('Rebuild notebook object contexts');
  } // notebooks_handle_on_rebuild_object_contexts_actions