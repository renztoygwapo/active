<?php

  // Build on top of framework level implementation
  AngieApplication::useController('fw_scheduled_tasks', ENVIRONMENT_FRAMEWORK);

  /**
   * Scheduled tasks controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ScheduledTasksController extends FwScheduledTasksController {

    /**
     * Send morning paper
     */
    function paper() {
      $this->renderText('Started sending Morning Paper on ' . strftime(FORMAT_DATETIME) . '.<br />' ,false, false);

      MorningPaper::send(DateValue::now());
      ConfigOptions::setValue('last_frequently_activity', time());

      $this->renderText('Morning Paper sent on ' . strftime(FORMAT_DATETIME) . '.');
    } // paper

  }