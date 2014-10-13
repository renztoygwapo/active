<?php

  // Build on top of framework controller
  AngieApplication::useController('fw_activity_logs', ACTIVITY_LOGS_FRAMEWORK);

  /**
   * Application level activity logs controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ActivityLogsController extends FwActivityLogsController {

  }