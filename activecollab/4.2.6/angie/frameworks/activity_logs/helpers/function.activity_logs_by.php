<?php

  /**
   * activity_logs_by helper implementation
   * 
   * @package angie.frameworks.activity_logs
   * @subpackage helpers
   */

  /**
   * Show activity logs by a given user
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_activity_logs_by($params, &$smarty) {
    $user = array_required_var($params, 'user', true, 'IUser');
    $by = array_required_var($params, 'by', true, 'IUser');
    
    AngieApplication::useHelper('activity_log', ACTIVITY_LOGS_FRAMEWORK);
    
    return smarty_function_activity_log(array(
      'user' => $user, 
      'activity_logs' => ActivityLogs::findRecentBy($user, $by),
      'rss_url' => $user->isFeedUser() ? $by->getRssUrl($user) : null,
    ), $smarty);
  } // smarty_function_activity_logs_by