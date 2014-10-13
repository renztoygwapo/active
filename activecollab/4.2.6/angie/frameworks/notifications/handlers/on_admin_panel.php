<?php

  /**
   * on_admin_panel event handler
   * 
   * @package angie.framework.notifications
   * @subpackage handlers
   */

  /**
   * Handle on_admin_panel event
   * 
   * @param AdminPanel $admin_panel
   */
  function notifications_handle_on_admin_panel(AdminPanel &$admin_panel) {
    $admin_panel->addToGeneral('notifications', lang('Notifications Center'), Router::assemble('notifications_admin'), AngieApplication::getImageUrl('admin-panel/notifications.png', NOTIFICATIONS_FRAMEWORK), array(
      'onclick' => new FlyoutFormCallback(array(
        'title' => lang('Notification Settings'),
        'success_event' => 'notifications_settings_updated',
        'success_message' => lang('Settings updated')
      )), 
    ));
  } // notifications_handle_on_admin_panel