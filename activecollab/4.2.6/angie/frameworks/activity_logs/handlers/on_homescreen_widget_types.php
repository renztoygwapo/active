<?php

  /**
   * on_homescreen_widget_types event handler
   * 
   * @package angie.frameworks.activity_logs
   * @subpackage handlers
   */

  /**
   * Handle on_homescreen_widget_types event
   * 
   * @param array $types
   * @param IUser $user
   */
  function activity_logs_handle_on_homescreen_widget_types(&$types, IUser &$user) {
    $types[] = new RecentActivitiesHomescreenWidget();
  } // activity_logs_handle_on_homescreen_widget_types