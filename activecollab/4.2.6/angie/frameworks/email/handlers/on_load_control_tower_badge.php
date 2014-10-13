<?php

  /**
   * on_load_control_tower_badge event handler implementation
   *
   * @package angie.frameworks.email
   * @subpackage handlers
   */

  /**
   * Handle on_load_control_tower_badge
   *
   * @param integer $badge_value
   * @param User $user
   */
  function email_handle_on_load_control_tower_badge(&$badge_value, User &$user) {
    if($user->isAdministrator()) {
      if (!AngieApplication::isOnDemand()) {
        if(ConfigOptions::getValue('control_tower_check_reply_to_comment') && !IncomingMailboxes::testReplyToComments(AngieApplication::mailer()->getDefaultSender()->getEmail())) {
          $badge_value++;
        } // if
      } // if

      if(ConfigOptions::getValue('control_tower_check_email_queue') && OutgoingMessages::countUnsent() > 0) {
        $badge_value++;
      } // if

      if(ConfigOptions::getValue('control_tower_check_email_conflicts') && IncomingMails::countConflicts()) {
        $badge_value++;
      } // if
    } // if
  } // email_handle_on_load_control_tower_badge