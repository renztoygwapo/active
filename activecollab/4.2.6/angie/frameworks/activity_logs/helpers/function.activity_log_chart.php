<?php

  /**
   * Render activities
   * 
   * Parameters:
   * 
   * - activity_logs - array of activity logs grouped by date.  
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_activity_log_chart($params, &$smarty) {
    $activity_logs = array_var($params, 'activity_logs', null, true);
    if ($activity_logs) {
      $project_activity_days = 15;

      $activity_logs = group_by_date($activity_logs, Authentication::getLoggedUser(), 'getCreatedOn', false, true);
      $line_chart = new LineChart('85%', '150px', 'project_recent_activities_chart_placeholder');
      $points = array();
      $gmt = DateTimeValue::now();
      $gmt->advance(-(($project_activity_days - 1) * 24 * 60 * 60));
      $gmt = $gmt->beginningOfDay();
      for ($i = 1; $i <= $project_activity_days; $i++) {
    	  $points[] = new ChartPoint($gmt->getTimestamp() * 1000, count($activity_logs[$gmt->getTimestamp()]));
     	  $gmt->advance(24*60*60);
      } //for
      $serie = new ChartSerie($points);
      $line_chart->addSeries($serie);
      return $line_chart->render(true, true, true);
    }
  } // smarty_function_activity_log