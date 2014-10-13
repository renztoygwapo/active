<?php

  // Build on top of announcements admin framework controller
  AngieApplication::useController('fw_announcements_admin', ANNOUNCEMENTS_FRAMEWORK);

  /**
   * Announcements administration controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class AnnouncementsAdminController extends FwAnnouncementsAdminController {
  }