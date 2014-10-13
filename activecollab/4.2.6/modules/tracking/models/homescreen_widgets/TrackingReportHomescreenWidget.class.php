<?php

  /**
   * Tracking report home screen widget
   * 
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  abstract class TrackingReportHomescreenWidget extends HomescreenWidget {
    
    /**
     * Return name of the group where this widget needs to be added to
     * 
     * @return string
     */
    function getGroupName() {
      return lang('Time and Expenses');
    } // getGroupName

    /**
     * Prepare report instance based on given criteria
     *
     * @return TrackingReport
     */
    function getReport() {
      $report = new TrackingReport();

      $days = $this->getDaysFilter();

      if(!in_array($days, array(7, 15, 30))) {
        $days = 30;
      } // if

      $report->filterByRange(DateValue::makeFromString("-$days days"), DateValue::now());

      if($this->getUserFilter() == TrackingReport::USER_FILTER_LOGGED_USER) {
        $report->setUserFilter(TrackingReport::USER_FILTER_LOGGED_USER);
      } else {
        $report->setUserFilter(TrackingReport::USER_FILTER_ANYBODY);
        $report->setSumByUser(true);
      } // if

      $report->setBillableStatusFilter($this->getBillableStatusFilter());
      $report->setGroupBy(TrackingReport::GROUP_BY_DATE);

      return $report;
    } // getReport
    
    /**
     * Return path to the view file that's used to render result
     * 
     * @return string
     */
    function getResultsViewPath() {
      return get_view_path('tracking_report', 'homescreen_widgets', TRACKING_MODULE, AngieApplication::INTERFACE_DEFAULT);
    } // getResultsViewPath
    
    /**
     * Return widget body
     * 
     * @param IUser $user
     * @param string $widget_id
     * @param string $column_wrapper_class
     * @return string
     * @throws Exception
     */
    function renderBody(IUser $user, $widget_id, $column_wrapper_class = null) {
      $report = $this->getReport();
      
      if($report instanceof TrackingReport) {
        $view = SmartyForAngie::getInstance()->createTemplate($this->getResultsViewPath());
        
        try {
          $records = $report->run($user);
        } catch(DataFilterConditionsError $e) {
          $records = null;
        } catch(Exception $e) {
          throw $e;
        } // try
      
        $view->assign(array(
          'widget' => $this, 
          'user' => $user, 
          'report' => $report, 
          'records' =>  $records, 
          'currencies' => Currencies::getIdDetailsMap(), 
        ));
        
        return $view->fetch();
      } else {
        return '';
      } // if
    } // renderBody

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
      return AngieApplication::getViewPath('tracking_report_options', 'homescreen_widgets', TRACKING_MODULE, AngieApplication::INTERFACE_DEFAULT);
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
        'billable_status_filter' => $this->getBillableStatusFilter(),
        'days_filter' => $this->getDaysFilter(),
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
        $this->setUserFilter($attributes['user_filter']);
      } // if

      if(array_key_exists('billable_status_filter', $attributes)) {
        $this->setBillableStatusFilter($attributes['billable_status_filter']);
      } // if

      if(array_key_exists('days_filter', $attributes)) {
        $this->setDaysFilter($attributes['days_filter']);
      } // if

      parent::setAttributes($attributes);
    } // setAttributes

    /**
     * Return user filter value
     *
     * @return string
     */
    function getUserFilter() {
      return $this->getAdditionalProperty('user_filter', TrackingReport::USER_FILTER_ANYBODY);
    } // getUserFilter

    /**
     * Return user filter value
     *
     * @param string $value
     * @return string
     * @throws InvalidParamError
     */
    function setUserFilter($value) {
      if($value == TrackingReport::USER_FILTER_ANYBODY || $value == TrackingReport::USER_FILTER_LOGGED_USER) {
        return $this->setAdditionalProperty('user_filter', $value);
      } else {
        throw new InvalidParamError('value', $value, '$value should be either Anyone or Logged User');
      } // if
    } // setUserFilter

    /**
     * Return billable status filter value
     *
     * @return string
     */
    function getBillableStatusFilter() {
      return $this->getAdditionalProperty('billable_status_filter', TrackingReport::BILLABLE_FILTER_ALL);
    } // getBillableStatusFilter

    /**
     * Set billable status filter
     *
     * @param string $value
     * @return string
     */
    function setBillableStatusFilter($value) {
      return $this->setAdditionalProperty('billable_status_filter', $value);
    } // setBillableStatusFilter

    /**
     * Return days filter value
     *
     * @return integer
     */
    function getDaysFilter() {
      return $this->getAdditionalProperty('days_filter', 7);
    } // getDaysFilter

    /**
     * Set days filter value
     *
     * @param integer $value
     * @return integer
     */
    function setDaysFilter($value) {
      $value = (integer) $value;

      if($value == 15 || $value == 30) {
        return $this->setAdditionalProperty('days_filter', $value);
      } else {
        return $this->setAdditionalProperty('days_filter', 7);
      } // if
    } // setDaysFilter
  
  }