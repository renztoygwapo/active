<?php

  /**
   * on_load_control_tower event handler
   *
   * @package angie.frameworks.email
   * @subpackage handlers
   */

  /**
   * Handle on_load_control_tower event
   *
   * @param ControlTower $control_tower
   * @param User $user
   */
  function email_handle_on_load_control_tower(ControlTower &$control_tower, User &$user) {

    if (!AngieApplication::isOnDemand()) {

      //  Email to Comment
      if(ConfigOptions::getValue('control_tower_check_reply_to_comment') && !IncomingMailboxes::testReplyToComments(AngieApplication::mailer()->getDefaultSender()->getEmail())) {
        $control_tower->indicators()->add('mail_to_comment', array(
          'label' => lang('Reply to Comment'),
          'value' => null,
          'url' => Router::assemble('email_admin_reply_to_comment'),
          'is_ok' => false,
        ));
      } // if

    } // if

    //  Queue Count
    if(ConfigOptions::getValue('control_tower_check_email_queue')) {
      $queue_count_label = OutgoingMessages::countUnsent();
      if ($queue_count_label > 0) {
        $control_tower->indicators()->add('queue', array(
          'label' => lang('Mailing Queue'),
          'value' => $queue_count_label,
          'url' => Router::assemble('outgoing_messages_admin'),
          'is_ok' => false,
        ));
      } // if
    } // if

    //  Conflicts Count
    if(ConfigOptions::getValue('control_tower_check_email_conflicts')) {
      $conflicts_count_label = IncomingMails::countConflicts();
      if ($conflicts_count_label > 0) {
        $control_tower->indicators()->add('conflicts', array(
          'label' => lang('Mail Conflicts'),
          'value' => $conflicts_count_label,
          'url' => Router::assemble('incoming_email_admin_conflict'),
          'is_ok' => false,
        ));
      } // if
    } // if

  } // email_handle_on_load_control_tower