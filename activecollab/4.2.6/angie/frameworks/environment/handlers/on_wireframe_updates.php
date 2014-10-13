<?php

  /**
   * on_wireframe_updates event handler implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage handlers
   */

  /**
   * Handle on_wireframe_updates event
   * 
   * @param array $wireframe_data
   * @param array $response_data
   * @param boolean $on_unload
   * @param User $user
   */
  function environment_handle_on_wireframe_updates(&$wireframe_data, &$response_data, $on_unload, &$user) {
    $counter = 1;

    while(isset($wireframe_data["behaviour_event_{$counter}"]) && $wireframe_data["behaviour_event_{$counter}"]) {
      $tags = explode(',', $wireframe_data["behaviour_event_{$counter}"]);

      if(count($tags) >= 2) {
        $event_class = array_shift($tags);
        $timestamp = (integer) array_pop($tags);

        if($event_class && $timestamp) {
          AngieApplication::behaviour()->record($event_class, $tags, $timestamp);
        } // if
      } // if

      $counter++;
    } // while

    if(empty($on_unload)) {
      if(isset($wireframe_data['refresh_backend_wireframe']) && $wireframe_data['refresh_backend_wireframe']) {
        $main_menu = new MainMenu();
        $main_menu->load($user, true);

        $status_bar = new StatusBar();
        $status_bar->load($user);

        $response_data['refresh_backend_wireframe'] = array(
          'main_menu' => $main_menu,
          'status_bar' => $status_bar,
        );
      } // if

      if($user->isAdministrator()) {
        $control_tower = new ControlTower($user);
        $response_data['menu_bar_badges']['admin'] = $control_tower->loadBadgeValue();
      } // if
    } // if
  } // environment_handle_on_wireframe_updates