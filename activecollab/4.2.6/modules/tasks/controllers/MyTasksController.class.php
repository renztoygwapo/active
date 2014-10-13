<?php

  // Build on top of backend controller
  AngieApplication::useController('backend', SYSTEM_MODULE);

  /**
   * My tasks controller
   *
   * @package activeCollab.modules.tasks
   * @subpackage controllers
   */
  class MyTasksController extends BackendController {

    /**
     * Execute before any action
     */
    function __before() {
      parent::__before();

      if ($this->logged_user instanceof Client) {
        $this->response->notFound();
      } // if

      if($this->logged_user instanceof Client) {
        $this->response->notFound();
      } // if

      $this->prepareTabs($this->logged_user, 'my_tasks');

      $this->wireframe->breadcrumbs->add('my_tasks', lang('My Tasks'), Router::assemble('my_tasks'));
    } // __before

    /**
     * Refresh my tasks list
     */
    function refresh() {
      if ($this->request->isAsyncCall()) {
        $filter = Tasks::getMyTasksFilter($this->logged_user);

        try {
          $assignments = $filter->run($this->logged_user);
        } catch (DataFilterConditionsError $e) {
          $assignments = null;
        } // try

        if ($assignments) {
          $filter->resultToMap($assignments);
        } // if

        try {
          $late_assignments = Tasks::getMyLateTasksFilter($this->logged_user)->run($this->logged_user);
        } catch(DataFilterConditionsError $e) {
          $late_assignments = null;
        } // if

        if($late_assignments && isset($late_assignments['all'])) {
          $filter->resultToMap($late_assignments);

          $late_assignments = $late_assignments['all']['assignments'];
        } // if

        $this->response->respondWithData(array(
          'assignments' => JSON::valueToMap($assignments),
          'late_assignments' => $late_assignments,
          'labels' => Labels::getIdDetailsMap('AssignmentLabel', true),
        ));

        //$this->response->respondWithMap($assignments);
      } else {
        $this->response->badRequest();
      } // if
    } // refresh

    /**
     * Show and process task settings
     */
    function settings() {
      $labels = array();

      foreach(Labels::getIdNameMap('AssignmentLabel', true) as $v) {
        $labels[] = $v;
      } // foreach

      $this->response->assign(array(
        'settings_data' => ConfigOptions::getValueFor(array('my_tasks_labels_filter', 'my_tasks_labels_filter_data'), $this->logged_user),
        'labels' => $labels,
      ));

      if($this->request->isSubmitted()) {
        try {
          $settings_data = $this->request->post('settings');

          $labels_filter = array_var($settings_data, 'my_tasks_labels_filter');

          if($labels_filter != AssignmentFilter::LABEL_FILTER_ANY && $labels_filter != AssignmentFilter::LABEL_FILTER_SELECTED && $labels_filter != AssignmentFilter::LABEL_FILTER_NOT_SELECTED) {
            $labels_filter = AssignmentFilter::LABEL_FILTER_ANY;
          } // if

          if($labels_filter != AssignmentFilter::LABEL_FILTER_ANY) {
            if($labels_filter == AssignmentFilter::LABEL_FILTER_SELECTED) {
              $labels_filter_data = trim(array_var($settings_data, 'only_show_labels'));
            } else {
              $labels_filter_data = trim(array_var($settings_data, 'ignore_labels'));
            } // if

            if(empty($labels_filter_data)) {
              throw new ValidationErrors(array(
                'my_tasks_labels_filter_data' => lang('Please select labels')
              ));
            } // if
          } else {
            $labels_filter_data = null;
          } // if

          ConfigOptions::setValueFor(array(
            'my_tasks_labels_filter' => $labels_filter,
            'my_tasks_labels_filter_data' => $labels_filter_data,
          ), $this->logged_user);

          $this->response->ok();
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } // if
    } // settings

    /**
     * List recently completed tasks
     */
    function completed() {
      $filter = new AssignmentFilter();

      $filter->completedAfterDate(DateValue::makeFromString('-31 days'));
      $filter->filterByUsers(array($this->logged_user->getId()), false);
      $filter->setIncludeSubtasks(true);
      $filter->setIncludeOtherAssignees(true);

      try {
        $assignments = $filter->run($this->logged_user);
      } catch(DataFilterConditionsError $e) {
        $assignments = null;
      } // try

      if($assignments) {
        $flat_list = array();
        $user_id = $this->logged_user->getId();
        $user_gmt_offset = get_user_gmt_offset($this->logged_user);

        $prepare_group = function($key, DateTimeValue $timestamp) use (&$flat_list, $user_gmt_offset) {
          if(!isset($flat_list[$key])) {
            if($timestamp->isToday($user_gmt_offset)) {
              $label = lang('Today');
            } elseif($timestamp->isYesterday($user_gmt_offset)) {
              $label = lang('Yesterday');
            } else {
              $label = $timestamp->formatDateForUser(null, 0);
            } // if

            $flat_list[$key] = array(
              'label' => $label,
              'assignments' => array(),
            );
          } // if
        };

        foreach($assignments['all']['assignments'] as $assignment) {
          if($assignment['completed_on'] instanceof DateTimeValue && ($assignment['assignee_id'] == $user_id || (is_array($assignment['other_assignees']) && in_array($user_id, $assignment['other_assignees'])))) {
            $key = date(DATE_MYSQL, $assignment['completed_on']->getTimestamp());

            $prepare_group($key, $assignment['completed_on']);

            $flat_list[$key]['assignments']['task-' . $assignment['id']] = $assignment;
          } // if

          if($assignment['subtasks'] && is_foreachable($assignment['subtasks'])) {
            foreach($assignment['subtasks'] as $subtask) {
              if($subtask['completed_on'] instanceof DateTimeValue && $subtask['assignee_id'] == $user_id) {
                $key = date(DATE_MYSQL, $subtask['completed_on']->getTimestamp());

                $prepare_group($key, $subtask['completed_on']);

                $subtask['parent'] = array(
                  'id' => $assignment['id'],
                  'name' => $assignment['name'],
                  'permalink' => $assignment['permalink'],
                  'task_id' => $assignment['task_id'],
                );
                $subtask['project'] = $assignment['project'];

                $flat_list[$key]['assignments']['subtask-' . $subtask['id']] = $subtask;
              } // if
            } // foreach
          } // if
        } // foreach

        foreach($flat_list as $k => $v) {
          uasort($flat_list[$k]['assignments'], function($a, $b) {
            return strcmp($a['completed_on']->toMySQL(), $b['completed_on']->toMySQL());
          });
        } // foreach

        krsort($flat_list);

        $assignments = $flat_list;
      } // if

      $this->response->assign(array(
        'user_id' => $this->logged_user->getId(),
        'assignments' => $assignments,
        'labels' => Labels::getIdDetailsMap('AssignmentLabel', true),
      ));
    } // completed

    /**
     * List unassigned tasks
     */
    function unassigned() {
      $filter = new AssignmentFilter();

      $filter->setCompletedOnFilter(DataFilter::DATE_FILTER_IS_NOT_SET);
      $filter->setProjectFilter(Projects::PROJECT_FILTER_ACTIVE);
      $filter->setUserFilter(AssignmentFilter::USER_FILTER_NOT_ASSIGNED);
      $filter->setGroupBy(AssignmentFilter::GROUP_BY_PROJECT);

      try {
        $assignments = $filter->run($this->logged_user);
      } catch(DataFilterConditionsError $e) {
        $assignments = null;
      } // try

      $this->response->assign(array(
        'user_id' => $this->logged_user->getId(),
        'assignments' => $assignments,
        'labels' => Labels::getIdDetailsMap('AssignmentLabel', true),
      ));
    } // unassigned

  }