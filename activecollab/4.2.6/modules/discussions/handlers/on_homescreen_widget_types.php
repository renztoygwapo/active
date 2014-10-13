<?php

  /**
   * on_homescreen_widget_types event handler
   * 
   * @package angie.frameworks.discussions
   * @subpackage handlers
   */

  /**
   * Handle on_homescreen_widget_types event
   * 
   * @param array $types
   * @param IUser $user
   */
  function discussions_handle_on_homescreen_widget_types(&$types, IUser &$user) {
    $types[] = new MyDiscussionsHomescreenWidget();
  } // discussions_handle_on_homescreen_widget_types