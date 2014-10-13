<?php

  /**
   * status module on_status_bar event handler
   *
   * @package activeCollab.modules.status
   * @subpackage handlers
   */
  
  /**
   * Register status bar items
   *
   * @param StatusBar $status_bar
   * @param IUser $user
   */
  function status_handle_on_status_bar(StatusBar &$status_bar, IUser &$user) {
    if(StatusUpdates::canUse($user)) {
      $status_bar->add('status_updates', lang('Status Updates'), Router::assemble('status_updates'), AngieApplication::getImageUrl('status-bar/status-updates.png', STATUS_MODULE), array(
        'group' => StatusBar::GROUP_LEFT, 
        'badge' => StatusUpdates::countNewMessagesForUser($user), 
      ));
    } // if
  } // system_handle_on_status_bar