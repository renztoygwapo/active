<?php

/**
 * on_used_disk_space event handler implementation
 *
 * @package activeCollab.modules.documents
 * @subpackage handlers
 */

/**
 * Handle on_used_disk_space event
 *
 * @param integer $used_disk_space
 */
function documents_handle_on_used_disk_space(&$used_disk_space) {
  $documents_table = TABLE_PREFIX . 'documents';
  $used_space_by_documents = DB::executeFirstCell('SELECT SUM(size) FROM ' . $documents_table . ' WHERE type = ?', 'File');

  if ($used_space_by_documents) {
    $used_disk_space['global_documents'] = array(
      'title'         => lang('Global Documents'),
      'size'          => $used_space_by_documents,
      'color'         => '#8e70b2'
    );
  } // if
} // documents_handle_on_used_disk_space