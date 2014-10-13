<?php

  // Build on top of general module
  AngieApplication::useController('task_analyzer_filters', TASKS_MODULE);

  /**
   * Weekly completed tasks reports controller
   *
   * @package activeCollab.modules.tasks
   * @subpackage controllers
   */
  class WeeklyCompletedTasksReportsController extends TaskAnalyzerFiltersController {

    /**
     * Return filter class managed by this controller
     *
     * @return string
     */
    function getFilterType() {
      return 'WeeklyCompletedTasksReport';
    } // getFilterType

    /**
     * Return filter ID variable name
     *
     * @return mixed
     */
    function getFilterIdVariableName() {
      return 'weekly_completed_tasks_report_id';
    } // getFilterIdVariableName

  }