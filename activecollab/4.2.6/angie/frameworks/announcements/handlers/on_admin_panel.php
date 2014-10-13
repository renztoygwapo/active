<?php

  /**
   * on_admin_panel event handler
   * 
   * @package angie.frameworks.announcements
   * @subpackage handlers
   */

  /**
   * Handle on_admin_panel event
   * 
   * @param AdminPanel $admin_panel
   */
  function announcements_handle_on_admin_panel(AdminPanel &$admin_panel) {
    $admin_panel->addToTools('announcements', lang('Announce.'), Router::assemble('admin_announcements'), AngieApplication::getImageUrl('admin_panel/announcements.png', ANNOUNCEMENTS_FRAMEWORK));
  } // announcements_handle_on_admin_panel