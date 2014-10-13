<?php

  // Build on top of reports module
  AngieApplication::useController('reports', REPORTS_FRAMEWORK_INJECT_INTO);

  /**
   * Project Tasks reports controller implementation
   *
   * @package activeCollab.modules.tasks
   * @subpackage controllers
   */
  class TasksReportsController extends ReportsController {
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if($this->logged_user->isProjectManager() || $this->logged_user->isPeopleManager() || $this->logged_user->isFinancialManager()) {
      	$this->response->assign(array(
      		'logged_user' => $this->logged_user,
      	));
      } else {
        $this->response->forbidden();
      } // if
    } // __construct
    
    /**
     * Show tracking report form and options
     */
    function aggregated_tasks() {
      $this->response->assign(array(
        'projects_exist' => count($this->logged_user->projects()->getIds())
      ));
    } // aggregated_tasks
    
    /**
     * Runs the ajax based chart generator
     */
    function aggregated_tasks_run() {
    	if ($this->request->isAsyncCall()) {
    		$project = $this->request->get('project_id') ? Projects::findById($this->request->get('project_id')) : null;
  		  
  		  if($project instanceof Project) {
  		    $report = new AggregatedTasksReport($project, $this->request->get('group_by'));
  		    
  		    $this->response->assign('rendered_report_result', $report->render($this->logged_user));
  		    $this->response->assign('report', $report);
  		  } else {
  		    $this->response->notFound();
  		  } // if
    	} else {
    		$this->response->badRequest();
    	} //if
    } // aggregated_tasks_run

  }