<?php
  // Build on top of framework level controller
  AngieApplication::useController('fw_incoming_mail_conflict', EMAIL_FRAMEWORK);

  /**
   * Incoming mail conflictadministration controller
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IncomingMailConflictController extends FwIncomingMailConflictController {
    
  }