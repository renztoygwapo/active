<?php

  /**
   * Base task analyzer report class
   *
   * @package activeCollab.modules.tasks
   * @subpackage models
   */
  abstract class TasksAnalyzerReport extends ProjectObjectsDataFilter {

    // Task segment filter
    const TASKS_SEGMENT_FILTER_ANY = 'any';
    const TASKS_SEGMENT_FILTER_SELECTED = 'selected';

    // Task segment filters
    const SEGMENT_FILTER_ANY = 'any';
    const SEGMENT_FILTER_SELECT = 'selected';

    // Date filters
    const DATE_FILTER_ANY = 'any';
    const DATE_FILTER_SELECTED_RANGE = 'selected_range';

    /**
     * Return conditions
     *
     * @param IUser $user
     * @return string
     * @throws DataFilterConditionsError
     */
    function getConditions(IUser $user) {
      $project_objects_table = TABLE_PREFIX . 'project_objects';

      $conditions = array(
        DB::prepare("$project_objects_table.type = 'Task' AND $project_objects_table.state >= ?", STATE_ARCHIVED),
      );

      $project_ids = Projects::getProjectIdsByDataFilter($this, $user);

      if($project_ids) {
        $conditions[] = DB::prepare("$project_objects_table.project_id IN (?)", $project_ids);
      } // if

      if($this->getTasksSegmentFilter() == self::TASKS_SEGMENT_FILTER_SELECTED) {
        $task_segment_id = $this->getTasksSegmentId();

        $task_segment = $task_segment_id ? TaskSegments::findById($task_segment_id) : null;

        if($task_segment instanceof TaskSegment) {
          if($task_segment->getConditions($user, false)) {
            $conditions[] = $task_segment->getConditions($user, false);
          } //if
        } else {
          throw new DataFilterConditionsError('tasks_segment_filter', $this->getTasksSegmentFilter(), $task_segment_id);
        } // if
      } // if

      return implode(' AND ', $conditions);
    } // getConditions

    // ---------------------------------------------------
    //  Getters and Setters
    // ---------------------------------------------------

    /**
     * Set attributes
     *
     * @param array $attributes
     */
    function setAttributes($attributes) {
      if(isset($attributes['tasks_segment_filter'])) {
        if($attributes['tasks_segment_filter'] == self::TASKS_SEGMENT_FILTER_SELECTED && $attributes['tasks_segment_id']) {
          $this->filterByTasksSegment($attributes['tasks_segment_id']);
        } else {
          $this->setTasksSegmentFilter(self::TASKS_SEGMENT_FILTER_ANY);
        } // if
      } // if

      if(isset($attributes['date_filter'])) {
        switch($attributes['date_filter']) {
          case self::DATE_FILTER_SELECTED_RANGE:
            $this->filterByRange($attributes['date_from'], $attributes['date_to']);
            break;
          default:
            $this->setDateFilter($attributes['date_filter']);
        } // switch
      } // if

      if(isset($attributes['project_filter'])) {
        if($attributes['project_filter'] == Projects::PROJECT_FILTER_CATEGORY) {
          $this->filterByProjectCategory(array_var($attributes, 'project_category_id'));
        } elseif($attributes['project_filter'] == Projects::PROJECT_FILTER_CLIENT) {
          $this->filterByProjectClient(array_var($attributes, 'project_client_id'));
        } elseif($attributes['project_filter'] == Projects::PROJECT_FILTER_SELECTED) {
          $this->filterByProjects(array_var($attributes, 'project_ids'));
        } else {
          $this->setProjectFilter($attributes['project_filter']);
        } // if
      } // if

      parent::setAttributes($attributes);
    } // setAttributes

    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
      $result = parent::describe($user, $detailed, $for_interface);

      $result['tasks_segment_filter'] = $this->getTasksSegmentFilter();
      if($result['tasks_segment_filter'] == self::TASKS_SEGMENT_FILTER_SELECTED) {
        $result['tasks_segment_id'] = $this->getTasksSegmentFilter();
      } // if

      $result['date_filter'] = $this->getDateFilter();
      if($result['date_filter'] == self::DATE_FILTER_SELECTED_RANGE) {
        list($from, $to) = $this->getDateFilterSelectedRange();

        $result['date_from'] = $from;
        $result['date_to'] = $to;
      } // if

      // Project filter
      $result['project_filter'] = $this->getProjectFilter();
      switch($result['project_filter']) {
        case TrackingReport::PROJECT_FILTER_CATEGORY:
          $result['project_category_id'] = $this->getProjectCategoryId();
          break;
        case TrackingReport::PROJECT_FILTER_CLIENT:
          $result['project_client_id'] = $this->getProjectClientId();
          break;
        case TrackingReport::PROJECT_FILTER_SELECTED:
          $result['project_ids'] = $this->getProjectIds();
          break;
      } // switch

      return $result;
    } // describe

    // ---------------------------------------------------
    //  Filters
    // ---------------------------------------------------

    /**
     * Return task segment filter value
     *
     * @return string
     */
    function getTasksSegmentFilter() {
      return $this->getAdditionalProperty('tasks_segment_filter', self::TASKS_SEGMENT_FILTER_ANY);
    } // getTasksSegmentFilter

    /**
     * Set task segment filter value
     *
     * @param string $value
     * @return string
     */
    function setTasksSegmentFilter($value) {
      return $this->setAdditionalProperty('tasks_segment_filter', $value);
    } // setTasksSegmentFilter

    /**
     * Set task segment filter by task segment
     *
     * @param array $task_segment_id
     */
    function filterByTasksSegment($task_segment_id) {
      if($task_segment_id) {
        $this->setTasksSegmentFilter(self::TASKS_SEGMENT_FILTER_SELECTED);

        $this->setAdditionalProperty('tasks_segment_id', (integer) $task_segment_id);
      } else {
        throw new InvalidParamError('tasks_segment_id', $task_segment_id, 'Task segment ID is required');
      } // if
    } // filterByTasksSegment

    /**
     * Return selected task segment ID-s
     *
     * @return array
     */
    function getTasksSegmentId() {
      return $this->getAdditionalProperty('tasks_segment_id');
    } // getTasksSegmentId

    /**
     * Return date filter value
     *
     * @return string
     */
    function getDateFilter() {
      return $this->getAdditionalProperty('date_filter', self::DATE_FILTER_ANY);
    } // getDateFilter

    /**
     * Set date filter to a given $value
     *
     * @param string $value
     * @return string
     */
    function setDateFilter($value) {
      return $this->setAdditionalProperty('date_filter', $value);
    } // setDateFilter

    /**
     * Filter records by date range
     *
     * @param string $from
     * @param string $to
     */
    function filterByRange($from, $to) {
      $this->setDateFilter(self::DATE_FILTER_SELECTED_RANGE);
      $this->setAdditionalProperty('date_filter_from', (string) $from);
      $this->setAdditionalProperty('date_filter_to', (string) $to);
    } // filterByRange

    /**
     * Return selected range for date filter
     *
     * @return array
     */
    function getDateFilterSelectedRange() {
      $from = $this->getAdditionalProperty('date_filter_from');
      $to = $this->getAdditionalProperty('date_filter_to');

      return $from && $to ? array(new DateValue($from), new DateValue($to)) : array(null, null);
    } // getDateFilterSelectedRange

  }