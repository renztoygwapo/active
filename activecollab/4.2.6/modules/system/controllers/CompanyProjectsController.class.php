<?php

  // Build on top of companies controller
  AngieApplication::useController('companies', SYSTEM_MODULE);
  
  /**
   * Company projcects controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class CompanyProjectsController extends CompaniesController {
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if($this->active_company->isLoaded()) {
        if($this->active_company->canView($this->logged_user)) {
          $this->wireframe->breadcrumbs->add('company_projects', lang('Projects'), $this->active_company->getProjectsUrl());
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // __construct
    
    /**
     * Show company projects
     */
    function index() {
      $projects_table = TABLE_PREFIX . 'projects';
      $company_projects_url = Router::assemble("people_company_projects", array("company_id" => $this->active_company->getId()));

      if ($this->request->get('archive')) {
        $conditions = DB::prepare("$projects_table.state = ?", STATE_ARCHIVED);
        $order_by = "completed_on DESC";
        $projects_toggle_url = $company_projects_url;
        $projects_toggle_text = lang("Show Active Projects");
      } else {
        $conditions = DB::prepare("$projects_table.state >= ?", STATE_VISIBLE);
        $order_by = "$projects_table.completed_on asc, $projects_table.name ASC";
        $projects_toggle_url = extend_url($company_projects_url, array('archive' => 1));
        $projects_toggle_text = lang("Show Archived Projects");
      } // conditions

      $all_projects = Projects::findByUserAndCompany($this->logged_user, $this->active_company, true, $conditions, $order_by);
      if ($this->request->get('for_select_box')) {
        $response = array(
          'active' => array(),
          'completed' => array()
        );

        if (is_foreachable($all_projects)) {
          // group by active/completed status
          foreach ($all_projects as $project) {
            $key = $project->getCompletedOn() ? 'completed' : 'active';
            $response[$key][$project->getId()] = $project->getName();
          } // foreach
        } // if

        $this->response->respondWithData($response, array('format' => 'json'));
      } else {
        $this->response->assign(array(
          "is_archive" => (boolean) $this->request->get('archive'),
          "projects" => $all_projects,
          "projects_toggle_url" => $projects_toggle_url,
          "projects_toggle_text" => $projects_toggle_text
        ));
      } // if
    } // index
    
    /**
     * Show company projects archive page
     */
    function archive() {
      if($this->request->isMobileDevice()) {
        $this->smarty->assign('completed_projects', Projects::findCompletedByUserAndCompany($this->logged_user, $this->active_company, true));
      } else {
        $this->response->badRequest();
      } // if
    } // archive
    
  }