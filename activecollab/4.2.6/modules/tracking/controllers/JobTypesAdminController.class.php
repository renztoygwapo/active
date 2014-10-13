<?php

  // Build on top of administration controller
  AngieApplication::useController('admin', SYSTEM_MODULE);

  /**
   * Job types administration controller
   * 
   * @package activeCollab.modules.tracking
   * @subpackage controllers
   */
  class JobTypesAdminController extends AdminController {
    
    /**
     * Selected job type
     *
     * @var JobType
     */
    protected $active_job_type;
    
    /**
     * Execute before action
     */
    function __before() {
      parent::__before();
      
      $this->wireframe->breadcrumbs->add('job_types_admin', lang('Job Types & Hourly Rates'), Router::assemble('job_types_admin'));
      
      $job_type_id = $this->request->getId('job_type_id');
      if($job_type_id) {
        $this->active_job_type = JobTypes::findById($job_type_id);
      } // if
      
      if($this->active_job_type instanceof JobType) {
        $this->wireframe->breadcrumbs->add('job_type', $this->active_job_type->getName(), $this->active_job_type->getViewUrl());
      } else {
        $this->active_job_type = new JobType();
      } // if
      
      $this->response->assign('active_job_type', $this->active_job_type);
    } // __before
  
    /**
     * Display list of defined job types
     */
    function index() {
      $this->wireframe->actions->add('add_job_type_form', lang('New Job Type'), Router::assemble('job_types_add'), array(
        'onclick' => new FlyoutFormCallback(array(
          'success_event' => 'job_type_created',
          'width' => 400
        )),
        'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
      ));

      $job_types_per_page = 50;
    	
    	if($this->request->get('paged_list')) {
    		$exclude = $this->request->get('paged_list_exclude') ? explode(',', $this->request->get('paged_list_exclude')) : null;
    		$timestamp = $this->request->get('paged_list_timestamp') ? (integer) $this->request->get('paged_list_timestamp') : null;
    		
    		$this->response->respondWithData(JobTypes::getSlice($job_types_per_page, $exclude, $timestamp));
    	} else {
    	  $this->smarty->assign(array(
    		  'job_types' => JobTypes::getSlice($job_types_per_page), 
    			'job_types_per_page' => $job_types_per_page, 
    		  'total_job_types' => JobTypes::count(),
          'job_types_page_url' => Router::assemble('job_types_admin')
    		));
    	} // if
    } // index
    
    /**
     * Show details of a single job type
     */
    function view() {
      if($this->active_job_type->isLoaded()) {
        if($this->active_job_type->canView($this->logged_user)) {
          $projects_table = TABLE_PREFIX . 'projects';
          $project_hourly_rates_table = TABLE_PREFIX . 'project_hourly_rates';
          
          $rows = DB::execute("SELECT $projects_table.name AS 'project_name', $projects_table.slug AS 'project_slug', $projects_table.company_id AS 'company_id', $project_hourly_rates_table.hourly_rate AS 'hourly_rate' FROM $projects_table, $project_hourly_rates_table WHERE $projects_table.id = $project_hourly_rates_table.project_id AND job_type_id = ?", $this->active_job_type->getId());
          if($rows instanceof DBResult) {
            $rows = $rows->toArray();
            
            $company_ids = array();
            
            foreach($rows as $row) {
              if($row['company_id'] && !in_array($row['company_id'], $company_ids)) {
                $company_ids[] = (integer) $row['company_id'];
              } // if
            } // foreach
            
            $company_names = $company_ids ? Companies::getIdNameMap($company_ids) : null;
            
            foreach($rows as $k => $v) {
              if($v['company_id'] && $company_names && isset($company_names[$v['company_id']])) {
                $rows[$k]['company_name'] = $company_names[$v['company_id']];
              } else {
                $rows[$k]['company_name'] = null;
              } // if
            } // foreach
          } // if
          
          $this->response->assign('projects_with_custom_hourly_rate', $rows);
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // view
    
    /**
     * Define a new job type
     */
    function add() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() || $this->request->isSubmitted())) {
        if(JobTypes::canAdd($this->logged_user)) {
          $job_type_data = $this->request->post('job_type');
          $this->response->assign('job_type_data', $job_type_data);
          
          if($this->request->isSubmitted()) {
            try {
              $this->active_job_type->setAttributes($job_type_data);
              $this->active_job_type->setIsActive(true);
              $this->active_job_type->save();
              
              $this->response->respondWithData($this->active_job_type, array('as' => 'job_type'));
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
     * Update an existing job type definition
     */
    function edit() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() || $this->request->isSubmitted())) {
        if($this->active_job_type->isLoaded()) {
          if($this->active_job_type->canEdit($this->logged_user)) {
            $job_type_data = $this->request->post('job_type', array(
              'name' => $this->active_job_type->getName(), 
              'default_hourly_rate' => $this->active_job_type->getDefaultHourlyRate(), 
              'update_default_hourly_rate' => false, 
              'update_default_hourly_rate_for' => 'active_projects', 
            ));
            $this->response->assign('job_type_data', $job_type_data);
            
            if($this->request->isSubmitted()) {
              try {
                DB::beginWork('Updating job type @ ' . __CLASS__);
                
                $old_default_hourly_rate = $this->active_job_type->getDefaultHourlyRate();
                
                $this->active_job_type->setName($job_type_data['name']);
                
                // Update hourly rate?
                if(isset($job_type_data['update_default_hourly_rate']) && (boolean) $job_type_data['update_default_hourly_rate']) {
                  $new_default_hourly_rate = (float) $job_type_data['default_hourly_rate'];
                  
                  if($new_default_hourly_rate != $old_default_hourly_rate) {
                    $this->active_job_type->setDefaultHourlyRate($new_default_hourly_rate);
                  } // if
                } else {
                  $new_default_hourly_rate = $old_default_hourly_rate;
                } // if
                
                $this->active_job_type->setAttributes($job_type_data);
                $this->active_job_type->save();
                
                // Make sure that we remember old default hourly rate for completed projects
                if($new_default_hourly_rate != $old_default_hourly_rate && isset($job_type_data['update_default_hourly_rate_for']) && $job_type_data['update_default_hourly_rate_for'] == 'active_projects') {
                  $completed_project_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'projects WHERE completed_on IS NOT NULL');
                  
                  if(is_foreachable($completed_project_ids)) {
                    $projects_with_custom_hourly_rate = DB::executeFirstColumn('SELECT project_id FROM ' . TABLE_PREFIX . 'project_hourly_rates WHERE job_type_id = ?', $this->active_job_type->getId());
                    
                    $escaped_job_type_id = DB::escape($this->active_job_type->getId());
                    $escaped_hourly_rate = DB::escape($old_default_hourly_rate);
                    
                    $to_insert = array();
                    foreach($completed_project_ids as $completed_project_id) {
                      if($projects_with_custom_hourly_rate && in_array($completed_project_id, $projects_with_custom_hourly_rate)) {
                        continue;
                      } // if
                      
                      $to_insert[] = DB::prepare("(?, $escaped_job_type_id, $escaped_hourly_rate)", $completed_project_id);
                    } // foreach
                    if(is_foreachable($to_insert)) {
                      DB::execute('INSERT INTO ' . TABLE_PREFIX . 'project_hourly_rates (project_id, job_type_id, hourly_rate) VALUES ' . implode(',', $to_insert));
                    }//if
                  } // if
                } // if
                
                DB::commit('Job type updated @ ' . __CLASS__);
                
                $this->response->respondWithData($this->active_job_type, array('as' => 'job_type'));
              } catch(Exception $e) {
                DB::rollback('Failed to updated job type @ ' . __CLASS__);
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
     * Set selected job type as default
     */
    function set_as_default() {
      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted()) {
        if($this->active_job_type->isLoaded()) {
          if($this->active_job_type->canSetAsDefault($this->logged_user)) {
            try {
              $this->active_job_type->setAsDefault();
              $this->response->respondWithData($this->active_job_type, array('as' => 'job_type'));
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
    } // set_as_default

    /**
     * Archive selected job type
     */
    function archive() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() || $this->request->isSubmitted())) {
        if($this->active_job_type->isLoaded()) {
          if($this->active_job_type->canArchive($this->logged_user)) {
            $job_type_data = $this->request->post('job_type', array(
              'used_by_users_count' => ConfigOptions::countByValue('job_type_id', $this->active_job_type->getId()),
              'replace_job_type' => false,
              'job_type_id' => JobTypes::getDefaultJobTypeId()
            ));
            $this->response->assign('job_type_data', $job_type_data);

            if($this->request->isSubmitted()) {
              try {
                DB::beginWork('Archiving job type @ ' . __CLASS__);

                $this->active_job_type->setIsActive(false);
                $this->active_job_type->save();

                if(isset($job_type_data['replace_job_type']) && (boolean) $job_type_data['replace_job_type']) {
                  DB::execute('UPDATE ' . TABLE_PREFIX . 'config_option_values SET value = ? WHERE name = ? AND value = ?', serialize((integer) $job_type_data['job_type_id']), 'job_type_id', serialize($this->active_job_type->getId()));
                  AngieApplication::cache()->clearModelCache();
                } else {
                  ConfigOptions::removeByValue('job_type_id', $this->active_job_type->getId());
                } // if

                DB::commit('Job type archived @ ' . __CLASS__);

                $this->response->respondWithData($this->active_job_type, array('as' => 'job_type'));
              } catch(Exception $e) {
                DB::rollback('Failed to archive job type @ ' . __CLASS__);
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
    } // archive

    /**
     * Unarchive selected job type
     */
    function unarchive() {
      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted()) {
        if($this->active_job_type->isLoaded()) {
          if($this->active_job_type->canArchive($this->logged_user)) {
            try {
              DB::beginWork('Unarchiving job type @ ' . __CLASS__);

              $this->active_job_type->setIsActive(true);
              $this->active_job_type->save();

              DB::commit('Job type unarchived @ ' . __CLASS__);

              $this->response->respondWithData($this->active_job_type, array('as' => 'job_type'));
            } catch(Exception $e) {
              DB::rollback('Failed to unarchive job type @ ' . __CLASS__);
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
    } // unarchive
    
    /**
     * Remove a specific job type
     */
    function delete() {
      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted()) {
        if($this->active_job_type->isLoaded()) {
          if($this->active_job_type->canDelete($this->logged_user)) {
            try {
              $this->active_job_type->delete();
              $this->response->respondWithData($this->active_job_type, array('as' => 'job_type'));
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