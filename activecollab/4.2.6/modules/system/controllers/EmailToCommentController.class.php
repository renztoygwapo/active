<?php

  // Build on top of framework level implementation
  AngieApplication::useController('fw_email_to_comment', EMAIL_FRAMEWORK);

  /**
   * Application level email to comment controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class EmailToCommentController extends FwEmailToCommentController {

  }