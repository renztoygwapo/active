<?php

  /**
   * on_homescreen_widget_types event handler
   *
   * @package angie.frameworks.announcements
   * @subpackage handlers
   */

  /**
   * Handle on_homescreen_widget_types event
   *
   * @param array $types
   * @param IUser $user
   */
  function announcements_handle_on_homescreen_widget_types(&$types, IUser &$user) {
    $types[] = new AnnouncementsHomescreenWidget();
  } // announcements_handle_on_homescreen_widget_types