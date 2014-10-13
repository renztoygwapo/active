<?php

  // Build on top of the framework level controller
  AngieApplication::useController('fw_help_videos', HELP_FRAMEWORK);

  /**
   * Application level help videos controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class HelpVideosController extends FwHelpVideosController {

  }