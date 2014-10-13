<?php

  /**
   * on_homescreen_widget_types event handler
   * 
   * @package angie.frameworks.tracking
   * @subpackage handlers
   */

  /**
   * Handle on_homescreen_widget_types event
   * 
   * @param array $types
   * @param IUser $user
   */
  function tracking_handle_on_homescreen_widget_types(&$types, IUser &$user) {
    if(!($user instanceof Client)) {
      $types[] = new MyTimeHomescreenWidget();
      $types[] = new TrackedTimeHomescreenWidget();
      $types[] = new TrackedExpensesHomescreenWidget();
    } // if
  } // tracking_handle_on_homescreen_widget_types