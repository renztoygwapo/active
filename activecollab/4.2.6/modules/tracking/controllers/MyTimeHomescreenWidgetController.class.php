<?php

// Build on top of homescreen widgets controller
AngieApplication::useController('homescreen_widgets', SYSTEM_MODULE);

/**
 * My time homescreen widget controller
 *
 * @package activeCollab.modules.tracking
 * @subpackage controllers
 */
class MyTimeHomescreenWidgetController extends HomescreenWidgetsController {

  /**
   * Execute before action
   */
  function __before() {
    parent::__before();

    if(!($this->active_homescreen_widget instanceof MyTimeHomescreenWidget)) {
      $this->response->operationFailed();
    } // if

    $this->response->assign('active_homescreen_widget', $this->active_homescreen_widget);
  } // __before

  /**
   * Handles weekly time flyout
   */
  function weekly_time() {
    if($this->request->isAsyncCall()) {
      if($this->active_homescreen_widget->isLoaded()) {
        $week = $this->request->get('week') == 'current' ? TrackingReport::DATE_FILTER_THIS_WEEK : TrackingReport::DATE_FILTER_LAST_WEEK;

        $per_day_week_report = $this->active_homescreen_widget->getWeekSumReport($this->logged_user, $week);
        $per_project_week_report = $this->active_homescreen_widget->getWeekReport($this->logged_user, $week);

        if(!($per_day_week_report instanceof TrackingReport) || !($per_project_week_report instanceof TrackingReport)) {
          $this->response->notFound();
        }  // if

        $per_day_week_report->setGroupBy(TrackingReport::GROUP_BY_DATE);
        $per_project_week_report->setGroupBy(TrackingReport::GROUP_BY_PROJECT);

        $per_day_records = $per_day_week_report->run($this->logged_user);
        $per_project_records = $per_project_week_report->run($this->logged_user);

        if($this->request->get('refresh')) {
          $this->response->respondWithData(array(
            'per_day_records' => $per_day_records,
            'per_project_records' => $per_project_records
          ), array('format' => 'json'));
        } else {
          $this->response->assign(array(
            'per_day_records' => $per_day_records,
            'per_project_records' => $per_project_records,
            'week_data' => $this->active_homescreen_widget->getWeekData($this->logged_user, $week),
            'selected_week' => $this->request->get('week')
          ));

          $this->renderView('weekly_time', 'my_time_homescreen_widget', TRACKING_MODULE);
        } // if
      } else {
        $this->response->notFound();
      } // if
    } else {
      $this->response->badRequest();
    } // if
  } // weekly_time

  /**
   * Handles add time flyout
   */
  function add_time() {
    if($this->request->isAsyncCall()) {
      if($this->active_homescreen_widget->isLoaded()) {
        $selected_user = $this->active_homescreen_widget->getSelectedUser() instanceof IUser ? $this->active_homescreen_widget->getSelectedUser() : $this->logged_user;

        $week = $this->request->get('week') == 'current' ? TrackingReport::DATE_FILTER_THIS_WEEK : TrackingReport::DATE_FILTER_LAST_WEEK;

        $this->response->assign(array(
          'selected_user' => $selected_user,
          'week_data' => !is_null($this->request->get('week_data')) ? $this->request->get('week_data') : $this->active_homescreen_widget->getWeekData($this->logged_user, $week),
          'day_record_date' => $this->request->get('day_record_date'),
          'add_project_time_url' => Router::assemble('project_tracking_time_records_add', array('project_slug' => '--PROJECT_ID--')),
          'add_task_time_url' => Router::assemble('project_task_tracking_time_records_add', array('project_slug' => '--PROJECT_ID--', 'task_id' => '--TASK_ID--'))
        ));

        $this->response->assign('time_record_data', array(
          'user_id' => $selected_user->getId(),
          'record_date' => ConfigOptions::getValueFor('time_first_week_day', $selected_user),
          'billable_status' => ConfigOptions::getValue('default_billable_status') ? 1 : 0
        ));

        $this->response->respondWithFragment('add_time', 'my_time_homescreen_widget', TRACKING_MODULE);
      } else {
        $this->response->notFound();
      } // if
    } else {
      $this->response->badRequest();
    } // if
  } // add_time

  /**
   * Refresh widget data
   */
  function refresh() {
    if($this->request->isAsyncCall()) {
      if($this->active_homescreen_widget->isLoaded()) {
        $this->response->respondWithData(array('records' => $this->active_homescreen_widget->run($this->logged_user)), array('format' => 'json'));
      } else {
        $this->response->notFound();
      } // if
    } else {
      $this->response->badRequest();
    } // if
  } // refresh

}