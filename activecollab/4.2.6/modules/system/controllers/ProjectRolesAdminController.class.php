<?php

  // Build on top of roles admin controller
  AngieApplication::useController('admin', SYSTEM_MODULE);

  /**
   * Project roles administration controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ProjectRolesAdminController extends AdminController {
    
    /**
     * Selected project role instance
     *
     * @var ProjectRole
     */
    protected $active_role;
    
    /**
     * Execute before any of the controller actions
     */
    function __before() {
      parent::__before();
      
      $this->wireframe->breadcrumbs->add('project_roles_admin', lang('Project Roles'), Router::assemble('admin_project_roles'));
      
      $role_id = $this->request->getId('role_id');
      if($role_id) {
        $this->active_role = ProjectRoles::findById($role_id);
      } // if
      
      if($this->active_role instanceof ProjectRole) {
        $this->wireframe->breadcrumbs->add("project_role_{$role_id}", $this->active_role->getName(), $this->active_role->getViewUrl());
      } else {
        $this->active_role = new ProjectRole();
      } // if
      $this->response->assign('active_role', $this->active_role);
    } // __before
    
    /**
     * Show all project roles
     */
    function index() {
      $this->wireframe->actions->add('new_role', lang('New Role'), Router::assemble('admin_project_roles_add'), array(
        'onclick' => new FlyoutFormCallback('project_role_created'), 
        'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()), 
      ));
      
      $project_roles_per_page = 50;
    	
    	if($this->request->get('paged_list')) {
    		$exclude = $this->request->get('paged_list_exclude') ? explode(',', $this->request->get('paged_list_exclude')) : null;
    		$timestamp = $this->request->get('paged_list_timestamp') ? (integer) $this->request->get('paged_list_timestamp') : null;
    		
    		$this->response->respondWithData(ProjectRoles::getSlice($project_roles_per_page, $exclude, $timestamp));
    	} else {
    	  JSON::encode(ProjectRoles::getSlice($project_roles_per_page));
    	  
    	  $this->smarty->assign(array(
    		  'project_roles' => ProjectRoles::getSlice($project_roles_per_page), 
    			'project_roles_per_page' => $project_roles_per_page, 
    		  'total_project_roles' => ProjectRoles::count(), 
    		));
    	} // if
    } // index
    
    /**
     * Show single project role and its users
     */
		function view() {
			if($this->active_role->isLoaded()) {
				$this->wireframe->actions->add('change_project_role_permission', lang('Change Settings'), $this->active_role->getEditUrl(), array(
          'onclick' => new FlyoutFormCallback('project_role_updated'), 
        ));
        
				$this->response->assign('project_users', $this->active_role->getProjectUsers($this->logged_user));
			} else {
				$this->response->notFound();
			} //if
		} // view   
    
    /**
     * Add project role
     */
    function add() {
    	if($this->request->isAsyncCall()) {
      	$role_data = $this->request->post('role');
      	$this->response->assign('role_data', $role_data);
      	
      	if($this->request->isSubmitted()) {
      	  try {
      	    $this->active_role->setAttributes($role_data);
      	    
      	    if(ProjectRoles::count() < 1) {
      	      $this->active_role->setIsDefault(true);
      	    } // if
      	    
      	    $this->active_role->save();

            clean_menu_projects_and_quick_add_cache();
      	    
      	    $this->response->respondWithData($this->active_role, array(
      	      'as' => 'role', 
      	      'detailed' => true, 
      	    ));
      	  } catch(Exception $e) {
      	    $this->response->exception($e);
      	  } // try
      	} // if
      } else {
        $this->response->badRequest();
      } // if
    } // add
    
    /**
     * Update existing role
     */
    function edit() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if($this->active_role->isLoaded()) {
          if($this->active_role->canEdit($this->logged_user)) {
            $role_data = $this->request->post('role', array(
              'name' => $this->active_role->getName(),
              'permissions' => $this->active_role->getPermissions(),
            ));
            $this->response->assign('role_data', $role_data);
            
            if($this->request->isSubmitted()) {
              try {
                $this->active_role->setAttributes($role_data);
                $this->active_role->save();

                clean_menu_projects_and_quick_add_cache();
                
                $this->response->respondWithData($this->active_role, array(
                  'as' => 'role', 
                  'detailed' => true, 
                ));
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
     * Set selected project role as default
     */
    function set_as_default() {
      if($this->request->isSubmitted() && ($this->request->isAsyncCall() || $this->request->isApiCall())) {
        if($this->active_role->isLoaded()) {
          if($this->active_role->canEdit($this->logged_user)) {
            try {
        	    ProjectRoles::setDefault($this->active_role);
        	    $this->response->respondWithData($this->active_role, array(
        	      'as' => 'role',
        	      'detailed' => true,
        	    ));
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
     * Drop existing role
     */
    function delete() {
      if($this->request->isSubmitted() && ($this->request->isAsyncCall() || $this->request->isApiCall())) {
        if($this->active_role->isLoaded()) {
          if($this->active_role->canDelete($this->logged_user)) {
            try {
              $this->active_role->delete();

              AngieApplication::cache()->clearModelCache();
              
              $this->response->respondWithData($this->active_role, array(
                'as' => 'role', 
                'detailed' => true, 
              ));
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