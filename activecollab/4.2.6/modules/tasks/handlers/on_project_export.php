<?php

  /**
   * Tasks module on_project_export event handler
   *
   * @package activeCollab.modules.tasks
   * @subpackage handlers
   */

  /**
   * Handle project exporting
   *
   * @param array $exportable_modules
   * @param Project $project
   * @param array $project_tabs
   * @return null
   */
  function tasks_handle_on_project_export(&$exportable_modules, $project, $project_tabs) {
  	if (in_array('tasks', $project_tabs, true)) {
    	$exportable_modules['tasks'] = array(
    		'name' => lang('Tasks'),
    		'exporter' => 'TasksProjectExporter'
    	);
  	} //if
  } // tasks_handle_on_project_export