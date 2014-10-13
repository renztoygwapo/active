<?php

  // Build on top of schedule controller
  AngieApplication::useController('fw_schedule', SCHEDULE_FRAMEWORK);

  /**
   * Application level schedule controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ScheduleController extends FwScheduleController {
  	
  }