<?php

  /**
   * on_load_control_tower_settings event handler implementation
   *
   * @package angie.frameworks.email
   * @subpackage handlers
   */

  /**
   * Handle on_load_control_tower_settings
   *
   * @param array $settings
   * @param User $user
   */
  function email_handle_on_load_control_tower_settings(&$settings, User &$user) {
    if(!AngieApplication::isOnDemand()) {
      $settings[lang('Email')]['control_tower_check_reply_to_comment'] = array(
        'label' => lang('Check if "Reply to Comment" Feature is Properly Configured'),
        'value' => ConfigOptions::getValue('control_tower_check_reply_to_comment'),
      );
    } //if

    $settings[lang('Email')]['control_tower_check_email_queue'] = array(
      'label' => lang('Check Unsent Messages in Email Queue'),
      'value' => ConfigOptions::getValue('control_tower_check_email_queue'),
    );
    $settings[lang('Email')]['control_tower_check_email_conflicts'] = array(
      'label' => lang('Check Incoming Mail Conflicts Count'),
      'value' => ConfigOptions::getValue('control_tower_check_email_conflicts'),
    );
  } // email_handle_on_load_control_tower_settings