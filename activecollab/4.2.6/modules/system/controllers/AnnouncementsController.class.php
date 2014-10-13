<?php

  // Build on top of announcements framework controller
  AngieApplication::useController('fw_announcements', ANNOUNCEMENTS_FRAMEWORK);

  /**
   * Announcements controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class AnnouncementsController extends FwAnnouncementsController {
  }