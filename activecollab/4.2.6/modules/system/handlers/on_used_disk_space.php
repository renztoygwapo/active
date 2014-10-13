<?php

/**
 * on_used_disk_space event handler implementation
 *
 * @package activeCollab.modules.files
 * @subpackage handlers
 */

/**
 * Handle on_used_disk_space event
 *
 * @param integer $used_disk_space
 */
function system_handle_on_used_disk_space(&$used_disk_space) {
	$disk_size_in_files = 0;
	$disk_size_in_files += (integer) DB::executeFirstCell("SELECT SUM(file_size) FROM " . TABLE_PREFIX . "project_object_templates WHERE type = ?", "File");

	if ($disk_size_in_files) {
		$used_disk_space['project_object_templates'] = array(
			'title'         => lang('Template Files'),
			'size'          => $disk_size_in_files,
			'color'         => '#0080BC'
		);
	} // if
} // files_handle_on_template_used_disk_space