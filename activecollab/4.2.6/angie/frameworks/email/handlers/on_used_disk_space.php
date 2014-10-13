<?php

  /**
   * on_used_disk_space event handler implementation
   *
   * @package angie.frameworks.email
   * @subpackage handlers
   */

  /**
   * Handle on_used_disk_space event
   *
   * @param integer $used_disk_space
   */
  function email_handle_on_used_disk_space(&$used_disk_space) {
    $incoming_mail_attachments_size = DB::executeFirstCell('SELECT SUM(file_size) FROM ' . TABLE_PREFIX . 'incoming_mail_attachments');
    if ($incoming_mail_attachments_size) {
      $used_disk_space['incoming_mail'] = array(
        'title'         => lang('Pending/Conflicted Emails'),
        'size'          => $incoming_mail_attachments_size,
        'color'         => '#a61a7f'
      );
    } // if
  } // email_handle_on_used_disk_space