<?php

  /**
   * on_homescreen_widget_types event handler
   * 
   * @package angie.frameworks.system
   * @subpackage handlers
   */

  /**
   * Handle on_homescreen_widget_types event
   * 
   * @param array $types
   * @param IUser $user
   */
  function system_handle_on_homescreen_widget_types(&$types, IUser &$user) {
    $types[] = new MyProjectsHomescreenWidget();
    $types[] = new FavoriteProjectsHomescreenWidget();
    $types[] = new DayOverviewHomescreenWidget();
    $types[] = new WelcomeHomescreenWidget();
  } // system_handle_on_homescreen_widget_types