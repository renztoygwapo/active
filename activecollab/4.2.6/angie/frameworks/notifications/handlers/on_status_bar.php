<?php

  /**
   * Notifications framework on_status_bar event handler
   *
   * @package angie.frameworks.notifications
   * @subpackage handlers
   */
  
  /**
   * Register status bar items
   *
   * @param StatusBar $status_bar
   * @param IUser $user
   */
  function notifications_handle_on_status_bar(StatusBar &$status_bar, IUser &$user) {
    $status_bar->add('notifications', lang('Notifications'), Router::assemble('notifications_popup'), AngieApplication::getImageUrl('status-bar/notifications.png', NOTIFICATIONS_FRAMEWORK), array(
      'group' => StatusBar::GROUP_LEFT,
      'badge' => $user instanceof User ? Notifications::countUnseenByUser($user) : 0,
    ));
  } // notifications_handle_on_status_bar