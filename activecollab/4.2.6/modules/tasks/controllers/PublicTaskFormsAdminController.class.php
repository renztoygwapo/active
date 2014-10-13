<?php

  // Build on top of administration controller
  AngieApplication::useController('admin', SYSTEM_MODULE);

  /**
   * Public tasks form administration controller
   * 
   * @package activeCollab.modules.tasks
   * @subpackage controllers
   */
  class PublicTaskFormsAdminController extends AdminController {
    
    /**
     * Selected public taks form
     *
     * @var PublicTaskForm
     */
    protected $active_public_task_form;
    
    /**
     * Execute before action
     */
    function __before() {
      parent::__before();
      
      $public_task_form_id = $this->request->getId('public_task_form_id');
      if($public_task_form_id) {
        $this->active_public_task_form = PublicTaskForms::findById($public_task_form_id);
      } // if
      
      if($this->active_public_task_form instanceof PublicTaskForm) {
        $this->wireframe->breadcrumbs->add('public_task_form', $this->active_public_task_form->getName(), $this->active_public_task_form->getViewUrl());
      } else {
        $this->active_public_task_form = new PublicTaskForm();
      } // if
      
      $this->response->assign('active_public_task_form', $this->active_public_task_form);
    } // __before
  
    /**
     * Create a new public task form
     */
    function add() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() || $this->request->isSubmitted())) {
        if(PublicTaskForms::canAdd($this->logged_user)) {
          $public_task_form_data = $this->request->post('public_task_form', array(
          	'sharing' => 1,
          	'expire_after' => 7,
          	'comments_enabled' => true,
          	'reopen_on_comment' => true,
          	'subscribe_author' => true,
            'attachments_enabled' => false
          ));
          
          $this->response->assign('public_task_form_data', $public_task_form_data);
          
          if($this->request->isSubmitted()) {
            try {
              DB::beginWork('Creating task form @ ' . __CLASS__);
              
              $this->active_public_task_form->setAttributes($public_task_form_data);
              $this->active_public_task_form->setIsEnabled(true);
              
              // save additional properties
              $this->active_public_task_form->setAdditionalProperty('sharing', array_var($public_task_form_data, 'sharing'));
              if (array_var($public_task_form_data, 'expire_after')) {
              	$this->active_public_task_form->setAdditionalProperty('expire_after', array_var($public_task_form_data, 'expire_after'));		            	
              } // if
              $this->active_public_task_form->setAdditionalProperty('subscribe_author', array_var($public_task_form_data, 'subscribe_author'));
              $this->active_public_task_form->setAdditionalProperty('comments_enabled', array_var($public_task_form_data, 'comments_enabled'));
              $this->active_public_task_form->setAdditionalProperty('reopen_on_comment', array_var($public_task_form_data, 'reopen_on_comment'));
              $this->active_public_task_form->setAdditionalProperty('attachments_enabled', array_var($public_task_form_data, 'attachments_enabled'));
              
              $this->active_public_task_form->save();
              
              // set subscribers
  						$this->active_public_task_form->subscriptions()->set(array_unique(array_merge(
  		          (array) $this->active_public_task_form->getProject()->getLeaderId(),
  	            (array) array_var($_POST, 'subscribers', array())
  	          )), true);
              
              DB::commit('Task form create @ ' . __CLASS__);
              
              $this->response->respondWithData($this->active_public_task_form, array('as' => 'public_task_form'));
            } catch(Exception $e) {
              DB::rollback('Failed to create task form @ ' . __CLASS__);
              $this->response->exception($e);
            } // try
          } // if
        } else {
          $this->response->forbidden();
        } // oif
      } else {
        $this->response->badRequest();
      } // if
    } // add
    
    /**
     * Update an existing public task form
     */
    function edit() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() || $this->request->isSubmitted())) {
        if($this->active_public_task_form->isLoaded()) {
          if($this->active_public_task_form->canEdit($this->logged_user)) {
            $public_task_form_data = $this->request->post('public_task_form', array(
              'slug' => $this->active_public_task_form->getSlug(),
              'name' => $this->active_public_task_form->getName(), 
              'body' => $this->active_public_task_form->getBody(), 
              'project_id' => $this->active_public_task_form->getProjectId(),
	            'sharing' => $this->active_public_task_form->getAdditionalProperty('sharing', 1),
	            'expire_after' => $this->active_public_task_form->getAdditionalProperty('expire_after', 7),
	            'subscribe_author' => $this->active_public_task_form->getAdditionalProperty('subscribe_author', 1),
	            'comments_enabled' => $this->active_public_task_form->getAdditionalProperty('comments_enabled', 1),
	            'reopen_on_comment' => $this->active_public_task_form->getAdditionalProperty('reopen_on_comment', 1),
              'attachments_enabled' => $this->active_public_task_form->getAdditionalProperty('attachments_enabled', 0)
            ));

            $this->smarty->assign(array(
            	'public_task_form_data' => $public_task_form_data,
            	'subscribers' => $this->active_public_task_form->subscriptions()->getIds()
            ));
            
            if($this->request->isSubmitted()) {
              try {
                DB::beginWork('Updating task form @ ' . __CLASS__);
                
                // set attributes
                $this->active_public_task_form->setAttributes($public_task_form_data);
                
		            // save additional properties
		            $this->active_public_task_form->setAdditionalProperty('sharing', array_var($public_task_form_data, 'sharing'));
		            if (array_var($public_task_form_data, 'expire_after')) { $this->active_public_task_form->setAdditionalProperty('expire_after', array_var($public_task_form_data, 'expire_after')); } // if
		            $this->active_public_task_form->setAdditionalProperty('subscribe_author', array_var($public_task_form_data, 'subscribe_author'));
		            $this->active_public_task_form->setAdditionalProperty('comments_enabled', array_var($public_task_form_data, 'comments_enabled'));
		            $this->active_public_task_form->setAdditionalProperty('reopen_on_comment', array_var($public_task_form_data, 'reopen_on_comment'));
                $this->active_public_task_form->setAdditionalProperty('attachments_enabled', array_var($public_task_form_data, 'attachments_enabled'));

		            // save the form
                $this->active_public_task_form->save();
                
                // set subscribers
								$this->active_public_task_form->subscriptions()->set(array_unique(array_merge(
	                (array) $this->active_public_task_form->getProject()->getLeaderId(),
	                (array) array_var($_POST, 'subscribers', array())
	              )), true);
                
                DB::commit('Task form updated @ ' . __CLASS__);
                
                $this->response->respondWithData($this->active_public_task_form, array(
                  'as' => 'public_task_form', 
                  'detailed' => true, 
                ));
              } catch(Exception $e) {
                DB::rollback('Failed to update task form @ ' . __CLASS__);
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
     * Enable selected public task form
     */
    function enable() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() || $this->request->isSubmitted())) {
        if($this->active_public_task_form->isLoaded()) {
          if($this->active_public_task_form->canChangeStatus($this->logged_user)) {
            try {
              $this->active_public_task_form->setIsEnabled(true);
              $this->active_public_task_form->save();
              
              $this->response->respondWithData($this->active_public_task_form, array(
                'as' => 'public_task_form', 
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
    } // enable
    
    /**
     * Disable selected public task form
     */
    function disable() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() || $this->request->isSubmitted())) {
        if($this->active_public_task_form->isLoaded()) {
          if($this->active_public_task_form->canChangeStatus($this->logged_user)) {
            try {
              $this->active_public_task_form->setIsEnabled(false);
              $this->active_public_task_form->save();
              
              $this->response->respondWithData($this->active_public_task_form, array(
                'as' => 'public_task_form', 
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
    } // disable
    
    /**
     * Drop selected public task form
     */
    function delete() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() || $this->request->isSubmitted())) {
        if($this->active_public_task_form->isLoaded()) {
          if($this->active_public_task_form->canDelete($this->logged_user)) {
            try {
              $this->active_public_task_form->delete();
              $this->response->respondWithData($this->active_public_task_form, array(
                'as' => 'public_task_form', 
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

    /**
     * render select subscribers widget for provided project
     */
    function subscribers() {
    	$project_id = $this->request->get('project_id');
    	$project = Projects::findById($project_id);
    	
    	if (!($project && $project instanceof Project && !$project->isNew())) {
    		$this->response->notFound();
    	} // if
    	
    	$subscribers = $this->request->post('subscribers');

    	$dummy_task = new Task();
    	$dummy_task->setProjectId($project_id);
    	
    	AngieApplication::useHelper('select_subscribers', SUBSCRIPTIONS_FRAMEWORK);
    	
    	$this->response->respondWithText(smarty_function_select_subscribers(array(
    		'name' => 'subscribers',
    		'object' => $dummy_task,
    		'user' => $this->logged_user,
    		'value' => $subscribers
    	), $this->smarty));
    } // subscribers
  }