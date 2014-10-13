<?php

  // We need admin controller
  AngieApplication::useController('admin');

  /**
   * Projects data cleanup administration controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ProjectsDataCleanupAdminController extends AdminController {
  
    /**
     * Show projects data cleanup page
     */
    function index() {
      $projects_per_page = 50;
      $project_states = array(STATE_DELETED, STATE_ARCHIVED);

      if($this->request->get('paged_list')) {
        $exclude = $this->request->get('paged_list_exclude') ? explode(',', $this->request->get('paged_list_exclude')) : null;
        $timestamp = $this->request->get('paged_list_timestamp') ? (integer) $this->request->get('paged_list_timestamp') : null;

        $this->response->respondWithData(Projects::sliceByStateForCleanup($projects_per_page, $project_states, $exclude, $timestamp));
      } else {
        $this->smarty->assign(array(
          'projects' => Projects::sliceByStateForCleanup($projects_per_page, $project_states),
          'projects_per_page' => $projects_per_page,
          'total_projects' => Projects::count(array('state IN (?)', $project_states))
        ));
      } // if
    } // index

    /**
     * Permanently delete chosen project
     */
    function permanently_delete_project() {
      if($this->request->isAsyncCall()) {
        $project_slug = $this->request->get('project_slug');

        $project = null;
        if($project_slug) {
          $project = Projects::findBySlug($project_slug);
        } // if

        if($project instanceof Project && $project->isLoaded()) {
          if($project->getState() == STATE_TRASHED || $project->getState() == STATE_VISIBLE) {
            $this->response->notFound();
          } // if

          if($project->canDelete($this->logged_user)) {
            try {
              $project->delete(true);
              $this->response->respondWithData($project, array('as' => 'project'));
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
    } // permanently_delete_project
    
  }