<?php

/**
 * Files module on_project_export event handler
 *
 * @package activeCollab.modules.files
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
function files_handle_on_project_export(&$exporters, $project, $project_tabs) {
  if (in_array('files', $project_tabs, true)) {
    $exporters['files'] = array(
      'name' => lang('Files'),
      'exporter' => 'FilesProjectExporter',
    );
  } //if
} // notebooks_handle_on_project_export