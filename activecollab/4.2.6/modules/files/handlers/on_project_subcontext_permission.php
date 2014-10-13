<?php

  /**
   * on_project_subcontext_permission event handler implementation
   * 
   * @package activeCollab.modules.files
   * @subpackage handlers
   */

  /**
   * Handle on_project_subcontext_permission event
   * 
   * @param array $map
   */
  function files_handle_on_project_subcontext_permission(&$map) {
    $map['files'] = 'file';
  } // files_handle_on_project_subcontext_permission