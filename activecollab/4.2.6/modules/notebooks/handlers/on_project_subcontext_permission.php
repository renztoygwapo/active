<?php

  /**
   * on_project_subcontext_permission event handler implementation
   * 
   * @package activeCollab.modules.notebooks
   * @subpackage handlers
   */

  /**
   * Handle on_project_subcontext_permission event
   * 
   * @param array $map
   */
  function notebooks_handle_on_project_subcontext_permission(&$map) {
    $map['notebooks'] = 'notebook';
  } // notebooks_handle_on_project_subcontext_permission