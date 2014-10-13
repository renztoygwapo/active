<?php

  /**
   * Tasks handle on_project_permissions event
   *
   * @package activeCollab.modules.tasks
   * @subpackage handlers
   */

  /**
   * Handle on_project_permissions event
   *
   * @param array $permissions
   */
  function tasks_handle_on_project_permissions(&$permissions) {
  	$permissions['task'] = lang('Tasks');
  } // tasks_handle_on_project_permissions