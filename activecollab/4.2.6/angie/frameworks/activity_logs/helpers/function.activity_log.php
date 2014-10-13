<?php

  /**
   * activity_log helper implementation
   * 
   * @package angie.frameworks.activity_logs
   * @subpackage helpers
   */

  /**
   * Render activities
   * 
   * Parameters:
   * 
   * - activity_logs - Ungrouped array of activity logs. This helper will group 
   *   logs by date
   * - context - Context in which log is displayed. Based on context, log 
   *   entries may decide to render rows a bit differently
   * - has_more - Indicator whether there are more entries in the log or not
   * - load_more_url - URL that script needs to load in order to load more 
   *   entries to display to the visitor
   * - chart - draws a chart of activity logs grouped by a date  
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_activity_log($params, &$smarty) {
    $user = array_required_var($params, 'user', true, 'IUser');
    $activity_logs = array_var($params, 'activity_logs', null, true);
    
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    
    $id = isset($params['id']) && $params['id'] ? $params['id'] : HTML::uniqueId('activity_log');
  
    $wrapper = '<div id="' . $id . '">';

    // Phone specific opener
    if($interface == AngieApplication::INTERFACE_PHONE) {
      AngieApplication::useHelper('image_url', ENVIRONMENT_FRAMEWORK);

      $wrapper .= '<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
        <li data-role="list-divider"><img src="' . smarty_function_image_url(array(
          'name' => 'icons/listviews/navigate-recent-activities-icon.png',
          'module' => SYSTEM_MODULE,
          'interface' => AngieApplication::INTERFACE_PHONE
        ), $smarty) . '" class="divider_icon" alt="">' . lang('Recent Activities') . '</li>
      </ul>';

    // Printer specific opener
    } else if ($interface == AngieApplication::INTERFACE_PRINTER) {
      $wrapper .= '<h2>' . lang('Recent Activities') . '</h2>';
    } // if

    $wrapper .= '</div>';

    list($authors, $subjects, $targets) = ActivityLogs::loadRelatedDataFromActivities($activity_logs, $user); // Load related data, so we can pass it to callbacks

    if(array_key_exists('rss_url', $params)) {
      $rss_url = $params['rss_url'];
    } else {
      $rss_url = $user instanceof User && $user->isFeedUser() ? Router::assemble('backend_activity_log_rss', array(AngieApplication::API_TOKEN_VARIABLE_NAME => $user->getFeedToken())) : null;
    } // if

    if($interface != AngieApplication::INTERFACE_PHONE) {
      AngieApplication::useWidget('activity_log', ACTIVITY_LOGS_FRAMEWORK);
    } // if

    $wrapper .= '<script type="text/javascript">$("#' . $id . '").activityLog(' . JSON::encode(array(
      'entries' => $activity_logs,
      'authors' => $authors,
      'subjects' => $subjects,
      'targets' => $targets,
      'callbacks' => ActivityLogs::getCallbacks(),
      'decorator' => ActivityLogs::getDecorator($interface),
      'interface' => $interface,
      'rss_url' => $rss_url,
    )) . ');</script>';

    return $wrapper;
  } // smarty_function_activity_log