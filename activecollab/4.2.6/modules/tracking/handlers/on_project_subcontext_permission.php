<?php

  /**
   * on_project_subcontext_permission event handler implementation
   * 
   * @package activeCollab.modules.tracking
   * @subpackage handlers
   */

  /**
   * Handle on_project_subcontext_permission event
   * 
   * @param array $map
   */
  function tracking_handle_on_project_subcontext_permission(&$map) {
    $map['tracking'] = 'tracking';
  } // tracking_handle_on_project_subcontext_permission