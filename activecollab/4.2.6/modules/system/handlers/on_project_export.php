<?php

  /**
   * System module on_project_export event handler
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */

  /**
   * Handle project exporting
   *
   * @param array $exporters
   * @param Project $project
   * @param array $project_tabs
   * @return null
   */
  function system_handle_on_project_export(&$exporters, $project, $project_tabs) {
    $exporters['system'] = array(
  		'name' => lang('Overview'),
  		'exporter' => 'SystemProjectExporter',
  		'mandatory' => true
  	);
  	$exporters['people'] = array(
  		'name' => lang('People'),
  		'exporter' => 'PeopleProjectExporter',
  		'mandatory' => true
  	);
  	if (in_array('milestones', $project_tabs, true)) {
  		$exporters['milestones'] = array(
    		'name' => lang('Milestones'),
    		'exporter' => 'MilestonesProjectExporter',
    	);
  	} //if
  } // system_handle_on_project_export