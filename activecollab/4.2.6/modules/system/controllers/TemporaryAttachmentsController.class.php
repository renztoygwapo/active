<?php

  // Build on top of framework controller
  AngieApplication::useController('fw_temporary_attachments', ATTACHMENTS_FRAMEWORK);

  /**
   * Temp attachments implementation
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class TemporaryAttachmentsController extends FwTemporaryAttachmentsController {
    
  }