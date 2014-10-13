<?php

  /**
   * on_predefined_homescreen_tabs event handler
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */

  /**
   * Get the list of predefined home screen tabs
   *
   * @param User $user
   * @param array $tabs
   */
  function system_handle_on_predefined_homescreen_tabs(User &$user, &$tabs) {
    $tabs['whats_new'] = lang("What's New");

    if(!($user instanceof Client)) {
      $tabs['my_tasks'] = lang('My Tasks');
    } // if
  } // system_handle_on_predefined_homescreen_tabs