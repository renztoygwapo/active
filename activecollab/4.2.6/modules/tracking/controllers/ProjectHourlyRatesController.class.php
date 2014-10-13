<?php

  // Build on top of project controller
  AngieApplication::useController('project', SYSTEM_MODULE);

  /**
   * Project hourly rates management controller
   * 
   * @package activeCollab.modules.tracking
   * @subpackage controllers
   */
  class ProjectHourlyRatesController extends ProjectController {
    
    /**
     * Execute before any controller action
     */
    function __before() {
      parent::__before();
      
      if(!JobTypes::canManageProjectHourlyRates($this->logged_user, $this->active_project)) {
        $this->response->forbidden();
      } // if
    } // __before
  
    /**
     * Show project hourly rates
     */
    function index() {
      if($this->request->isApiCall()) {
        $result = array();

        $job_types = JobTypes::findAvailableTo($this->logged_user);
        if($job_types) {
          foreach($job_types as $job_type) {
            $result[] = array_merge($job_type->describeForApi($this->logged_user), array(
              'project_hourly_rate' => $job_type->getHourlyRateFor($this->active_project), 
            ));
          } // foreach
        } // if

        $this->response->respondWithData($result, array(
          'as' => 'job_types',
        ));
      } else {
        if(JobTypes::canManage($this->logged_user)) {
          $this->wireframe->actions->add('job_types_settings', lang('Job Type Settings'), Router::assemble('job_types_admin'), array(
            'onclick' => new FlyoutCallback(array(
              'title' => lang('Job Types & Hourly Rates'),
              'width' => 700
            )),
            'icon' => AngieApplication::getImageUrl('icons/12x12/settings.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface())
          ));
        } // if

        $this->response->assign('job_types', JobTypes::findForObjectsList($this->active_project));
      } // if
    } // view
    
    /**
     * Show and process project hourly rate form
     */
    function edit() {
      $job_type_id = $this->request->getId('job_type_id');
      
      $job_type = $job_type_id ? JobTypes::findById($job_type_id) : null;
      
      if($job_type instanceof JobType) {
        $project_hourly_rate_data = $this->request->post('project_hourly_rate', array(
          'use_custom' => $job_type->hasCustomHourlyRateFor($this->active_project), 
          'hourly_rate' => $job_type->getHourlyRateFor($this->active_project), 
        ));
        
        $this->response->assign(array(
          'active_job_type' => $job_type, 
          'project_hourly_rate_data' => $project_hourly_rate_data, 
        ));
        
        if($this->request->isSubmitted()) {
          try {
            if($project_hourly_rate_data['use_custom']) {
              $job_type->setCustomHourlyRateFor($this->active_project, (float) $project_hourly_rate_data['hourly_rate']);
            } else {
              $job_type->dropCustomHourlyRateFor($this->active_project);
            } // if
            
            $this->response->respondWithData(array(
              'id' => $job_type->getId(),
              'name' => $job_type->getName(),
              'default_hourly_rate' => $job_type->getDefaultHourlyRate(),
            	'custom_hourly_rate' => $job_type->getCustomHourlyRateFor($this->active_project),
              'is_active' => $job_type->getIsActive(),
              'urls' => array(
                'edit' => Router::assemble('project_hourly_rate', array('project_slug' => $this->active_project->getSlug(), 'job_type_id' => $job_type->getId()))
              )
            ), array('as' => 'job_type'));
          } catch(Exception $e) {
            $this->response->exception($e);
          } // try
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // edit
    
  }