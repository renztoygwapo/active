<?php

  /**
   * Discussions module on_project_export event handler
   *
   * @package activeCollab.modules.discussions
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
  function discussions_handle_on_project_export(&$exporters, $project, $project_tabs) {
    if (in_array('discussions', $project_tabs, true)) {
  		$exporters['discussions'] = array(
    		'name' => lang('Discussions'),
    		'exporter' => 'DiscussionsProjectExporter',
    	);
    } //if
  } //discussions_handle_on_project_export