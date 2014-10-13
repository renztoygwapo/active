<?php

  /**
   * on_homescreen_widget_types event handler
   * 
   * @package angie.frameworks.authentication
   * @subpackage handlers
   */

  /**
   * Handle on_homescreen_widget_types event
   * 
   * @param array $types
   * @param IUser $user
   */
  function authentication_handle_on_homescreen_widget_types(&$types, IUser &$user) {
    if($user instanceof User && $user->canSeeWhoIsOnline()) {
      $types[] = new WhosOnlineHomescreenWidget();
    } // if
  } // authentication_handle_on_homescreen_widget_types