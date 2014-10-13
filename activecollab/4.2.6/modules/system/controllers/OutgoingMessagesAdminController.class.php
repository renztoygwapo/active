<?php

  // Build on top of framework controller
  AngieApplication::useController('fw_outgoing_messages_admin', EMAIL_FRAMEWORK);

  /**
   * Outgoing email queue administration controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class OutgoingMessagesAdminController extends FwOutgoingMessagesAdminController {
    
  }