<?php

  /**
   * List notifications helper implementation
   *
   * @package angie.frameworks.notifications
   * @subpackage helpers
   */

  /**
   * Render list of notifications
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_list_notifications($params, &$smarty) {
    AngieApplication::useWidget('list_notifications', NOTIFICATIONS_FRAMEWORK);

    $user = array_required_var($params, 'user', true, 'IUser');
    $id = array_var($params, 'id');
    $mark_listed_as_seen = array_var($params, 'mark_listed_as_seen', true);
    $events_scope = array_var($params, 'scope', 'content');

    if(empty($id)) {
      $id = HTML::uniqueId('list_notifications');
    } // if

    $options = array(
      'refresh_url' => Router::assemble('notifications_refresh'),
      'settings_url' => Router::assemble('notifications_settings'),
      'mass_edit_url' => Router::assemble('notifications_mass_edit'),

      'delete_url_pattern' => Router::assemble('notification_delete', array('notification_id' => '--NOTIFICATION-ID--')),
      'mark_read_url_pattern' => Router::assemble('notification_mark_read', array('notification_id' => '--NOTIFICATION-ID--')),
      'mark_unread_url_pattern' => Router::assemble('notification_mark_unread', array('notification_id' => '--NOTIFICATION-ID--')),

      'read_icon_url' => AngieApplication::getImageUrl('icons/read.png', NOTIFICATIONS_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT),
      'unread_icon_url' => AngieApplication::getImageUrl('icons/unread.png', NOTIFICATIONS_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT),
      'last_refresh_timestamp' => DateTimeValue::now()->toMySQL(),

      'events_scope' => $events_scope,

      'user_id' => $user->getId(),
    );

    $described = array();

    $notifications = Notifications::findRecentByUser($user);

    if($notifications) {
      foreach($notifications as $notification) {
        $described[] = AngieApplication::describe()->object($notification, $user, false, AngieApplication::INTERFACE_DEFAULT);

        if($mark_listed_as_seen) {
          Notifications::markSeen($notification, $user);
        } // if
      } // foreach
    } // if

    return '<div class="list_notifications" id="' . $id . '"></div><script type="text/javascript">$("#' . $id . '").listNotifications(' . JSON::encode($options) . ', ' . JSON::map($described) . ');</script>';
  } // smarty_function_list_notifications