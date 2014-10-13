<?php

  // Build on top of general module
  AngieApplication::useController('task_analyzer_filters', TASKS_MODULE);

  /**
   * Open vs completed task reports controller
   *
   * @package activeCollab.modules.tasks
   * @subpackage controllers
   */
  class OpenVsCompletedTasksReportsController extends TaskAnalyzerFiltersController {

    /**
     * Return filter class managed by this controller
     *
     * @return string
     */
    function getFilterType() {
      return 'OpenVsCompletedTasksReport';
    } // getFilterType

    /**
     * Return filter ID variable name
     *
     * @return mixed
     */
    function getFilterIdVariableName() {
      return 'open_vs_completed_tasks_report_id';
    } // getFilterIdVariableName

  }