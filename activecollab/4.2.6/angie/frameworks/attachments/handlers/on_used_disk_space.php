<?php

  /**
   * on_used_disk_space event handler implementation
   *
   * @package angie.frameworks.attachments
   * @subpackage handlers
   */

  /**
   * Handle on_used_disk_space event
   *
   * @param integer $used_disk_space
   */
  function attachments_handle_on_used_disk_space(&$used_disk_space) {
    // attachments which are in use
    $attachments_in_use_size = DB::executeFirstCell('SELECT SUM(size) FROM ' . TABLE_PREFIX . 'attachments WHERE state > ? AND !(parent_id IS NULL OR parent_id = 0)', STATE_DELETED);
    if ($attachments_in_use_size) {
      $used_disk_space['uploaded_attachments'] = array(
        'title'         => lang('Uploaded Attachments'),
        'size'          => $attachments_in_use_size,
        'color'         => '#64b84b'
      );
    } // if

    // attachments without parent
    $temporary_attachments_size = DB::executeFirstCell('SELECT SUM(size) FROM ' . TABLE_PREFIX . 'attachments WHERE (parent_id IS NULL OR parent_id = 0)');
    if ($temporary_attachments_size) {
      $used_disk_space['temporary_attachments'] = array(
        'title'         => lang('Temporary Attachments'),
        'size'          => $temporary_attachments_size,
        'color'         => '#ffc325',
        'cleanup'       => array(
          'url'                 => Router::assemble('disk_space_remove_temporary_attachments'),
          'title'               => lang('Remove temporary attachments'),
          'success_message'     => lang('Temporary attachments removed successfully'),
          'confirm_message'     => lang('Are you sure that you want to remove temporary attachments?')
        )
      );
    } // if
  } // attachments_handle_on_used_disk_space