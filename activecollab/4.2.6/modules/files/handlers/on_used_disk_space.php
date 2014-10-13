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
  function files_handle_on_used_disk_space(&$used_disk_space) {
    $project_objects_table = TABLE_PREFIX . 'project_objects';

    $disk_size_in_files = 0;
    $disk_size_in_files += DB::executeFirstCell("SELECT SUM(integer_field_2) FROM $project_objects_table WHERE type = 'File'");
    $disk_size_in_files += DB::executeFirstCell("SELECT SUM(size) FROM " . TABLE_PREFIX . "file_versions");

    if ($disk_size_in_files) {
      $used_disk_space['files'] = array(
        'title'         => lang('Uploaded Files'),
        'size'          => $disk_size_in_files,
        'color'         => '#eb4965'
      );
    } // if
  } // files_handle_on_used_disk_space