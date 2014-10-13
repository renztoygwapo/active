<?php

  /**
   * Weekly completed tasks filter
   *
   * @package activeCollab.modules.tasks
   * @subpackage models
   */
  class WeeklyCompletedTasksReport extends TasksAnalyzerReport {

    /**
     * Run report
     *
     * @param IUser $user
     * @param array $additional
     * @return array
     */
    function run(IUser $user, $additional = null) {
      $project_objects_table = TABLE_PREFIX . 'project_objects';

      $conditions = $this->getConditions($user);

      $total_tasks = (integer) DB::executeFirstCell("SELECT COUNT(id) FROM $project_objects_table WHERE $conditions");

      if($total_tasks) {
        if($this->getDateFilter() == self::DATE_FILTER_ANY) {
          $range = DB::executeFirstRow("SELECT MIN(DATE(created_on)) AS 'min_created_on', MAX(DATE(created_on)) AS 'max_created_on', MAX(DATE(completed_on)) AS 'max_completed_on' FROM $project_objects_table WHERE $conditions");

          $start_from = new DateValue($range['min_created_on']);

          if($range['max_completed_on']) {
            $go_to = $range['max_completed_on'] > $range['max_created_on'] ? new DateValue($range['max_completed_on']) : new DateValue($range['max_created_on']);
          } else {
            $go_to = new DateValue($range['max_created_on']);
          } // if
        } else {
          list($start_from, $go_to) = $this->getDateFilterSelectedRange();
        } // if

        $weekly_data = array();

        DateValue::iterateWeekly($start_from, $go_to, function(DateTimeValue $week_start, DateTimeValue $week_end) use (&$weekly_data, $conditions, $project_objects_table) {
          $escaped_start = DB::escape($week_start);
          $escaped_end = DB::escape($week_end);

          $weekly_data[] = array(
            'year' => $week_start->getYear(),
            'week' => $week_start->getWeek(),
            'week_start_timestamp' => $week_start->getTimestamp(),
            'week_end_timestamp' => $week_end->getTimestamp(),
            'completed_tasks' => (integer) DB::executeFirstCell("SELECT COUNT(id) FROM $project_objects_table WHERE (completed_on BETWEEN $escaped_start AND $escaped_end) AND ($conditions)"), // All tasks created this week
          );
        }, (integer) ConfigOptions::getValueFor('time_first_week_day', $user));

        return $weekly_data;
      } // if

      return null;
    } // run

    /**
     * Return data so it is good for export
     *
     * @param IUser $user
     * @param mixed $additional
     * @return array|void
     */
    function runForExport(IUser $user, $additional = null) {
      $data = $this->run($user, $additional);

      if($data) {
        $this->beginExport(array(
          'Year',
          'Week',
          'Week Start',
          'Week Start Timestamp',
          'Week End',
          'Week End Timestamp',
          'Completed Tasks',
        ), array_var($additional, 'export_format'));

        foreach($data as $v) {
          $this->exportWriteLine(array($v['year'], $v['week'], DateValue::makeFromTimestamp($v['week_start_timestamp'])->toMySQL(), $v['week_start_timestamp'], DateValue::makeFromTimestamp($v['week_end_timestamp'])->toMySQL(), $v['week_end_timestamp'], $v['completed_tasks']));
        } // foreach

        return $this->completeExport();
      } // if

      return null;
    } // runForExport

    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'weekly_completed_tasks_report';
    } // getRoutingContext

    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('weekly_completed_tasks_report_id' => $this->getId());
    } // getRoutingContextParams

  }