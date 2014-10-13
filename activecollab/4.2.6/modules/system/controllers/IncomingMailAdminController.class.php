<?php

  // Build on top of framework level controller
  AngieApplication::useController('fw_incoming_mail_admin', EMAIL_FRAMEWORK);

  /**
   * Incoming mail administration controller
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IncomingMailAdminController extends FwIncomingMailAdminController {
    
  }