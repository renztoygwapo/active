<?php

  /**
   * Announcements framework implementation
   *
   * @package angie.framework.announcements
   */
  class AnnouncementsFramework extends AngieFramework {
    
    /**
     * Short framework name
     *
     * @var string
     */
    protected $name = 'announcements';

    /**
     * Define framework routes
     */
    function defineRoutes() {
      Router::map('admin_announcements', 'admin/announcements', array('controller' => 'announcements_admin', 'module' => ANNOUNCEMENTS_FRAMEWORK_INJECT_INTO));
      Router::map('admin_announcements_add', 'admin/announcements/add', array('controller' => 'announcements_admin', 'action' => 'add', 'module' => ANNOUNCEMENTS_FRAMEWORK_INJECT_INTO));
      Router::map('admin_announcements_reorder', 'admin/announcements/reorder', array('controller' => 'announcements_admin', 'action' => 'reorder', 'module' => ANNOUNCEMENTS_FRAMEWORK_INJECT_INTO));

      Router::map('admin_announcement', 'admin/announcements/:announcement_id', array('controller' => 'announcements_admin', 'action' => 'view', 'module' => ANNOUNCEMENTS_FRAMEWORK_INJECT_INTO), array('announcement_id' => Router::MATCH_ID));
      Router::map('admin_announcement_edit', 'admin/announcements/:announcement_id/edit', array('controller' => 'announcements_admin', 'action' => 'edit', 'module' => ANNOUNCEMENTS_FRAMEWORK_INJECT_INTO), array('announcement_id' => Router::MATCH_ID));
      Router::map('admin_announcement_enable', 'admin/announcements/:announcement_id/enable', array('controller' => 'announcements_admin', 'action' => 'enable', 'module' => ANNOUNCEMENTS_FRAMEWORK_INJECT_INTO), array('announcement_id' => Router::MATCH_ID));
      Router::map('admin_announcement_disable', 'admin/announcements/:announcement_id/disable', array('controller' => 'announcements_admin', 'action' => 'disable', 'module' => ANNOUNCEMENTS_FRAMEWORK_INJECT_INTO), array('announcement_id' => Router::MATCH_ID));
      Router::map('admin_announcement_delete', 'admin/announcements/:announcement_id/delete', array('controller' => 'announcements_admin', 'action' => 'delete', 'module' => ANNOUNCEMENTS_FRAMEWORK_INJECT_INTO), array('announcement_id' => Router::MATCH_ID));

      Router::map('announcement_dismiss', 'announcements/:announcement_id/dismiss', array('controller' => 'announcements', 'action' => 'dismiss', 'module' => ANNOUNCEMENTS_FRAMEWORK_INJECT_INTO), array('announcement_id' => Router::MATCH_ID));
    } // defineRoutes
    
    /**
     * Define framework handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_admin_panel', 'on_admin_panel');
      EventsManager::listen('on_homescreen_widget_types', 'on_homescreen_widget_types');
    } // defineHandlers
    
  }