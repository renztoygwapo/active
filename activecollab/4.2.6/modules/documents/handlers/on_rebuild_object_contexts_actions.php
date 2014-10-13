<?php

  /**
   * on_rebuild_object_contexts_actions event handler
   * 
   * @package activeCollab.modules.documents
   * @subpackage handlers
   */

  /**
   * Handle on_rebuild_object_contexts_actions event
   * 
   * @param array $actions
   */
  function documents_handle_on_rebuild_object_contexts_actions(&$actions) {
    $actions[Router::assemble('object_contexts_admin_rebuild_documents')] = lang('Rebuild documents object contexts');
  } // documents_handle_on_rebuild_object_contexts_actions