<?php

  /**
   * on_homescreen_widget_types event handler
   */
  
  /**
   * Handle on_homescreen_widget_types event
   *
   * @param array $types
   * @param IUser $user
   */
  function test_handle_on_homescreen_widget_types(&$types, IUser &$user) {
    	$types[] = new TestHomeScreenWidget();
  }