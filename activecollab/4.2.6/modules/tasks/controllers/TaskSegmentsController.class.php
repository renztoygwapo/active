<?php

  // Build on top of reports module
  AngieApplication::useController('reports', REPORTS_FRAMEWORK_INJECT_INTO);

  /**
   * Task segments controller
   *
   * @package activeCollab.modules.tasks
   * @subpackage controllers
   */
  class TaskSegmentsController extends ReportsController {

    /**
     * Selected task segment
     *
     * @var TaskSegment
     */
    protected $active_task_segment;

    /**
     * Execute before any action
     */
    function __before() {
      parent::__before();

      $this->wireframe->breadcrumbs->add('task_segments', lang('Task Segments'), Router::assemble('task_segments'));
      $this->wireframe->actions->add('add_task_segmnet', lang('New Task Segment'), Router::assemble('task_segments_add'), array(
        'onclick' => new FlyoutFormCallback('task_segment_created', array(
          'width' => 650,
        )),
        'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
      ));

      $task_segment_id = $this->request->getId('task_segment_id');
      if($task_segment_id) {
        $this->active_task_segment = TaskSegments::findById($task_segment_id);
      } // if

      if($this->active_task_segment instanceof TaskSegment) {
        $this->wireframe->breadcrumbs->add('task_segment', $this->active_task_segment->getName(), $this->active_task_segment->getViewUrl());
      } else {
        $this->active_task_segment = new TaskSegment();
      } // if

      $this->response->assign('active_task_segment', $this->active_task_segment);
    } // __before

    /**
     * List all defined segments
     */
    function index() {
      $task_segments_per_page = 50;

      if($this->request->get('paged_list')) {
        $exclude = $this->request->get('paged_list_exclude') ? explode(',', $this->request->get('paged_list_exclude')) : null;
        $timestamp = $this->request->get('paged_list_timestamp') ? (integer) $this->request->get('paged_list_timestamp') : null;

        $this->response->respondWithData(TaskSegments::getSlice($task_segments_per_page, $exclude, $timestamp));
      } else {
        $all = new TaskSegment();
        list($total, $open, $completed) = $all->countTasks($this->logged_user);

        $this->smarty->assign(array(
          'task_segments' => TaskSegments::getSlice($task_segments_per_page),
          'task_segments_per_page' => $task_segments_per_page,
          'total_task_segments' => TaskSegments::count(),
          'all_tasks' => $total,
          'all_open_tasks' => $open,
          'all_completed_tasks' => $completed,
        ));
      } // if
    } // index

    /**
     * Show task segment data
     */
    function view() {
      $this->response->notFound();
    } // getView

    /**
     * Create a new segment
     */
    function add() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() || $this->request->isSubmitted())) {
        if(TaskSegments::canAdd($this->logged_user)) {
          $task_segment_data = $this->request->post('task_segment');
          $this->response->assign('task_segment_data', $task_segment_data);

          if($this->request->isSubmitted()) {
            try {
              $this->active_task_segment->setAttributes($task_segment_data);
              $this->active_task_segment->save();

              $this->response->respondWithData($this->active_task_segment, array(
                'as' => 'task_segment'
              ));
            } catch(Exception $e) {
              $this->response->exception($e);
            } // try
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // add

    /**
     * Update an existing segment
     */
    function edit() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() || $this->request->isSubmitted())) {
        if($this->active_task_segment->isLoaded()) {
          if($this->active_task_segment->canEdit($this->logged_user)) {
            $selected_priorities = $this->active_task_segment->getSelectedPriorities();

            $task_segment_data = $this->request->post('task_segment', array(
              'name' => $this->active_task_segment->getName(),
              'milestone_filter' => $this->active_task_segment->getMilestoneFilter(),
              'milestone_names' => $this->active_task_segment->getMilestoneNames(),
              'category_filter' => $this->active_task_segment->getCategoryFilter(),
              'category_names' => $this->active_task_segment->getCategoryNames(),
              'label_filter' => $this->active_task_segment->getLabelFilter(),
              'label_names' => $this->active_task_segment->getLabelNames(),
              'priority_filter' => $this->active_task_segment->getPriorityFilter(),
              'priority_lowest' => is_array($selected_priorities) && in_array(PRIORITY_LOWEST, $selected_priorities),
              'priority_low' => is_array($selected_priorities) && in_array(PRIORITY_LOW, $selected_priorities),
              'priority_normal' => is_array($selected_priorities) && in_array(PRIORITY_NORMAL, $selected_priorities),
              'priority_high' => is_array($selected_priorities) && in_array(PRIORITY_HIGH, $selected_priorities),
              'priority_highest' => is_array($selected_priorities) && in_array(PRIORITY_HIGHEST, $selected_priorities),
            ));
            $this->response->assign('task_segment_data', $task_segment_data);

            if($this->request->isSubmitted()) {
              try {
                $this->active_task_segment->setAttributes($task_segment_data);
                $this->active_task_segment->save();

                $this->response->respondWithData($this->active_task_segment, array('as' => 'task_segment'));
              } catch(Exception $e) {
                $this->response->exception($e);
              } // try
            } // if
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // edit

    /**
     * Delete a segment
     */
    function delete() {
      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted()) {
        if($this->active_task_segment->isLoaded()) {
          if($this->active_task_segment->canDelete($this->logged_user)) {
            try {
              $this->active_task_segment->delete();
              $this->response->respondWithData($this->active_task_segment, array('as' => 'task_segment'));
            } catch(Exception $e) {
              $this->response->exception($e);
            } // try
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // delete

  }