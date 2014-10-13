<?php

  /**
   * on_project_subcontext_permission event handler implementation
   * 
   * @package activeCollab.modules.tasks
   * @subpackage handlers
   */

  /**
   * Handle on_project_subcontext_permission event
   * 
   * @param array $map
   */
  function tasks_handle_on_project_subcontext_permission(&$map) {
    $map['tasks'] = 'task';
  } // tasks_handle_on_project_subcontext_permission