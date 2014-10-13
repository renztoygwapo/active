<?php

  /**
   * on_rebuild_object_contexts_actions event handler
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage handlers
   */

  /**
   * Handle on_rebuild_object_contexts_actions event
   * 
   * @param array $actions
   */
  function invoicing_handle_on_rebuild_object_contexts_actions(&$actions) {
    $actions[Router::assemble('object_contexts_admin_rebuild_invoicing')] = lang('Rebuild invoicing object contexts');
  } // invoicing_handle_on_rebuild_object_contexts_actions