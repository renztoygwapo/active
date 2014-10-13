<?php

  // Build on top of framework controller
  AngieApplication::useController('fw_email_admin', EMAIL_FRAMEWORK);

  /**
   * Mailing administration controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class EmailAdminController extends FwEmailAdminController {
    
  }