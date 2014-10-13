<?php

  // Build on top of framework level implementation
  AngieApplication::useController('fw_scheduled_tasks_admin', ENVIRONMENT_FRAMEWORK);

  /**
   * Application level scheduled tasks admin controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ScheduledTasksAdminController extends FwScheduledTasksAdminController {
  
  }