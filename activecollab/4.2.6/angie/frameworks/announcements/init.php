<?php

  /**
   * Announcements framework initialization file
   *
   * @package angie.framework.announcements
   */
  
  const ANNOUNCEMENTS_FRAMEWORK = 'announcements';
  const ANNOUNCEMENTS_FRAMEWORK_PATH = __DIR__;
  
  defined('ANNOUNCEMENTS_FRAMEWORK_INJECT_INTO') or define('ANNOUNCEMENTS_FRAMEWORK_INJECT_INTO', 'system');

  // Available announcement body types
  const ANNOUNCEMENT_BODY_TYPE_PLAIN_TEXT = 0;
  const ANNOUNCEMENT_BODY_TYPE_HTML = 1;

  // Available announcement states
  const ANNOUNCEMENT_DISABLED = 0;
  const ANNOUNCEMENT_ENABLED = 1;
  
  AngieApplication::setForAutoload(array(

    // Base classes
    'FwAnnouncement' => ANNOUNCEMENTS_FRAMEWORK_PATH . '/models/announcements/FwAnnouncement.class.php',
    'FwAnnouncements' => ANNOUNCEMENTS_FRAMEWORK_PATH . '/models/announcements/FwAnnouncements.class.php',

    // Homescreen widgets
    'AnnouncementsHomescreenWidget' => ANNOUNCEMENTS_FRAMEWORK_PATH . '/models/homescreen_widgets/AnnouncementsHomescreenWidget.class.php',

    // Notifications
    'FwNewAnnouncementNotification' => ANNOUNCEMENTS_FRAMEWORK_PATH . '/notifications/FwNewAnnouncementNotification.class.php',
  ));