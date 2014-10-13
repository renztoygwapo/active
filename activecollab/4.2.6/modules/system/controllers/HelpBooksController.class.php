<?php

  // Build on top of the framework level controller
  AngieApplication::useController('fw_help_books', HELP_FRAMEWORK);

  /**
   * Application level help books controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class HelpBooksController extends FwHelpBooksController {

  }