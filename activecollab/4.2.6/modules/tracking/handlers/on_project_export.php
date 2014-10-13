<?php

  /**
   * Tracking module on_project_export event handler
   *
   * @package activeCollab.modules.tracking
   * @subpackage handlers
   */

  /**
   * Handle project exporting
   *
   * @param array $exporters
   * @param Project $project
   * @param array $project_tabs
   */
  function tracking_handle_on_project_export(&$exporters, $project, $project_tabs) {
  	if (in_array('time', $project_tabs, true)) {
    	$exporters['tracking'] = array(
    		'name' => lang('Time and Expenses'),
    		'exporter' => 'TrackingExporter',
    	);
  	} //if
  } //tracking_handle_on_project_export