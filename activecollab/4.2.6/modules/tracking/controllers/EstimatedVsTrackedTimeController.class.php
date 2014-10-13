<?php

  // Build on top of reports module
  AngieApplication::useController('reports', REPORTS_FRAMEWORK_INJECT_INTO);


  /**
   * Estimated vs tracked time controller
   *
   * @package activeCollab.modules.tracking
   * @subpackage controllers
   */
  class EstimatedVsTrackedTimeController extends ReportsController {

    /**
     * Estiamted vs tracked time report
     */
    function estimated_vs_tracked_time() {
      $this->response->assign('projects_exist', count(Projects::findIdsByUser($this->logged_user, true)));
    } // estimated_vs_tracked_time

    /**
     * Run estimated vs tracked time report
     */
    function estimated_vs_tracked_time_run() {
      if($this->request->isAsyncCall()) {
        $project = $this->request->get('project_id') ? Projects::findById($this->request->get('project_id')) : null;

        if($project instanceof Project) {
          $assignments = null;

          try {
            $filter = new AssignmentFilter();
            $filter->filterByProjects(array($project->getId()));
            $filter->setIncludeTrackingData(true);
            $filter->setGroupBy($this->request->get('group_by'));
            $filter->setIncludeAllProjects(true);

            $assignments = $filter->run($this->logged_user);
          } catch(DataFilterConditionsError $e) {
            $assignments = null;
          } catch(Exception $e) {
            $this->response->exception($e);
          } // try

          $this->response->assign(array(
            'assignments' => $assignments,
            'labels' => Labels::getIdDetailsMap('AssignmentLabel'),
            'project_slugs' => Projects::getIdSlugMap(),
            'task_url' => AngieApplication::isModuleLoaded('tasks') ? Router::assemble('project_task', array('project_slug' => '--PROJECT_SLUG--', 'task_id' => '--TASK_ID--')) : '#',
            'job_types' => JobTypes::getIdNameMap(null, JOB_TYPE_INACTIVE),
          ));
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // estimated_vs_tracked_time_run

  }