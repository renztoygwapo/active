<?php

  /**
   * Framework level activity logs controller implementation
   *
   * @package angie.frameworks.activity_logs
   * @subpackage controllers
   */
  class FwActivityLogsController extends Controller {

    /**
     * Show activities for given object
     *
     * @var ApplicationObject
     */
    protected $show_activities_in;

    /**
     * Show activities by given user
     *
     * @var IUser
     */
    protected $show_activities_by;

    /**
     * Render activities
     */
    function activity_log() {
      if($this->request->isApiCall()) {
        $result = array();

        if($this->show_activities_in instanceof ApplicationObject) {
          ActivityLogs::populateApiResponseFromActivities($result, $this->logged_user, ActivityLogs::findRecentIn($this->logged_user, $this->show_activities_in));
        } elseif($this->show_activities_by instanceof IUser) {
          ActivityLogs::populateApiResponseFromActivities($result, $this->logged_user, ActivityLogs::findRecentBy($this->logged_user, $this->show_activities_by));
        } else {
          ActivityLogs::populateApiResponseFromActivities($result, $this->logged_user, ActivityLogs::findRecent($this->logged_user));
        } // if

        if(count($result) < 1) {
          $result = null;
        } // if

        $this->response->respondWithData($result, array('as' => 'activity_logs'));
      } elseif($this->request->isMobileDevice()) {
        $this->wireframe->breadcrumbs->add('backend_activity_log', lang('Recent Activities'), Router::assemble('backend_activity_log'));
        $this->response->assign('activity_logs', ActivityLogs::findRecent($this->logged_user));
      } else {
        $this->response->badRequest();
      } // if
    } // activity_log

    /**
     * Render activities RSS
     */
    function activity_log_rss() {
      if($this->logged_user->isFeedUser()) {
        require_once ANGIE_PATH . '/classes/feed/init.php';

        // Render RSS in given object
        if($this->show_activities_in instanceof ApplicationObject) {
          $feed = new Feed(lang(":name Recent Activities", array(
            'name' => $this->show_activities_in->getName()
          )), ROOT_URL);

          $feed->setDescription(lang(":name Recent Activities Feed", array(
            'name' => $this->show_activities_in->getName()
          )));

          ActivityLogs::populateFeedWithActivities($feed, $this->logged_user, ActivityLogs::findRecentIn($this->logged_user, $this->show_activities_in));

        // Render RSS for selected user
        } elseif($this->show_activities_by instanceof IUser) {
          $feed = new Feed(lang(":user's Recent Activities", array(
            'user' => $this->show_activities_by->getFirstName(true)
          )), ROOT_URL);

          $feed->setDescription(lang(":user's Recent Activities Feed", array(
            'user' => $this->show_activities_by->getFirstName(true)
          )));

          ActivityLogs::populateFeedWithActivities($feed, $this->logged_user, ActivityLogs::findRecentBy($this->logged_user, $this->show_activities_by));

        // Global feed
        } else {
          $title = ConfigOptions::getValue('identity_name');

          if($title) {
            $title .= ' / ' . lang('Recent Activities');
          } else {
            $title = lang('Recent Activities');
          } // if

          $feed = new Feed($title, ROOT_URL);
          $feed->setDescription(lang('Global Recent Activities Feed'));

          ActivityLogs::populateFeedWithActivities($feed, $this->logged_user, ActivityLogs::findRecent($this->logged_user));
        } // if

        print render_rss_feed($feed);
        die();
      } else {
        $this->response->forbidden();
      } // if
    } // activity_log_rss

  }