<?php

  /**
   * Notifications framework definition
   *
   * @package angie.frameworks.notifications
   */
  class NotificationsFramework extends AngieFramework {
    
    /**
     * Framework name
     *
     * @var string
     */
    protected $name = 'notifications';
    
    /**
     * Define notification framework routes
     */
    function defineRoutes() {
      Router::map('public_notifications_subscribe', 'public/notifications/subscribe', array('controller' => 'public_notifications', 'action' => 'subscribe', 'module' => NOTIFICATIONS_FRAMEWORK_INJECT_INTO));
      Router::map('public_notifications_unsubscribe', 'public/notifications/unsubscribe', array('controller' => 'public_notifications', 'action' => 'unsubscribe', 'module' => NOTIFICATIONS_FRAMEWORK_INJECT_INTO));

      Router::map('notifications', 'notifications', array('controller' => 'notifications', 'module' => NOTIFICATIONS_FRAMEWORK_INJECT_INTO));
      Router::map('notifications_mass_edit', 'notifications/mass-edit', array('controller' => 'notifications', 'action' => 'mass_edit', 'module' => NOTIFICATIONS_FRAMEWORK_INJECT_INTO));
      Router::map('notifications_settings', 'notifications/settings', array('controller' => 'notifications', 'action' => 'settings', 'module' => NOTIFICATIONS_FRAMEWORK_INJECT_INTO));
      Router::map('notifications_refresh', 'notifications/refresh', array('controller' => 'notifications', 'action' => 'refresh', 'module' => NOTIFICATIONS_FRAMEWORK_INJECT_INTO));
      Router::map('notifications_seen_all', 'notifications/seen-all', array('controller' => 'notifications', 'action' => 'seen_all', 'module' => NOTIFICATIONS_FRAMEWORK_INJECT_INTO));

      Router::map('notification', 'notifications/:notification_id', array('controller' => 'notifications', 'action' => 'view', 'module' => NOTIFICATIONS_FRAMEWORK_INJECT_INTO), array('notification_id' => Router::MATCH_ID));
      Router::map('notification_edit', 'notifications/:notification_id/edit', array('controller' => 'notifications', 'action' => 'edit', 'module' => NOTIFICATIONS_FRAMEWORK_INJECT_INTO), array('notification_id' => Router::MATCH_ID));
      Router::map('notification_delete', 'notifications/:notification_id/delete', array('controller' => 'notifications', 'action' => 'delete', 'module' => NOTIFICATIONS_FRAMEWORK_INJECT_INTO), array('notification_id' => Router::MATCH_ID));
      Router::map('notification_mark_read', 'notifications/:notification_id/mark-read', array('controller' => 'notifications', 'action' => 'mark_read', 'module' => NOTIFICATIONS_FRAMEWORK_INJECT_INTO), array('notification_id' => Router::MATCH_ID));
      Router::map('notification_mark_unread', 'notifications/:notification_id/mark-unread', array('controller' => 'notifications', 'action' => 'mark_unread', 'module' => NOTIFICATIONS_FRAMEWORK_INJECT_INTO), array('notification_id' => Router::MATCH_ID));

      Router::map('notifications_popup', 'notifications/popup', array('controller' => 'notifications', 'action' => 'popup', 'module' => NOTIFICATIONS_FRAMEWORK_INJECT_INTO));
      Router::map('notifications_popup_show_only_unread', 'notifications/popup/show-only-unread', array('controller' => 'notifications', 'action' => 'show_only_unread', 'module' => NOTIFICATIONS_FRAMEWORK_INJECT_INTO));
      Router::map('notifications_popup_show_read_and_unread', 'notifications/popup/show-read-and-unread', array('controller' => 'notifications', 'action' => 'show_read_and_unread', 'module' => NOTIFICATIONS_FRAMEWORK_INJECT_INTO));

      // Settings
      Router::map('notifications_admin', 'admin/notifications', array('controller' => 'notifications_admin', 'module' => NOTIFICATIONS_FRAMEWORK_INJECT_INTO));
    } // defineRoutes
    
    /**
     * Define event handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_admin_panel', 'on_admin_panel');
      EventsManager::listen('on_status_bar', 'on_status_bar');
      EventsManager::listen('on_wireframe_updates', 'on_wireframe_updates');
    } // defineHandlers
    
  }