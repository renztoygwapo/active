<?php

  /**
   * on_wireframe_updates event handler implementation
   *
   * @package angie.frameworks.notifications
   * @subpackage handlers
   */

  /**
   * Handle wireframe updates even
   *
   * @param array $wireframe_data
   * @param array $response_data
   * @param boolean $on_unload
   * @param User $user
   */
  function notifications_handle_on_wireframe_updates(&$wireframe_data, &$response_data, $on_unload, &$user) {
    if(empty($on_unload)){
      $response_data['status_bar_badges']['notifications'] = Notifications::countUnseenByUser($user);

      if(ConfigOptions::getValueFor('notifications_show_indicators', $user) >= Notifications::SHOW_BADGE && $response_data['status_bar_badges']['notifications']) {
        $unseen_notifications = Notifications::findUnseenByUser($user, 5);

        if($unseen_notifications) {
          $response_data['unseen_notifications'] = array();

          foreach($unseen_notifications as $notification) {
            $response_data['unseen_notifications'][$notification->getId()] = array(
              'message' => $notification->getMessage($user),
              'url' => $notification->getVisitUrl($user),
            );
          } // foreach

          $response_data['unseen_notifications'] = JSON::valueToMap($response_data['unseen_notifications']);
        } // if
      } // if
    } // if
  } // notifications_handle_on_wireframe_updates