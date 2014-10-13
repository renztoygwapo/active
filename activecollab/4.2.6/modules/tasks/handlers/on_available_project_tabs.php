<?php

  /**
   * Tasks module on_available_project_tabs event handler
   * 
   * @package activeCollab.modules.tasks
   * @subpackage handlers
   */

  /**
   * Populate list of available project tabs
   * 
   * @param array $tabs
   */
  function tasks_handle_on_available_project_tabs(&$tabs) {
    $tabs['tasks'] = lang('Tasks');
  } // tasks_handle_on_available_project_tabs