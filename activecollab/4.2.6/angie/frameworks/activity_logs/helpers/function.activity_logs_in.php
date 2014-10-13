<?php

  /**
   * activity_logs_in helper implementation
   * 
   * @package angie.frameworks.activity_logs
   * @subpackage helpers
   */

  /**
   * Show activity logs in given section
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_activity_logs_in($params, &$smarty) {
    $user = array_required_var($params, 'user', true, 'IUser');
    $in = array_required_var($params, 'in', true, 'ApplicationObject');
    
    AngieApplication::useHelper('activity_log', ACTIVITY_LOGS_FRAMEWORK);
    
    return smarty_function_activity_log(array(
      'user' => $user, 
      'activity_logs' => ActivityLogs::findRecentIn($user, $in),
      'rss_url' => $user->isFeedUser() ? $in->getRssUrl($user) : null,
    ), $smarty);
  } // smarty_function_activity_logs_in