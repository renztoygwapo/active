<?php

  // Build on top of framework level controller
  AngieApplication::useController('fw_incoming_mail_filter_admin', EMAIL_FRAMEWORK);

  /**
   * Incoming mail filter administration controller
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IncomingMailFilterAdminController extends FwIncomingMailFilterAdminController {
    
  }