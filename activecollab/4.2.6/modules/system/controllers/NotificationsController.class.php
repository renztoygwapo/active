<?php

  // Build on top of framework level controller
  AngieApplication::useController('fw_notifications', NOTIFICATIONS_FRAMEWORK);

  /**
   * Application level notifications controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class NotificationsController extends FwNotificationsController {

  }