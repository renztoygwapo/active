<?php

  // Build on top of framework controller
  AngieApplication::useController('fw_outgoing_email_admin', EMAIL_FRAMEWORK);

  /**
   * Ougoing email administration controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class OutgoingEmailAdminController extends FwOutgoingEmailAdminController {
    
  }