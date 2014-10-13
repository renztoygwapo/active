<?php

  /**
   * Notebooks module on_project_export event handler
   *
   * @package activeCollab.modules.notebooks
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
  function notebooks_handle_on_project_export(&$exporters, $project, $project_tabs) {
  	if (in_array('notebooks', $project_tabs, true)) {
    	$exporters['notebooks'] = array(
    		'name' => lang('Notebooks'),
    		'exporter' => 'NotebooksProjectExporter',
    	);
  	} //if    
  } // notebooks_handle_on_project_export