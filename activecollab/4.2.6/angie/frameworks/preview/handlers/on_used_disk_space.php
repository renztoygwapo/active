<?php

/**
 * on_used_disk_space event handler implementation
 *
 * @package angie.frameworks.preview
 * @subpackage handlers
 */

/**
 * Handle on_used_disk_space event
 *
 * @param integer $used_disk_space
 */
function preview_handle_on_used_disk_space(&$used_disk_space) {
  if (!AngieApplication::isOnDemand()) {
    $thumbnails_disk_size = Thumbnails::cacheSize();
    if ($thumbnails_disk_size) {
      $used_disk_space['thumbnails'] = array(
        'title'         => lang('Thumbnails & Previews'),
        'size'          => $thumbnails_disk_size,
        'color'         => '#ef5f34',
        'cleanup'       => array(
          'url'                 => Router::assemble('disk_space_remove_thumbnails'),
          'title'               => lang('Remove thumbnails and previews'),
          'success_message'     => lang('Thumbnails and previews removed successfully'),
          'confirm_message'     => lang('Are you sure that you want to remove thumbnails and previews?')
        )
      );
    } // if
  } // if
} // preview_handle_on_used_disk_space