<?php

  // Build on top of data filters controller
  AngieApplication::useController('data_filters', SYSTEM_MODULE);

  /**
   * Workload report controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class WorkloadReportsController extends DataFiltersController {
  	
  	/**
     * Return filter class managed by this controller
     *
     * @return string
     */
    function getFilterType() {
      return 'WorkloadReport';
    } // getFilterType

    /**
     * Return filter ID variable name
     *
     * @return mixed
     */
    function getFilterIdVariableName() {
      return 'workload_report_id';
    } // getFilterIdVariableName

    /**
     * Execute before other controller actions
     */
    function __before() {
      parent::__before();

      if(!$this->logged_user->isProjectManager()) {
        $this->response->notFound();
      } // if
    } // __before

    /**
     * Show workload report form and options
     */
    function index() {
      parent::index();

      $this->smarty->assign(array(
      	'users' => Users::getForSelect($this->logged_user),
        'companies' => Companies::getIdNameMap(null, STATE_VISIBLE),
        'projects' => Projects::getIdNameMap($this->logged_user, STATE_ARCHIVED, null, null, true),
        'active_projects' => Projects::getIdNameMap($this->logged_user, STATE_VISIBLE, null, null, true), // We need this so we can group projects in the picker
        'project_users' => Projects::getProjectUsersIdMap(),
        'project_categories' => Categories::getIdNameMap(null, 'ProjectCategory'),
        'project_slugs' => Projects::getIdSlugMap(),
        'reassign_url' => Router::assemble('project_task_assignees', array('project_slug' => '--PROJECT_SLUG--', 'task_id' => '--TASK_ID--')),
        'task_url' => AngieApplication::isModuleLoaded('tasks') ? Router::assemble('project_task', array('project_slug' => '--PROJECT_SLUG--', 'task_id' => '--TASK_ID--')) : '',
        'task_subtask_url' => AngieApplication::isModuleLoaded('tasks') ? Router::assemble('project_task_subtask', array('project_slug' => '--PROJECT_SLUG--', 'task_id' => '--TASK_ID--', 'subtask_id' => '--SUBTASK_ID--')) : '',
        'todo_url' => AngieApplication::isModuleLoaded('todo') ? Router::assemble('project_todo_list', array('project_slug' => '--PROJECT_SLUG--', 'todo_list_id' => '--TODO_LIST_ID--')) : '',
        'todo_subtask_url' => AngieApplication::isModuleLoaded('todo') ? Router::assemble('project_todo_list_subtask', array('project_slug' => '--PROJECT_SLUG--', 'todo_list_id' => '--TODO_LIST_ID--', 'subtask_id' => '--SUBTASK_ID--')) : '',
        'labels' => Labels::getIdDetailsMap('AssignmentLabel')
      ));
    } // index
    
    /**
     * Run workload report
     */
    function run() {
      if($this->request->isPrintCall() || ($this->request->isWebBrowser() && $this->request->isAsyncCall())) {
        $filter = new WorkloadReport();
        $filter->setAttributes($this->request->get('filter'));

        $results = null;

        try {
          $results = $filter->run($this->logged_user);
        } catch(DataFilterConditionsError $e) {
          $results = null;
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try

        if($this->request->isPrintCall()) {
          $this->response->assign(array(
            'filter' => $filter,
            'result' => $results,
          ));
        } else {
          if($results) {
          	$this->response->respondWithMap(array(
          		array(
          			'assignees' => $results,
                'real_assignees' => $filter->getRealAssignees($this->logged_user), // Override assignments filter user selection result
          			'timespan' => $filter->getTimespan($this->logged_user),
          			'offset' => $filter->getOffset()
          		)
          	));
          } else {
            $this->response->ok();
          }//if
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // run

  }