<?php

  /**
   * User projects helper implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IUserProjectsImplementation {
    
    /**
     * User instance
     *
     * @var User
     */
    protected $object;
    
    /**
     * Construct user projects implementation
     *
     * @param User $object
     * @throws InvalidInstanceError
     */
    function __construct(User $object) {
      if($object instanceof User) {
        $this->object = $object;
      } else {
        throw new InvalidInstanceError('object', $object, 'User');
      } // if
    } // __construct
    
    /**
     * Return projects
     *
     * @return Project[]
     */
    function get() {
      return Projects::findByUser($this->object, false, DB::prepare('state >= ?', STATE_VISIBLE));
    } // get
    
    /**
     * Return project ID-s
     *
     * @param boolean $all_for_admins_and_pms
     * @return array
     */
    function getIds($all_for_admins_and_pms = false) {
      return Projects::findIdsByUser($this->object, $all_for_admins_and_pms, DB::prepare('state >= ?', STATE_VISIBLE));
    } // getIds
    
    /**
     * Return type filter for this user in projects based on user's permissions
     * 
     * If $only_types is present system will include only types listed in that 
     * array and ignore other no matter the permissions
     *
     * @param string $projects
     * @param null $only_types
     * @param bool $use_cache
     * @return array
     */
    function getVisibleTypesFilter($projects = Project::STATUS_ANY, $only_types = null, $use_cache = true) {
      if(is_array($projects)) {
        if(count($projects)) {
          $projects_cache_key = implode('-', $projects);
        } else {
          return '';
        } // if
      } else {
        $projects_cache_key = $projects;
      } // if
      
      if($only_types) {
        $types_cache_key = implode('-', $only_types);
      } else {
        $types_cache_key = 'all_types';
      } // if
      
      // Get and prepare cached value
      $cached_value = AngieApplication::cache()->getByObject($this->object, 'visible_types_filter');
      
      if(is_array($cached_value)) {
        if(!isset($cached_value[$projects_cache_key])) {
          $cached_value[$projects_cache_key] = array();
        } // if
      } else {
        $cached_value = array($projects_cache_key => array());
      } // if
      
      if($use_cache && isset($cached_value[$projects_cache_key]) && isset($cached_value[$projects_cache_key][$types_cache_key])) {
        return $cached_value[$projects_cache_key][$types_cache_key];
      } // if
      
      $projects_table = TABLE_PREFIX . 'projects';
      $project_users_table = TABLE_PREFIX . 'project_users';
      
      if(is_array($projects)) {
        $projects_filter = DB::prepare(" AND $projects_table.id IN (?)", $projects);
      } else {
        switch($projects) {
          case Project::STATUS_ACTIVE:
            $projects_filter = " AND $projects_table.completed_on IS NULL";
            break;
          case Project::STATUS_COMPLETED:
            $projects_filter = " AND $projects_table.completed_on IS NOT NULL";
            break;
          default:
            $projects_filter = '';
        } // switch
      } // if
      
      $rows = DB::execute("SELECT $project_users_table.project_id, $project_users_table.role_id, $project_users_table.permissions, $projects_table.leader_id FROM $project_users_table, $projects_table WHERE $project_users_table.user_id = ? AND $project_users_table.project_id = $projects_table.id $projects_filter", $this->object->getId());
      if($rows instanceof DBResult) {
        $rows->setCasting(array(
          'project_id' => DBResult::CAST_INT, 
          'role_id' => DBResult::CAST_INT, 
          'leader_id' => DBResult::CAST_INT, 
        ));
                
        $escaped_only_types = $only_types !== null ? DB::escape($only_types) : '';
        
        $project_objects_table = TABLE_PREFIX . 'project_objects';
        
        // If we have administrators or project managers lets skip all the dirty 
        // work with roles and permissions
        if($this->object->isAdministrator() || $this->object->isProjectManager()) {
          $result = array();
          foreach($rows as $row) {
            $project_id = (integer) $row['project_id'];
            
            if($only_types === null) {
              $result[] = "($project_objects_table.project_id = $project_id)";
            } else {
              $result[] = "($project_objects_table.project_id = $project_id AND $project_objects_table.type IN ($escaped_only_types))";
            } // if
          } // if
          
          $cached_value[$projects_cache_key][$types_cache_key] = '(' . implode(' OR ', $result) . ')';

          AngieApplication::cache()->setByObject($this->object, 'visible_types_filter', $cached_value);
          
          return $cached_value[$projects_cache_key][$types_cache_key];
        } // if
        
        // Load roles data
        $role_ids = array();
        foreach($rows as $row) {
          $role_id = (integer) $row['role_id'];
          if($role_id && !in_array($role_id, $role_ids)) {
            $role_ids[] = $role_id;
          } // if
        } // foreach
        
        if(is_foreachable($role_ids)) {
          $roles = ProjectRoles::findByIds($role_ids);
          $roles = $roles instanceof DBResult ? $roles->toArrayIndexedBy('getId') : array();
        } else {
          $roles = array();
        } // if
        
        $result = array();
        foreach($rows as $row) {
          
          // We have a project leader
          if($this->object->getId() == $row['leader_id']) {
            $project_id = (integer) $row['project_id'];
            
            if($only_types === null) {
              $result[] = "($project_objects_table.project_id = '$project_id')";
            } else {
              $result[] = "($project_objects_table.project_id = '$project_id' AND $project_objects_table.type IN ($escaped_only_types))";
            } // if
            
          // Regular user
          } else {
            
            // Role or custom permissions
            if($row['role_id']) {
              $role = $roles[$row['role_id']];
              if($role instanceof ProjectRole) {
                $permissions = $role->getPermissions();
              } else {
                $permissions = array();
              } // if
            } else {
              $permissions = $row['permissions'] ? unserialize($row['permissions']) : array();
            } // if
            
            // Get types and prepare result parts
            if(!empty($permissions)) {
              $types = array();
              foreach($permissions as $permission_name => $permission_value) {
                $type_name = Inflector::camelize($permission_name);
                
                if($permission_value >= ProjectRole::PERMISSION_ACCESS) {
                  if($only_types !== null && !in_array($type_name, $only_types)) {
                    continue;
                  } // if
                  
                  $types[] = $type_name;
                } // if
              } // foreach
              
              if(count($types)) {
          	    $result[] = DB::prepare("($project_objects_table.project_id = ? AND $project_objects_table.type IN (?))", $row['project_id'], $types);
              } // if
            } // if
          } // if
        } // foreach
        
        $cached_value[$projects_cache_key][$types_cache_key] = empty($result) ? '' : '(' . implode(' OR ', $result) . ')';
        AngieApplication::cache()->setByObject($this->object, 'visible_types_filter', $cached_value);
        
        return $cached_value[$projects_cache_key][$types_cache_key];
      } // if
      
      $cached_value[$projects_cache_key][$types_cache_key] = '';
      AngieApplication::cache()->setByObject($this->object, 'visible_types_filter', $cached_value);
      
      return '';
    } // getVisbleTypesFilter
    
    /**
     * Return visible types filter by project
     *
     * @param Project $project
     * @param array $only_types
     * @param boolean $use_cache
     * @return string
     */
    function getVisibleTypesFilterByProject(Project $project, $only_types = null, $use_cache = true) {
      $project_id = $project->getId();
      
      if($only_types) {
        $cache_key = implode('-', $only_types);
      } else {
        $cache_key = 'all_types';
      } // if
      
      // Get and prepare cached value
      $cached_value = AngieApplication::cache()->getByObject($this->object, 'visible_project_types_filter');

      if(is_array($cached_value)) {
        if(!isset($cached_value[$project_id])) {
          $cached_value[$project_id] = array();
        } // if
      } else {
        $cached_value = array(
          $project->getId() => array()
        );
      } // if
      
      // From cache?
      if($use_cache && isset($cached_value[$cache_key])) {
        return $cached_value[$cache_key];
      } // if
      
      // Nope, load...
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      
      if($this->object->isProjectManager() || $project->isLeader($this->object)) {
        if($only_types === null) {
          $filter = "($project_objects_table.project_id = $project_id)";
        } else {
          $filter = DB::prepare("($project_objects_table.project_id = $project_id AND type IN (?))", $only_types);
        } // if
        
        // Add to cache and return
        $cached_value[$project_id][$cache_key] = $filter;
        AngieApplication::cache()->setByobject($this->object, 'visible_project_types_filter', $cached_value);
        
        return $filter;
      } // if
      
    	$types = $this->getVisibleTypesByProject($project);

      // Camelize type names
      foreach($types as $k => $v) {
        $types[$k] = Inflector::camelize($v);
      } // foreach

    	if($only_types !== null) {
      	foreach($types as $k => $v) {
      	  if(!in_array($v, $only_types)) {
      	    unset($types[$k]);
      	  } // if
      	} // foreach
    	} // if
    	
    	if(is_foreachable($types)) {
    	  $project_id = $project->getId();
        $escaped_types = DB::escape($types);
        
        $filter = "($project_objects_table.project_id = '$project_id' AND $project_objects_table.type IN ($escaped_types))";
        
        $cached_value[$project_id][$cache_key] = $filter;
        AngieApplication::cache()->setByobject($this->object, 'visible_project_types_filter', $cached_value);
    	  
    	  return $filter;
    	} else {
    	  $cached_value[$project_id][$cache_key] = '';
        AngieApplication::cache()->setByobject($this->object, 'visible_project_types_filter', $cached_value);
    	  
    	  return '';
    	} // if
    } // getVisibleTypesFilterByProject
    
    /**
     * Return top level types user can see in $project
     *
     * @param Project $project
     * @param boolean $use_cache
     * @return array
     */
    function getVisibleTypesByProject(Project $project, $use_cache = true) {
      $project_id = $project->getId();
      
      $cached_value = AngieApplication::cache()->getByObject($this->object, 'visible_project_types');
      if(!is_array($cached_value)) {
        $cached_value = array();
      } // if
      
      if($use_cache && isset($cached_value[$project_id])) {
        return $cached_value[$project_id];
      } // if
      
      if($this->object->isProjectManager() || $project->isLeader($this->object)) {
        $cached_value[$project_id] = array_keys(ProjectRoles::getPermissions());
        AngieApplication::cache()->setByObject($this->object, 'visible_project_types', $cached_value);
        
        return $cached_value[$project_id];
      } // if
      
      $row = DB::executeFirstRow('SELECT role_id, permissions FROM ' . TABLE_PREFIX . 'project_users WHERE user_id = ? AND project_id = ?', $this->object->getId(), $project->getId());
      if($row) {
        $role = $row['role_id'] ? ProjectRoles::findById($row['role_id']) : null;
        
        if($role instanceof ProjectRole) {
          $permissions = $role->getPermissions();
        } else {
          $permissions = unserialize($row['permissions']);
        } // if
        
        if(is_array($permissions)) {
          $types = array();
          foreach($permissions as $permission_name => $permission_value) {
            if($permission_value >= ProjectRole::PERMISSION_ACCESS) {
              $types[] = $permission_name;
            } // if
          } // foreach
          
          $cached_value[$project_id] = $types;
          AngieApplication::cache()->setByObject($this->object, 'visible_project_types', $cached_value);
          
          return $cached_value[$project_id];
        } // if
      } // if
      
      $cached_value[$project_id] = array();
      AngieApplication::cache()->setByObject($this->object, 'visible_project_types', $cached_value);
      
      return array();
    } // getVisibleTypesByProject
    
    /**
     * Return project roles map for a given user
     * 
     * Returns associative array where key is project ID and value is array of 
     * permissions user has in a given project. System permissions like 
     * administrator, project manager etc are ignored
     *
     * @param string $status
     * @return array
     */
    function getRolesMap($status = Project::STATUS_ANY) {
      $project_users_table = TABLE_PREFIX . 'project_users';
      $projects_table = TABLE_PREFIX . 'projects';
      
      switch($status) {
        case Project::STATUS_ACTIVE:
          $status_filter = " AND $projects_table.completed_on IS NULL";
          break;
        case Project::STATUS_COMPLETED:
          $status_filter = " AND $projects_table.completed_on IS NOT NULL";
          break;
        default:
          $status_filter = '';
      } // switch
      
      $rows = DB::execute("SELECT $project_users_table.*, $projects_table.name AS 'project_name', $projects_table.leader_id AS 'project_leader' FROM $projects_table, $project_users_table WHERE $projects_table.id = $project_users_table.project_id AND $project_users_table.user_id = ? $status_filter ORDER BY $projects_table.name", $this->object->getId());
    	if(is_foreachable($rows)) {
    	  $result = array();
    	  $roles = array();
    	  
    	  foreach($rows as $row) {
    	    $project_id = (integer) $row['project_id'];
    	    $role_id = (integer) $row['role_id'];
    	    
    	    // From role
    	    if($role_id) {
    	      if(!isset($roles[$role_id])) {
    	        $role_row = DB::executeFirstRow('SELECT permissions FROM ' . TABLE_PREFIX . 'project_roles WHERE id = ?', $role_id);
    	        if($role_row && isset($role_row['permissions'])) {
    	          $roles[$role_id] = $role_row['permissions'] ? unserialize($role_row['permissions']) : array();
    	        } else {
    	          $roles[$role_id] = array();
    	        } // if
    	      } // if
    	      $result[$project_id] = array(
    	        'name' => $row['project_name'],
    	        'leader' => $row['project_leader'],
    	        'permissions' => $roles[$role_id],
    	      );
    	      
    	    // From permissions
    	    } else {
    	      $result[$project_id] = array(
    	        'name' => $row['project_name'],
    	        'leader' => $row['project_leader'],
    	        'permissions' => $row['permissions'] ? unserialize($row['permissions']) : array()
    	      );
    	    } // if
    	  } // foreach
    	  
    	  return $result;
    	} // if
    	
    	return null;
    } // getRolesMap
    
    /**
     * Cached project data
     *
     * @var array
     */
    private $project_data = array();
    
    /**
     * Return project data for this user
     * 
     * The data is return as two elements array - first element is role instance 
     * and second one is array of permissions
     *
     * @param Project $project
     * @return array
     */
    protected function getProjectData(Project $project) {
      $project_id = $project->getId();
      
      if(!isset($this->project_data[$project_id])) {
        $row = DB::executeFirstRow('SELECT role_id, permissions FROM ' . TABLE_PREFIX . 'project_users WHERE user_id = ? AND project_id = ?', $this->object->getId(), $project->getId());
        if(empty($row)) {
          $this->project_data[$project_id] = array(null, null);
        } else {
          $role = $row['role_id'] ? ProjectRoles::findById($row['role_id']) : null;
          
          if($role instanceof ProjectRole) {
            $this->project_data[$project_id] = array($role, null);
          } else {
            $this->project_data[$project_id] = array(null, unserialize($row['permissions']));
          } // if
        } // if
      } // if
      
      return $this->project_data[$project_id];
    } // getProjectData

    /**
     * Clear in memory cache
     */
    function clearProjectDataCache() {
      $this->project_data = array();
    } // clearProjectDataCache
    
    /**
     * Return project role for a given project
     *
     * @param Project $project
     * @return ProjectRole
     */
    function getRole(Project $project) {
      $project_data = $this->getProjectData($project);
      return $project_data[0];
    } // getRole
    
    /**
     * Return verbose role name
     *
     * @param Project $project
     * @return string
     */
    function getRoleName(Project $project) {
      $role = $this->getRole($project);
      
      if($role instanceof ProjectRole) {
        return $role->getName();
      } else {
        if($project->isLeader($this->object)) {
          return lang('Project Leader');
        } else if($this->object->isAdministrator()) {
          return lang('System Administrator');
        } elseif($this->object->isProjectManager()) {
          return lang('Project Manager');
        } else {
          return lang('Custom');
        } // if
      } // if
    } // getRoleName
    
    /**
     * Return value for a specific project permission
     *
     * @param string $permission
     * @param Project $project
     * @return int
     * @throws InvalidInstanceError
     */
    function getPermission($permission, Project $project) {
      if($project instanceof Project) {
        if($project->isLeader($this->object) || $this->object->isProjectManager()) {
          return ProjectRole::PERMISSION_MANAGE;
        } // if
        
        $project_data = $this->getProjectData($project);
        
        if($project_data[0] instanceof ProjectRole) {
          return $project_data[0]->getPermissionValue($permission, ProjectRole::PERMISSION_NONE);
        } else {
          return isset($project_data[1][$permission]) ? (integer) $project_data[1][$permission] : ProjectRole::PERMISSION_NONE;
        } // if
      } else {
        throw new InvalidInstanceError('project', $project, 'Project');
      } // if
    } // getPermission
    
    /**
     * Clear relations
     */
    function clear() {
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'project_users WHERE user_id = ?', $this->object->getId());
    } // clear
    
  }