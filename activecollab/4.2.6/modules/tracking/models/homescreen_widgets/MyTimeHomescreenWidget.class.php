<?php

  /**
   * Show tracked time by active or selected user for current and previous week
   * 
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  class MyTimeHomescreenWidget extends HomescreenWidget {

    /**
     * Return name of the group where this widget needs to be added to
     *
     * @return string
     */
    function getGroupName() {
      return lang('Time & Expenses');
    } // getGroupName
    
    /**
     * Return widget name
     * 
     * @return string
     */
    function getName() {
      return lang('My Time');
    } // getName
    
    /**
     * Return widget description
     * 
     * @return string
     */
    function getDescription() {
      return lang('Show tracked time by active or selected user for current and previous week');
    } // getDescription

    /**
     * Return widget body
     *
     * @param IUser $user
     * @param string $widget_id
     * @param string $column_wrapper_class
     * @return string
     */
    function renderBody(IUser $user, $widget_id, $column_wrapper_class = null) {
      $view = SmartyForAngie::getInstance()->createTemplate($this->getResultsViewPath());

      try {
        $records = $this->run($user);
      } catch(DataFilterConditionsError $e) {
        $records = null;
      } catch(Exception $e) {
        throw $e;
      } // try

      $view->assign(array(
        'widget' => $this,
        'user' => $user,
        'records' =>  $records
      ));

      return $view->fetch();
    } // renderBody

    /**
     * Return path to the view file that's used to render result
     *
     * @return string
     */
    function getResultsViewPath() {
      return get_view_path('my_time', 'homescreen_widgets', TRACKING_MODULE, AngieApplication::INTERFACE_DEFAULT);
    } // getResultsViewPath

    /**
     * Run reports
     *
     * @param IUser $user
     * @return array
     */
    function run(IUser $user) {
      if($user instanceof User) {
        $previous_week_sum_records = $this->getWeekSumReport($user, TrackingReport::DATE_FILTER_LAST_WEEK)->run($user);
        $current_week_sum_records = $this->getWeekSumReport($user, TrackingReport::DATE_FILTER_THIS_WEEK)->run($user);

        return array(
          'previous_week' => $previous_week_sum_records['all']['records'],
          'current_week' => $current_week_sum_records['all']['records']
        );
      } else {
        throw new InvalidParamError('user', $user, '$user must be instance of User class');
      } // if
    } // run

    /**
     * Get summarized week report instance based on given criteria
     *
     * @param IUser $user
     * @param string $date_filter
     * @return TrackingReport
     */
    function getWeekSumReport(IUser $user, $date_filter) {
      $week_report = $this->getWeekReport($user, $date_filter);

      if($week_report instanceof TrackingReport) {
        $week_report->setSumByUser(true);
        return $week_report;
      } else {
        throw new InvalidParamError('week_report', $week_report, '$week_report must be instance of TrackingReport class');
      } // if
    } // getWeekSumReport

    /**
     * Get week report instance based on given criteria
     *
     * @param IUser $user
     * @param string $date_filter
     * @return TrackingReport
     */
    function getWeekReport(IUser $user, $date_filter) {
      $report = new TrackingReport();

      $today = new DateValue(time() + get_user_gmt_offset($user));
      if($date_filter == TrackingReport::DATE_FILTER_LAST_WEEK) {
        $today = $today->advance(-604800, false);
      } // if

      $first_week_day = (integer) ConfigOptions::getValueFor('time_first_week_day', $user);

      $report->filterByRange($today->beginningOfWeek($first_week_day), $today->endOfWeek($first_week_day));

      if($this->getUserFilter() == TrackingReport::USER_FILTER_SELECTED) {
        $report->filterByUsers(array($this->getUserFilterSelectedUser()));
      } else {
        $report->setUserFilter(TrackingReport::USER_FILTER_LOGGED_USER);
      } // if

      $report->setTypeFilter(TrackingReport::TYPE_FILTER_TIME);

      return $report;
    } // getWeekReport

    /**
     * Get week data
     *
     * @param IUser $user
     * @param string $date_filter
     * @return array
     */
    function getWeekData(IUser $user, $date_filter) {
      $today = new DateValue(time() + get_user_gmt_offset($user));
      if($date_filter == TrackingReport::DATE_FILTER_LAST_WEEK) {
        $today = $today->advance(-604800, false);
      } // if

      $week_data = array();

      DateValue::iterateWeekly($today, $today, function(DateTimeValue $week_start, DateTimeValue $week_end) use (&$week_data) {
        $week_start = new DateValue($week_start->getTimestamp());
        $week_end = new DateValue($week_end->getTimestamp());

        $week_data = array(
          'week_start' => $week_start->format('M jS'),
          'week_end' => $week_end->format('M jS'),
          'week_day_1' => $week_start->advance(86400 * 0, false)->toMySQL(),
          'week_day_2' => $week_start->advance(86400 * 1, false)->toMySQL(),
          'week_day_3' => $week_start->advance(86400 * 2, false)->toMySQL(),
          'week_day_4' => $week_start->advance(86400 * 3, false)->toMySQL(),
          'week_day_5' => $week_start->advance(86400 * 4, false)->toMySQL(),
          'week_day_6' => $week_start->advance(86400 * 5, false)->toMySQL(),
          'week_day_7' => $week_start->advance(86400 * 6, false)->toMySQL()
        );
      }, (integer) ConfigOptions::getValueFor('time_first_week_day', $user));

      return $week_data;
    } // getWeekData

    // ---------------------------------------------------
    //  Options
    // ---------------------------------------------------

    /**
     * Returns true if this widget has additional options
     *
     * @return boolean
     */
    function hasOptions() {
      return true;
    } // hasOptions

    /**
     * Render widget options form section
     *
     * @param IUser $user
     * @return string
     */
    function renderOptions(IUser $user) {
      $view = SmartyForAngie::getInstance()->createTemplate($this->getOptionsViewPath());

      $view->assign(array(
        'widget' => $this,
        'user' => $user,
        'widget_data' => $this->getOptionsViewWidgetData(),
      ));

      return $view->fetch();
    } // renderOptions

    /**
     * Return options view path
     *
     * @return string
     */
    protected function getOptionsViewPath() {
      return AngieApplication::getViewPath('my_time_options', 'homescreen_widgets', TRACKING_MODULE, AngieApplication::INTERFACE_DEFAULT);
    } // getOptionsViewPath

    /**
     * Return options view widget data
     *
     * @return array
     */
    protected function getOptionsViewWidgetData() {
      return array(
        'caption' => $this->getCaption(),
        'user_filter' => $this->getUserFilter(),
        'selected_user_id' => $this->getUserFilterSelectedUser()
      );
    } // getOptionsViewWidgetData

    // ---------------------------------------------------
    //  Attributes
    // ---------------------------------------------------

    /**
     * Return true if this widget uses caption field
     *
     * @return bool
     */
    function hasCaption() {
      return true;
    } // hasCaption

    /**
     * Bulk set widget attributes
     *
     * @param array $attributes
     */
    function setAttributes($attributes) {
      if(array_key_exists('user_filter', $attributes)) {
        if($attributes['user_filter'] == TrackingReport::USER_FILTER_SELECTED) {
          $this->filterByUser(array_var($attributes, 'selected_user_id'));
        } else {
          $this->setUserFilter($attributes['user_filter']);
        } // if
      } // if

      parent::setAttributes($attributes);
    } // setAttributes

    /**
     * Return user filter value
     *
     * @return string
     */
    function getUserFilter() {
      return $this->getAdditionalProperty('user_filter', TrackingReport::USER_FILTER_LOGGED_USER);
    } // getUserFilter

    /**
     * Set user filter value
     *
     * @param string $value
     * @return string
     */
    function setUserFilter($value) {
      return $this->setAdditionalProperty('user_filter', $value);
    } // setUserFilter

    /**
     * Set selected user filter for selected user ID
     *
     * @param integer $user_id
     */
    function filterByUser($user_id) {
      $this->setUserFilter(TrackingReport::USER_FILTER_SELECTED);
      $this->setAdditionalProperty('selected_user_id', (integer) $user_id);
    } // filterByUser

    /**
     * Return selected user ID
     *
     * @return integer
     */
    function getUserFilterSelectedUser() {
      return $this->getAdditionalProperty('selected_user_id');
    } // getUserFilterSelectedUser

    /**
     * Return selected user instance
     *
     * @return IUser
     */
    function getSelectedUser() {
      if($this->getUserFilter() == TrackingReport::USER_FILTER_SELECTED) {
        return Users::findById((integer) $this->getUserFilterSelectedUser());
      } else {
        return null;
      } // if
    } // getSelectedUser
    
  }