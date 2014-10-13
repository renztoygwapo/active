<?php

  // Build on top of framework level controller
  AngieApplication::useController('fw_public_notifications', NOTIFICATIONS_FRAMEWORK);

  /**
   * Public notifications controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class PublicNotificationsController extends FwPublicNotificationsController {
    
  }