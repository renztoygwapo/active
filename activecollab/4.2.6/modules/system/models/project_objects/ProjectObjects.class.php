<?php

  /**
   * ProjectObjects class
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ProjectObjects extends BaseProjectObjects {

    /**
     * Returns true if $user can access specific project section
     * 
     * @param IUser $user
     * @param Project $project
     * @param string $permission
     * @param string $tab_name
     * @return boolean
     */
    static function canAccess(IUser $user, Project $project, $permission, $tab_name = null) {
      if($user instanceof User && $project instanceof Project) {
        if($user->projects()->getPermission($permission, $project) >= ProjectRole::PERMISSION_ACCESS) {
          return $tab_name ? $project->hasTab($tab_name, $user) : true;
        } // if
      } // if
      
      return false;
    } // canAccess
    
    /**
     * Returns true if $user can add object of a given class to $project
     * 
     * @param IUser $user
     * @param Project $project
     * @param string $permission
     * @param string $tab_name
     * @return boolean
     */
    static function canAdd(IUser $user, Project $project, $permission, $tab_name = null) {
      if($user instanceof User && $project instanceof Project) {
        if($user->projects()->getPermission($permission, $project) >= ProjectRole::PERMISSION_CREATE) {
          return $tab_name ? $project->hasTab($tab_name, $user) : true;
        } // if
      } // if
      
      return false;
    } // canAdd
    
    /**
     * Returns true if $user can manage object in this section of $project
     * 
     * @param IUser $user
     * @param Project $project
     * @param string $permission
     * @param string $tab_name
     * @return boolean
     */
    static function canManage(IUser $user, Project $project, $permission, $tab_name = null) {
      if($user instanceof User && $project instanceof Project) {
        if($user->projects()->getPermission($permission, $project) >= ProjectRole::PERMISSION_MANAGE) {
          return $tab_name ? $project->hasTab($tab_name, $user) : true;
        } // if
      } // if
      
      return false;
    } // canManage
    
    // ---------------------------------------------------
    //  Utilities
    // ---------------------------------------------------
    
    /**
     * Cached array of class names and is project object class values
     *
     * @var array
     */
    static private $is_project_object_class = array();
    
    /**
     * Returns true if $class_name is project object class
     *
     * @param string $class_name
     * @return boolean
     */
    static function isProjectObjectClass($class_name) {
      if($class_name == 'ProjectObject') {
        return true;
      } // if
      
      if(!isset(self::$is_project_object_class[$class_name])) {
        $class = new ReflectionClass($class_name);
        
        self::$is_project_object_class[$class_name] = $class->isSubclassOf('ProjectObject');
      } // if
      
      return self::$is_project_object_class[$class_name];
    } // isProjectObjectClass

    /**
     * Cached value
     *
     * @var bool
     */
    static private $is_multiple_assignees_support_enabled = null;

    /**
     * Returns true if multiple assignees support is enabled
     *
     * @return bool
     */
    static function isMultipleAssigneesSupportEnabled() {
      if(self::$is_multiple_assignees_support_enabled === null) {
        self::$is_multiple_assignees_support_enabled = (boolean) ConfigOptions::getValue('multiple_assignees_for_milestones_and_tasks');
      } // if

      return self::$is_multiple_assignees_support_enabled;
    } // isMultipleAssigneesSupportEnabled
    
    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------
    
    /**
     * Find project objects by list of ID-s
     *
     * @param array $ids
     * @param integer $min_state
     * @param integer $min_visibility
     * @return ProjectObject[]
     */
    static function findByIds($ids, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('id IN (?) AND state >= ? AND visibility >= ?', $ids, $min_state, $min_visibility),
        'order' => 'created_on DESC',
      ));
    } // findByIds
    
    /**
     * Paginate objects by object ID-s
     *
     * @param array $ids
     * @param int $page
     * @param int $per_page
     * @param int $min_state
     * @param int $min_visibility
     * @return array
     */
    static function paginateByIds($ids, $page = 1, $per_page = 10, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::paginate(array(
        'conditions' => array('id IN (?) AND state >= ? AND visibility >= ?', $ids, $min_state, $min_visibility),
        'order' => 'created_on DESC',
      ), $page, $per_page);
    } // paginateByIds

    /**
     * Return visible and archived tasks in current project that given $user can
     * access
     *
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return ProjectObject[]
     */
    static function findByProject(Project $project, $min_state = STATE_VISIBLE, $min_visibility = STATE_VISIBLE) {
      return ProjectObjects::find(array(
        "conditions" => array('project_id = ? AND state >= ? AND visibility >= ?', $project->getId(), $min_state, $min_visibility),
        "order" => "priority DESC"
      ));
    } // findByProject
    
    /**
     * Return project objects by a given milestone
     * 
     * @param Milestone $milestone
     * @param integer $min_state
     * @return DBResult
     */
    static function findByMilestone(Milestone $milestone, $min_state = STATE_VISIBLE) {
      return ProjectObjects::find(array(
        'conditions' => array('milestone_id = ? AND project_id = ? AND state >= ?', $milestone->getId(), $milestone->getProjectId(), $min_state), // Make sure that milestone ID and project ID are the same (milestone_id is not enough due to an old activeCollab 2 data integrity issue)
        'order' => 'ISNULL(position) DESC, position',
      ));
    } // findByMilestone
    
    /**
     * Return project objects that don't have a milestone field set
     *
     * @param Project $project
     * @param integer $min_state
     * @return ProjectObject[]
     */
    static function findWithoutMilestone(Project $project, $min_state = STATE_VISIBLE) {
      return ProjectObjects::find(array(
        'conditions' => array('type != ? AND project_id = ? AND (milestone_id IS NULL OR milestone_id = ?) AND state >= ?', 'Milestone', $project->getId(), 0, $min_state),
        'order' => 'ISNULL(position) DESC, position',
      ));
    } // findWithoutMilestone
    
    /**
     * Returns ids and names of all objects in $project which are type specified in $type arrray
     *
     * @param Project $project
     * @param array $types
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    static function getIdNameMapByProject(Project $project, $types, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      $map = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'project_objects WHERE project_id = ? AND type IN (?) AND state >= ? AND visibility >= ? ORDER BY name', $project->getId(), $types, $min_state, $min_visibility);
      if (!is_foreachable($map)) {
        return null;
      } // if
      
      $formatted_map = array();
      foreach ($map as $map_element) {
      	$formatted_map[$map_element['id']] = $map_element['name'];
      } // foreach
            
      return $formatted_map;
    } // getIdNameMapForProject

    /**
     * Find project object for select box - only id and name
     *
     * @param Project $project
     * @param string $type
     * @param int $min_state
     * @param int $min_visibility
     * @return array
     */
    static function findForSelectBoxByProject(Project $project, $type, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      $results = DB::execute("SELECT id, name FROM " . TABLE_PREFIX . "project_objects WHERE type = ? AND project_id = ? AND state >= ? AND visibility >= ? ORDER BY name", $type, $project->getId(), $min_state, $min_visibility);
      $return = null;
      if(is_foreachable($results)) {
        foreach($results as $item) {
          $return[$item['id']] = $item['name'];
        } //foreach
      } //if
      return $return;
    } // findForSelectBoxByProject
    
    // ---------------------------------------------------
    //  Late, today, upcoming
    // ---------------------------------------------------
    
    /**
     * Return late and today object
     *
     * @param User $user
     * @param Project $project
     * @param array $types
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    static function findLateAndToday($user, $project = null, $types = null, $page = null, $per_page = null) {
      if($project instanceof Project) {
        $type_filter = $user->projects()->getVisibleTypesFilterByProject($project, $types);
      } else {
        $type_filter = $user->projects()->getVisibleTypesFilter(Project::STATUS_ACTIVE, $types);
      } // if

      if($type_filter) {
        $today = new DateValue(time() + get_user_gmt_offset());
        
        $conditions = array($type_filter . ' AND due_on <= ? AND state >= ? AND visibility >= ? AND completed_on IS NULL', $today, STATE_VISIBLE, $user->getMinVisibility());
        
        if($page !== null && $per_page !== null) {
          return ProjectObjects::paginate(array(
            'conditions' => $conditions,
            'order' => 'due_on, priority DESC',
          ), $page, $per_page);
        } else {
          return ProjectObjects::find(array(
            'conditions' => $conditions,
            'order' => 'due_on, priority DESC',
          ));
        } // if
      } // if
      
      return null;
    } // findLateAndTodayByProject
    
    /**
     * Return number of late milestones
     *
     * @param User $user
     * @param Project $project
     * @param array $types
     * @return integer
     */
    static function countLate($user, $project = null, $types = null) {
      $in_project = is_null($project) ? Project::STATUS_ACTIVE : $project;
      $type_filter = $user->projects()->getVisibleTypesFilter($in_project, $types);
      if ($type_filter) {
        return ProjectObjects::count(array($type_filter . ' AND due_on < ? AND state >= ? AND visibility >= ? AND completed_on IS NULL', new DateValue(time() + get_user_gmt_offset()), STATE_VISIBLE, $user->getMinVisibility()));
      } // if
      return 0;
    } // countLate
    
    /**
     * Return number that are due for today
     *
     * @param User $user
     * @param Project $project
     * @param array $types
     * @return integer
     */
    static function countToday($user, $project = null, $types = null) {
      $in_project = is_null($project) ? Project::STATUS_ACTIVE : $project;
      $type_filter = $user->projects()->getVisibleTypesFilter($in_project, $types);
      if ($type_filter) {
        return ProjectObjects::count(array($type_filter . ' AND due_on = ? AND state >= ? AND visibility >= ? AND completed_on IS NULL', new DateValue(time() + get_user_gmt_offset()), STATE_VISIBLE, $user->getMinVisibility()));
      } // if
      return 0;
    } // countToday
    
    /**
     * Return upcoming objects in a given projects
     *
     * @param User $user
     * @param Project $project
     * @param array $types
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    static function findUpcoming($user, $project = null, $types = null, $page = null, $per_page = null) {
      if($project instanceof Project) {
        $type_filter = $user->projects()->getVisibleTypesFilterByProject($project, $types);
      } else {
        $type_filter = $user->projects()->getVisibleTypesFilter(Project::STATUS_ACTIVE, $types);
      } // if

      if($type_filter) {
        $today = new DateTimeValue();
        $today->advance(get_user_gmt_offset());
        
        $newer_than = $today->endOfDay();
        
        $conditions = array($type_filter . ' AND due_on > ? AND state >= ? AND visibility >= ? AND completed_on IS NULL', $newer_than, STATE_VISIBLE, $user->getMinVisibility());
        
        if($page !== null && $per_page !== null) {
          return ProjectObjects::paginate(array(
            'conditions' => $conditions,
            'order' => 'due_on, priority DESC',
          ), $page, $per_page);
        } else {
          return ProjectObjects::find(array(
            'conditions' => $conditions,
            'order' => 'due_on, priority DESC',
          ));
        } // if
      } // if
      
      return null;
    } // findUpcoming

    // ---------------------------------------------------
    //  Search
    // ---------------------------------------------------

    /**
     * Rebuild project objects search index for $project
     *
     * @param Project $project
     * @param SearchIndex $index
     */
    static function rebuildProjectSearchIndex(Project $project, SearchIndex $index) {
      $users_map = $project->users()->getIdNameMap(); // Prepare users map
      $milestones_map = array(); // Load and index milestones

      $milestones = DB::execute("SELECT id, name, body, visibility, assignee_id, priority, due_on, completed_on FROM " . TABLE_PREFIX . "project_objects WHERE type = 'Milestone' AND project_id = ? AND state >= ?", $project->getId(), STATE_ARCHIVED);

      if($milestones) {
        $item_context = 'projects:projects/' . $project->getId() . '/milestones';

        $project_name = $project->getName(); // Lets shave off a couple of function calls

        foreach($milestones as $milestone) {
          $milestone_id = (integer) $milestone['id'];
          $assignee_id = $milestone['assignee_id'] ? (integer) $milestone['assignee_id'] : null;

          Search::set($index, array(
            'class' => 'Milestone',
            'id' => $milestone_id,
            'context' => $item_context,
            'project_id' => $project->getId(),
            'project' => $project_name,
            'name' => $milestone['name'],
            'body' => $milestone['body'] ? $milestone['body'] : null,
            'visibility' => $milestone['visibility'],
            'assignee_id' => $assignee_id,
            'assignee' => $assignee_id && isset($users_map[$assignee_id]) ? $users_map[$assignee_id] : null,
            'priority' => (integer) $milestone['priority'],
            'due_on' => $milestone['due_on'],
            'completed_on' => $milestone['completed_on'],
          ));

          $milestones_map[$milestone_id] = $milestone['name'];
        } // foreach
      } // if

      // Trigger event that notifies all modules to rebuild index for a given projects
      EventsManager::trigger('on_build_project_search_index', array(&$index, &$project, &$users_map, &$milestones_map));
    } // rebuildProjectSearchIndex

    /**
     * Return comments and subtasks for search index
     *
     * @param array|DBResult $project_objects
     * @param boolean $get_comments
     * @param boolean $get_subtasks
     * @param int $min_state
     * @return array
     * @throws InvalidParamError
     */
    static function getCommentsAndSubtasksForSearch($project_objects, $get_comments = false, $get_subtasks = false, $min_state = STATE_ARCHIVED) {
      $comments = $subtasks = array();

      if(is_foreachable($project_objects) && ($get_comments || $get_subtasks)) {
        $type_ids = array();

        foreach($project_objects as $project_object) {
          if(!isset($project_object['type'])) {
            throw new InvalidParamError('project_objects', $project_objects, 'Value of "type" field is needed for this function to work');
          } // if

          $type = $project_object['type'];

          if(isset($type_ids[$type])) {
            $type_ids[$type][] = (integer) $project_object['id'];
          } else {
            $type_ids[$type] = array((integer) $project_object['id']);
          } // if
        } // foreach

        $type_filter = array();

        foreach($type_ids as $type => $ids) {
          $type_filter[] = DB::prepare('(parent_type = ? AND parent_id IN (?))', $type, $ids);
        } // foreach

        $type_filter = '(' . implode(' OR ', $type_filter) . ')';

        if($get_comments) {
          $rows = DB::execute('SELECT id, parent_type, parent_id, body FROM ' . TABLE_PREFIX . 'comments WHERE state >= ? AND ' . $type_filter, $min_state);

          if($rows) {
            foreach($rows as $row) {
              $type = $row['parent_type'];

              if(isset($comments[$type])) {
                $comments[$type][(integer) $row['parent_id']] .= ' ' . $row['body'];
              } else {
                $comments[$type] = array(
                  (integer) $row['parent_id'] => $row['body'],
                );
              } // if
            } // foreach
          } // if
        } // if

        if($get_subtasks) {
          $rows = DB::execute('SELECT id, parent_type, parent_id, body FROM ' . TABLE_PREFIX . 'subtasks WHERE state >= ? AND ' . $type_filter, $min_state);

          if($rows) {
            foreach($rows as $row) {
              $type = $row['parent_type'];

              if(isset($subtasks[$type])) {
                $subtasks[$type][(integer) $row['parent_id']] .= ' ' . $row['body'];
              } else {
                $subtasks[$type] = array(
                  (integer) $row['parent_id'] => $row['body'],
                );
              } // if
            } // foreach
          } // if
        } // if
      } // if

      return array($comments, $subtasks);
    } // getCommentsAndSubtasksForSearch
    
    // ---------------------------------------------------
    //  Trash
    // ---------------------------------------------------
        
    /**
     * Get trashed map
     * 
     * @param User $user
     * @return array
     */
    static function getTrashedMap($user) {
    	$trashed_project_objects = DB::execute('SELECT id, type FROM ' . TABLE_PREFIX . 'project_objects WHERE state = ?', STATE_TRASHED);
    	    	
    	if (!is_foreachable($trashed_project_objects)) {
    		return null;
    	} // if
    	
    	$result = array();
    	
    	foreach ($trashed_project_objects as $trashed_project_object) {
    		$type = strtolower($trashed_project_object['type']);
    		
    		if (!isset($result[$type])) {
    			$result[$type] = array();
    		} // if 
    		
    		$result[$type][] = $trashed_project_object['id'];
    	} // foreach
    	
    	return $result;
    } // getTrashedMap
       
    /**
     * Find trashed project objects
     *
     * @param User $user
     * @param array $map
     * @return DBResult
     */
    static function findTrashed($user, &$map) {
    	$skip_project_ids = array_var($map, 'project');
    	
    	if (is_foreachable($skip_project_ids)) {
  			$trashed_project_objects = DB::execute('SELECT id, name, project_id, type, integer_field_1 FROM ' . TABLE_PREFIX . 'project_objects WHERE state = ? AND project_id NOT IN (?) ORDER BY updated_on DESC', STATE_TRASHED, $skip_project_ids);
    	} else {
    		$trashed_project_objects = DB::execute('SELECT id, name, project_id, type, integer_field_1 FROM ' . TABLE_PREFIX . 'project_objects WHERE state = ? ORDER BY updated_on DESC', STATE_TRASHED);
    	} // if
    	
    	if (!is_foreachable($trashed_project_objects)) {
    		return null;
    	} // if

    	// extract project we need
    	$needed_project_ids = DB::executeFirstColumn('SELECT DISTINCT(project_id) FROM ' . TABLE_PREFIX . 'project_objects WHERE state = ? AND project_id NOT IN (?) ORDER BY updated_on', STATE_TRASHED, $skip_project_ids);
    	$needed_projects_id_slug_map = Projects::getIdDetailsMap('slug', $needed_project_ids);
    	
    	// urls container
    	$view_urls = array();
    	
    	$items = array();
    	foreach ($trashed_project_objects as $object) {
        try {
          class_exists($object['type']);
        } catch (Exception $e) {
          continue;
        } //try

    		$type = strtolower($object['type']);
    		
    		// we need to create url templates for this object type
    		if (!isset($view_urls[$type])) {
    			$underscored_type = Inflector::underscore($object['type']);
    			
    			$routing_context = 'project_' . $underscored_type;
    			$routing_object_id =  $underscored_type . '_id';
    			if (is_subclass_of($object['type'], 'ProjectAsset')) {
    				$routing_context = 'project_assets_' . $underscored_type;
    				$routing_object_id =  'asset_id';	
    			} // if
    			
	    		$view_urls[$type] = Router::assemble($routing_context, array('project_slug' => '--PROJECT-SLUG--', $routing_object_id => '--OBJECT-ID--'));
    		}; // if
    		
    		// determine ID which will be used in url generating
    		$route_object_id = $object['id'];
    		if ($type == 'task') {
    			$route_object_id = $object['integer_field_1'];
    		};
    		
    		// determine project slug for url generating
    		$route_object_project_slug = $needed_projects_id_slug_map[$object['project_id']]['slug'];
    		
    		$items[] = array(
    			'id'					=> $object['id'],
    			'name'				=> $object['name'],
    			'type' 				=> $object['type'],
    			'permalink'		=> str_replace(array('--PROJECT-SLUG--', '--OBJECT-ID--'), array($route_object_project_slug, $route_object_id), $view_urls[$type]),    		
    		);
    	} // foreach
    	
    	return $items;
    } // findTrashed
    
    /**
     * Delete trashed project objects visible to $user
     
     * @param User $user
     * @return boolean
     */
    static function deleteTrashed(User $user) {
    	$project_objects = ProjectObjects::find(array(
    		'conditions' => array('state = ?', STATE_TRASHED)
    	));
    	
    	if (is_foreachable($project_objects)) {
    		foreach ($project_objects as $project_object) {
    			$project_object->state()->delete();
    		} // foreach
    	} // if
   
   		return true; 	
    } // deleteTrashed
    
    /**
     * Delete all objects by project
     *
     * @param Project $project
     * @param boolean $force_delete
     * @return boolean
     */
    static function deleteByProject(Project $project, $force_delete = false) {
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      
      $rows = DB::execute("SELECT id, type FROM $project_objects_table WHERE project_id = ?", $project->getId());
      
      if($rows) {
        $parents = array();
        
        foreach($rows as $row) {
          if(isset($parents[$row['type']])) {
            $parents[$row['type']][] = (integer) $row['id'];
          } else {
            $parents[$row['type']] = array((integer) $row['id']);
          } // if
        } // foreach

        try {
          DB::beginWork('Deleting project data @ ' . __CLASS__);
          
          Reminders::deleteByParents($parents); 
          ActivityLogs::deleteByParents($parents);
          Comments::deleteByParents($parents); 
          Subtasks::deleteByParents($parents); 
          Attachments::deleteByParents($parents);
          Assignments::deleteByParents($parents);
          Subscriptions::deleteByParents($parents);
          Favorites::deleteByParents($parents); 
          SharedObjectProfiles::deleteByParents($parents);
          ModificationLogs::deleteByParents($parents);
          
          if(AngieApplication::isModuleLoaded('tracking')) {
            Estimates::deleteByParents($parents);
            
            $parents['Project'] = array($project->getId()); // Also delete time records and expenses attached directly to project
            
            Expenses::deleteByParents($parents);
            TimeRecords::deleteByParents($parents);
          } // if

          if($force_delete) {
            if(isset($parents['File']) && is_foreachable($parents['File'])) {
              Files::forceDeleteByIds($parents['File']);
            } // if

            if(isset($parents['Notebook']) && is_foreachable($parents['Notebook'])) {
              NotebookPages::forceDeleteByParents($parents['Notebook']);
            } // if

            self::deleteByParents($parents);
          } // if
          
          DB::commit('Project data delete @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to delete project data @ ' . __CLASS__);
          throw $e;
        } // try
      } // if
    } // deleteByProject

    /**
     * Delete entries by parents
     *
     * $parents is an array where key is parent type and value is array of
     * object ID-s of that particular parent
     *
     * @param array $parents
     */
    static function deleteByParents($parents) {
      try {
        DB::beginWork('Removing project objects by type and IDs @ ' . __CLASS__);

        if(is_foreachable($parents)) {
          foreach($parents as $parent_type => $parent_ids) {
            DB::execute('DELETE FROM ' . TABLE_PREFIX . 'project_objects WHERE id IN (?) AND type = ?', $parent_ids, $parent_type);
          } // foreach
        } // if

        DB::commit('Project objects removed by type and IDs @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::commit('Failed to delete project objects by type and IDs @ ' . __CLASS__);
        throw $e;
      } // try
    } // deleteByParents
    
    // ---------------------------------------------------
    //  Group
    // ---------------------------------------------------
    
    /**
     * Group objects by project
     *
     * @param array $objects
     * @return array
     */
    static function groupByProject($objects) {
      $result = array();
      
      if(is_foreachable($objects)) {
        $project_ids = objects_array_extract($objects, 'getProjectId');
        if(is_foreachable($project_ids)) {
          $projects = Projects::findByIds($project_ids);
          if(is_foreachable($projects)) {
            foreach($projects as $project) {
              $result[$project->getId()] = array(
                'project' => $project,
                'objects' => array(),
              );
            } // foreach
          } // if
        } // if
        
        foreach($objects as $object) {
          if(isset($result[$object->getProjectId()])) {
            $result[$object->getProjectId()]['objects'][] = $object;
          } // if
        } // foreach
      } // if
      
      return $result;
    } // groupByProject
    
  }