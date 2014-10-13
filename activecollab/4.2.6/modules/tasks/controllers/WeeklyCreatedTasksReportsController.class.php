<?php

  // Build on top of general module
  AngieApplication::useController('task_analyzer_filters', TASKS_MODULE);

  /**
   * Weekly created tasks reports controller
   *
   * @package activeCollab.modules.tasks
   * @subpackage controllers
   */
  class WeeklyCreatedTasksReportsController extends TaskAnalyzerFiltersController {

    /**
     * Return filter class managed by this controller
     *
     * @return string
     */
    function getFilterType() {
      return 'WeeklyCreatedTasksReport';
    } // getFilterType

    /**
     * Return filter ID variable name
     *
     * @return mixed
     */
    function getFilterIdVariableName() {
      return 'weekly_created_tasks_report_id';
    } // getFilterIdVariableName

  }