<?php

  // Use project controller
  AngieApplication::useController('project', SYSTEM_MODULE);
  
  /**
   * Project outline controller
   * 
   * Manage project in outline view
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ProjectOutlineController extends ProjectController {
  	
  	/**
  	 * Whether project outline supports tasks
  	 * 
  	 * @var boolean
  	 */
  	private $supports_tasks = false;
  	
  	/**
  	 * Whether project outline supports todolists
  	 * 
  	 * @var boolean
  	 */
  	private $supports_todolists = false;
  	
  	/**
  	 * Whether project outline supports tracking
  	 * 
  	 * @var boolean
  	 */
  	private $support_tracking = false;
    
    /**
     * Controller constructor
     */
    function __before() {
      parent::__before();
      
      // if user has no access to milestones, outline can't be displayed 
      if (!$this->logged_user->canSeeMilestones($this->active_project)) {
				$this->response->forbidden();
      } // if
      
      // load project tabs
      $project_tabs = $this->active_project->getTabs($this->logged_user, AngieApplication::INTERFACE_DEFAULT);
      
      if (AngieApplication::isModuleLoaded('tasks') && $project_tabs->exists('tasks') && Tasks::canAccess($this->logged_user, $this->active_project)) {
      	$this->supports_tasks = true;
      } // if
      
      if (AngieApplication::isModuleLoaded('todo') && $project_tabs->exists('todo_lists') && TodoLists::canAccess($this->logged_user, $this->active_project)) {
      	$this->supports_todolists = true;
      } // if
      
      if (AngieApplication::isModuleLoaded('tracking') && $project_tabs->exists('time') && TrackingObjects::canAccess($this->logged_user, $this->active_project)) {
      	$this->support_tracking = true;
      } // if
      
			$this->wireframe->tabs->setCurrentTab('outline');
    } // __before
    
    /**
     * Project outline index page
     */
    function index() {
      AngieApplication::useWidget('project_outline', SYSTEM_MODULE);

      // extract labels in needed form
      $labels = array(); 
      $label_types = array(); 
      $default_labels = array();

      EventsManager::trigger('on_label_types', array(&$label_types));
      
      if (is_foreachable($label_types)) {
      	foreach ($label_types as $label_type => $label_info) {
      		$current_labels = Labels::findByType($label_type);
      		$current_default_label = Labels::findDefault($label_type);
      		if ($current_default_label instanceof Label) {
      			$default_labels[$label_type] = $current_default_label->getId();
      		} // if
      		if (is_foreachable($current_labels)) {
						foreach ($current_labels as $current_label) {
							$labels[$label_type][$current_label->getId()] = $current_label->describe($this->logged_user, false, true);
						} // foreach
      		} // if
      	} // foreach

        foreach($labels as $k => $v) {
          $labels[$k] = JSON::valueToMap($v);
        } // foreach
      } // if
            
      // find user map
      $users = $this->active_project->users()->get($this->logged_user);
      $users_map = array(); $companies_map = array();
      if (is_foreachable($users)) {
      	foreach ($users as $user) {
      		$users_map[$user->getCompanyId()][$user->getId()] = $user->getDisplayName();
      	} // foreach
      	$companies_map = Companies::getIdNameMap(array_keys($users_map));
      } // if
      
      // add urls
      $add_urls = array(
      	'milestone' => Router::assemble('project_milestones_add', array('project_slug' => $this->active_project->getSlug()))
			);
			// permissions
			$permissions = array(
				'can_add_milestones' => Milestones::canAdd($this->logged_user, $this->active_project), 
			);
			
			if ($this->supports_tasks) {
      	$add_urls['task'] = Router::assemble('project_tasks_add', array('project_slug' => $this->active_project->getSlug(), 'milestone_id' => '--PARENT-ID--'));
      	$add_urls['task_subtask'] = Router::assemble('project_task_subtasks_add', array('project_slug' => $this->active_project->getSlug(), 'task_id' => '--PARENT-ID--'));
				$permissions['can_see_tasks'] =  true;
      	$permissions['can_add_tasks'] = Tasks::canAdd($this->logged_user, $this->active_project);
      	$permissions['can_manage_tasks'] = Tasks::canManage($this->logged_user, $this->active_project);
			} // if
			
			if ($this->supports_todolists) {
				$add_urls['todolist'] = Router::assemble('project_todo_lists_add', array('project_slug' => $this->active_project->getSlug(), 'milestone_id' => '--PARENT-ID--'));
      	$add_urls['todolist_subtask'] = Router::assemble('project_todo_list_subtasks_add', array('project_slug' => $this->active_project->getSlug(), 'todo_list_id' => '--PARENT-ID--'));
      	$permissions['can_see_todolists'] =  true;
      	$permissions['can_add_todolists'] = TodoLists::canAdd($this->logged_user, $this->active_project);
      	$permissions['can_manage_todolists'] = TodoLists::canManage($this->logged_user, $this->active_project);
			} // if

			if ($this->support_tracking) {
				$permissions['can_use_tracking'] = true;
			} // if

      $unclassified_label = lang('Unclassified');
      if ($this->supports_tasks && $this->supports_todolists) {
        $unclassified_label = lang('Unclassified Tasks and To Do Lists');
      } else if ($this->supports_tasks) {
        $unclassified_label = lang('Unclassified Tasks');
      } else if ($this->supports_todolists) {
        $unclassified_label = lang('Unclassified To Do Lists');
      } // if
			
      $this->smarty->assign(array(
        'default_visibility' => $this->active_project->getDefaultVisibility(),
      	'initial_subobjects' => JSON::encode(Milestones::findActiveByProject($this->active_project, STATE_VISIBLE, $this->logged_user->getMinVisibility()), $this->logged_user, true, true),
      	'users' => $this->active_project->users()->get($this->logged_user),
      	'labels_map' => $labels,
      	'default_labels' => $default_labels,
      	'milestones_map' => Milestones::getIdNameMap($this->active_project),
      	'priorities_map' => array('2' => lang('Highest'), '1' => lang('High'), '0' => lang('Normal'), '-1' => lang('Low'), '-2' => lang('Lowest')),
      	'categories_map' => array(
          'task' => JSON::valueToMap(Categories::getIdNameMap($this->active_project, 'TaskCategory')),
          'todolist' => JSON::valueToMap(Categories::getIdNameMap($this->active_project, 'TodoListCategory'))
        ),
      	'users_map' => $users_map,
      	'companies_map' => $companies_map,
      	'job_types_map' => AngieApplication::isModuleLoaded('tracking') ? JobTypes::getIdNameMap(null, JOB_TYPE_INACTIVE) : null,
      	'add_urls' => $add_urls,
      	'subobjects_url' => Router::assemble('project_outline_subobjects', array('object_id' => '--OBJECT-ID--', 'object_type' => '--OBJECT-TYPE--', 'project_slug' => $this->active_project->getSlug())),
  			'reorder_url' => Router::assemble('project_outline_reorder', array('object_id' => '--OBJECT-ID--', 'object_type' => '--OBJECT-TYPE--', 'project_slug' => $this->active_project->getSlug())),
				'permissions' => $permissions,
        'unclassified_label' => $unclassified_label,
      	'mass_edit_urls' => array(
  			  'change_category' => Router::assemble('project_outline_mass_edit', array('project_slug' => $this->active_project->getSlug(), 'mass_edit_action' => 'change_category')),
  			  'change_assignee' => Router::assemble('project_outline_mass_edit', array('project_slug' => $this->active_project->getSlug(), 'mass_edit_action' => 'change_assignee')),
  			  'change_label' => Router::assemble('project_outline_mass_edit', array('project_slug' => $this->active_project->getSlug(), 'mass_edit_action' => 'change_label')),
  			),
  			'shortcuts_url' => Router::assemble('project_outline_shortcuts', array('project_slug' => $this->active_project->getSlug())),
        'default_billable_status' => AngieApplication::isModuleLoaded('tracking') ? $this->active_project->tracking()->getDefaultBillableStatus() : 0,
      ));
    } // index
    
    /**
     * Renders subobjects for some object
     */
    function subobjects() {
      if (!$this->request->isAsyncCall()) {
        $this->response->badRequest();
      } // if
      
      $object_type = strtolower($this->request->get('object_type'));
      if ($object_type != 'project') {
        $object = ProjectObjects::findById($this->request->get('object_id'));
        if (!$object && $object_type == 'milestone') {
					$object = new Milestone();
        } else if (!($object instanceof ProjectObject)) {
          $this->response->notFound();
        } // if
      } else {
      	$object = $this->active_project;
      } // if
      
      $subobjects = array();
      
      switch ($object_type) {
        case 'project':
					if (!$this->logged_user->canSeeMilestones($this->active_project)) {
						$this->response->forbidden();
					} // if
          $this->response->respondWithData(Milestones::findForOutline($object, $this->logged_user, STATE_VISIBLE));
          break;
          
        case 'milestone':
        	if (!($this->supports_tasks || $this->supports_todolists)) {
        		$this->response->forbidden();
        	} // if

          $tasks = $todo_lists = false;

          $results = array();

          if ($this->supports_tasks) {
            $tasks = Tasks::findForOutline($this->active_project, $object->getId(), $this->logged_user, STATE_VISIBLE);
          } // if

          if ($this->supports_todolists) {
            $todo_lists = TodoLists::findForOutline($this->active_project, $object->getId(), $this->logged_user, STATE_VISIBLE);
          } // if

          if(is_foreachable($tasks) && is_foreachable($todo_lists)) {
            $this->response->respondWithData(array_merge($tasks, $todo_lists));
          } elseif(is_foreachable($tasks)) {
            $this->response->respondWithData($tasks);
          } elseif(is_foreachable($todo_lists)) {
            $this->response->respondWithData($todo_lists);
          } else {
            $this->response->respondWithData(null);
          } // if

          break;
          
        case ('task' || 'todolist'):
        	if (!$object->canView($this->logged_user)) {
        		$this->response->forbidden();
        	} // if

          $this->response->respondWithData(Subtasks::findForOutline($object, $this->logged_user));
          break;          
      } // switch
            
      $this->response->respondWithData($subobjects, array('detailed' => true));
    } // subobjects
    
    /**
     * Mass edit action
     */
    function mass_edit() {
      if (!$this->request->isSubmitted()) {
      	$this->response->badRequest();
      } // if
      
      $object_ids = $this->request->post('selected_items');
      
      $updated_objects = array();
      switch ($this->request->get('mass_edit_action')) {
      	
      	// change category logic
        case 'change_category':
          // only tasks can have categories
          $category_id = $this->request->post('category_id') ? $this->request->post('category_id') : null; 
          
          if (is_foreachable($object_ids['task'])) {
            try {
            	DB::beginWork('project outline mass edit change category started');

       				$tasks = Tasks::findByIds($object_ids['task'], STATE_VISIBLE, $this->logged_user->getMinVisibility());
            	if (is_foreachable($tasks)) {
            		foreach ($tasks as $task) {
            			if ($task->canEdit($this->logged_user)) {
            				$task->setCategoryId($category_id);
            				$task->save();
            				$updated_objects[] = $task;
            			} // if
            		}	// if
            	} // if
							
							DB::commit('project outline mass edit change category successful');
            } catch (Exception $e) {
							DB::rollback('project outline mass edit change category failed');
            	$this->response->exception($e);
            } // try
          } // if
          break;
          
        // change label
        case 'change_label':
        	$label_id = $this->request->post('label_id') ? $this->request->post('label_id') : null;
        	
        	if (is_foreachable($object_ids['task']) || is_foreachable($object_ids['subtask'])) {
        		try {
        			DB::beginWork('project outline mass edit change label started');
        			
        			if (is_foreachable($object_ids['task'])) {
        				$tasks = Tasks::findByIds($object_ids['task'], STATE_VISIBLE, $this->logged_user->getMinVisibility());
	            	if (is_foreachable($tasks)) {
	            		foreach ($tasks as $task) {
	            			if ($task->canEdit($this->logged_user)) {
	            				$task->setLabelId($label_id);
	            				$task->save();
	            				$updated_objects[] = $task;
	            			} // if
	            		}	// if
	            	} // if
        			} // if
        			
        			if (is_foreachable($object_ids['subtask'])) {
        				$subtasks = Subtasks::findByIds($object_ids['subtask']);
        				if (is_foreachable($subtasks)) {
        					foreach ($subtasks as $subtask) {
        						if ($subtask->canEdit($this->logged_user)) {
        							$subtask->setLabelId($label_id);
        							$subtask->save();
        							$updated_objects[] = $subtask;
        						} // if
        					};
        				} // if
        			} // if
        			
							DB::commit('project outline mass edit change label successful');
        		} catch (Exception $e) {
							DB::rollback('project outline mass edit change label failed');
							$this->response->exception($e);
        		} // try
        	} // if
        	break;
        	
        // change assignee
        case 'change_assignee':
        	$assignee = $this->request->post('assignee_id') ? Users::findById($this->request->post('assignee_id')) : null;
        	if (is_foreachable($object_ids['task']) || is_foreachable($object_ids['subtask']) || is_foreachable($object_ids['milestone'])) {
        		try {
        			DB::beginWork('project outline mass edit change assignee started');
        			
        			// update milestones
        			if (is_foreachable($object_ids['milestone'])) {
        				$milestones = Milestones::findByIds($object_ids['milestone'], STATE_VISIBLE, $this->logged_user->getMinVisibility());
        				if (is_foreachable($milestones)) {
									foreach ($milestones as $milestone) {
										if ($milestone->canEdit($this->logged_user)) {
                      $current_assignee = $milestone->assignees()->getAssignee();
											$milestone->assignees()->setAssignee($assignee);
                      $milestone->assignees()->notifyOnReassignment($current_assignee, $milestone->assignees()->getAssignee(), $this->logged_user);
											$updated_objects[] = $milestone;
										} // if
									} // foreach       					
        				} // if
        			} // if
        			
        			// update tasks
        			if (is_foreachable($object_ids['task'])) {
        				$tasks = Tasks::findByIds($object_ids['task'], STATE_VISIBLE, $this->logged_user->getMinVisibility());
	            	if (is_foreachable($tasks)) {
	            		foreach ($tasks as $task) {
	            			if ($task->canEdit($this->logged_user)) {
                      $current_assignee = $task->assignees()->getAssignee();
                      $task->assignees()->setAssignee($assignee);
                      $task->assignees()->notifyOnReassignment($current_assignee, $task->assignees()->getAssignee(), $this->logged_user);
	            				$updated_objects[] = $task;
	            			} // if
	            		}	// if
	            	} // if
        			} // if
        			
        			if (is_foreachable($object_ids['subtask'])) {
        				$subtasks = Subtasks::findByIds($object_ids['subtask']);
        				if (is_foreachable($subtasks)) {
        					foreach ($subtasks as $subtask) {
        						if ($subtask->canEdit($this->logged_user)) {
                      $current_assignee = $subtask->assignees()->getAssignee();
                      $subtask->assignees()->setAssignee($assignee);
                      $subtask->assignees()->notifyOnReassignment($current_assignee, $subtask->assignees()->getAssignee(), $this->logged_user);
        							$updated_objects[] = $subtask;
        						} // if
        					};
        				} // if
        			} // if
        			
        			DB::commit('project outline mass edit change assignee successful');
        		} catch (Exception $e) {
        			DB::rollback('project outline mass edit change assignee failed');
        			$this->response->exception($e);
        		} // try
        	} // if
        	break;
          
        default:
          $this->response->badRequest();
          break;
      } // switch

      $this->response->respondWithData($updated_objects, array('detailed' => true));
    } // mass_edit
    
    /**
     * Reorder items
     */
    function reorder() {
    	// check if request is submitted and async
    	if (!($this->request->isSubmitted() && $this->request->isAsyncCall())) {
				$this->response->badRequest();
    	} // if
    	
    	$object_type = strtolower($this->request->get('object_type', null));
    	$object_id = $this->request->getId('object_id', null);

    	$active_object = null;
    	$result = array();
    	
    	if ($object_type == 'milestone') {
				$parent_id = null;
				
   			// check if we have permission to manage tasks
   			$task_ids = $this->request->post('task');
   			if (is_foreachable($task_ids)) {
					if (!Tasks::canManage($this->logged_user, $this->active_project)) {    				
						$this->response->forbidden();
					} // if
   			} // if
   			
   			// check if we have permission to manage todo lists
   			$todolist_ids = $this->request->post('todolist');
   			if (is_foreachable($todolist_ids)) {
   				if (!TodoLists::canManage($this->logged_user, $this->active_project)) {
   					$this->response->forbidden();
   				} // if
   			} // if
    			
   			// try to find the parent object (in case of milestone, the parent object can be unknown milestone which doesn not exists in database) 
				$active_object = Milestones::findById($object_id);
				if ($active_object instanceof Milestone && !$active_object->isNew()) {
					$parent_id = $active_object->getId();
				} else {
					$parent_id = null;
				} // if
				
				try {
					DB::beginWork('Project outline start sorting');
					
					// update tasks positions, and parent attributes
					if (is_foreachable($task_ids)) {
						$counter = 0;
						foreach ($task_ids	as $task_id) {
							DB::execute('UPDATE `' . TABLE_PREFIX . 'project_objects` SET `position` = ?, milestone_id = ? WHERE `id` = ? AND `project_id` = ? AND `type` = ?', $counter, $parent_id, $task_id, $this->active_project->getId(), 'Task');
							$counter ++;
						} // foreach
						
						$updated_tasks = Tasks::findByIds($task_ids, STATE_VISIBLE, $this->logged_user->getMinVisibility());
						$result = array_merge((array) $result, (array) $updated_tasks->toArray());
					} // if
					
					// update todolist positions
					if (is_foreachable($todolist_ids)) {
						$counter = 0;
						foreach ($todolist_ids as $todolist_id) {
							DB::execute('UPDATE `' . TABLE_PREFIX . 'project_objects` SET `position` = ?, milestone_id = ? WHERE `id` = ? AND `project_id` = ? AND `type` = ?', $counter, $parent_id, $todolist_id, $this->active_project->getId(), 'TodoList');
							$counter ++;
						} // foreach
						
						$updated_todolists = Todolists::findByIds($todolist_ids, STATE_VISIBLE, $this->logged_user->getMinVisibility());
						$result = array_merge((array) $result, (array) $updated_todolists->toArray());
					} // if
					
					DB::commit('Project outline sorting successful');
				} catch (Exception $e) {
					db::rollback('Project outline sorting failed');
					$this->response->exception($e);
				} // if
    	} else if ($object_type == 'task' || $object_type == 'todolist') {
    		
    		if ($object_type == 'task') {
    			$active_object = Tasks::findByTaskId($this->active_project, $object_id);
    		} else {
    			$active_object = TodoLists::findById($object_id);
    		} // if
    		
    		// check if object exists
    		if (!$active_object || $active_object->isNew()) {
    			$this->response->notFound();
    		} // if
    		
    		// check if we can reoreder subtasks
    		if (!$active_object->canEdit($this->logged_user)) {
    			$this->response->forbidden();
    		} // if
    		
    		try {   		
	    		$subtask_ids = $this->request->post('subtask');
	    		if (is_foreachable($subtask_ids)) {
	    			$counter = 0;
	    			foreach ($subtask_ids as $subtask_id) {
	    				DB::execute('UPDATE `' . TABLE_PREFIX . 'subtasks` SET `position` = ?, parent_id = ?, parent_type = ? WHERE `id` = ?', $counter, $active_object->getId(), $active_object->getType(), $subtask_id);
	    				$counter++;
	    			} // foreach
	    			
	    			$result = Subtasks::findByIds($subtask_ids);
	    		} // if
    		} catch (Exception $e) {
					$this->response->exception($e);    			
    		} // try
    	} else {
   			// object type is not supported, return bad request
   			$this->response->badRequest();
    	} // if
    	
    	$this->response->respondWithData($result, array('detailed' => true));
    } // reorder
    
    /**
     * Shortcuts page
     */
    function shortcuts() {
      
    } // shortcuts
    
  }