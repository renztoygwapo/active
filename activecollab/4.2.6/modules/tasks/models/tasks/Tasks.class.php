<?php

  /**
   * Tasks manager class
   *
   * @package activeCollab.modules.tasks
   * @subpackage models
   */
  class Tasks extends ProjectObjects {
    
    // Sharing context
    const SHARING_CONTEXT = 'request';

    // default orders
    const ORDER_ANY = "ISNULL(completed_on) DESC, position ASC, priority DESC, created_on, integer_field_1";
    const ORDER_OPEN = "ISNULL(position) ASC, position, priority DESC, created_on, integer_field_1";
    const ORDER_COMPLETED = 'ISNULL(position) ASC, position, priority DESC, created_on, integer_field_1';

    /**
     * List of rich text fields
     *
     * @var array
     */
    protected $rich_text_fields = array('body');
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can access tasks section of $project
     *
     * @param IUser $user
     * @param Project $project
     * @param boolean $check_tab
     * @return boolean
     */
    static function canAccess(IUser $user, Project $project, $check_tab = true) {
      return ProjectObjects::canAccess($user, $project, 'task', ($check_tab ? 'tasks' : null));
    } // canAccess
    
    /**
     * Returns true if $user can create a new task in $project
     *
     * @param IUser $user
     * @param Project $project
     * @param boolean $check_tab
     * @return boolean
     */
    static function canAdd(IUser $user, Project $project, $check_tab = true) {
      return ProjectObjects::canAdd($user, $project, 'task', ($check_tab ? 'tasks' : null));
    } // canAdd
    
    /**
     * Returns true if $user can manage tasks in $project
     *
     * @param IUser $user
     * @param Project $project
     * @param boolean $check_tab
     * @return boolean
     */
    static function canManage(IUser $user, Project $project, $check_tab = true) {
      return ProjectObjects::canManage($user, $project, 'task', ($check_tab ? 'tasks' : null));
    } // canManage
    
    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------
    
    /**
     * Return visible and archived tasks in current project that given $user can 
     * access
     * 
     * @param Project $project
     * @param User $user
     * @return Task[]
     */
    static function findByProject(Project $project, User $user) {
      return Tasks::find(array(
        "conditions" => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $project->getId(), 'Task', STATE_ARCHIVED, $user->getMinVisibility()),
        "order" => "priority DESC"
      ));
    } // findByProject

    /**
     * Return visible and archived tasks in current project that given $user can
     * access
     *
     * @param Project $project
     * @param User $user
     * @return Task[]
     */
    static function findActiveByProject(Project $project, User $user) {
      return Tasks::find(array(
        "conditions" => array('project_id = ? AND type = ? AND state = ? AND visibility >= ?', $project->getId(), 'Task', STATE_VISIBLE, $user->getMinVisibility()),
        "order" => "priority DESC"
      ));
    } // findActiveByProject

    /**
     * Return visible and archived tasks in current project that given $user can
     * access
     *
     * @param Project $project
     * @param User $user
     * @return Task[]
     */
    static function findArchivedByProject(Project $project, User $user) {
      return Tasks::find(array(
        "conditions" => array('project_id = ? AND type = ? AND state = ? AND visibility >= ?', $project->getId(), 'Task', STATE_ARCHIVED, $user->getMinVisibility()),
        "order" => "priority DESC"
      ));
    } // findArchivedByProject
    
    /**
     * Count tasks by project
     * 
     * @param Project $project
     * @param Category $category
     * @param integer $min_state
     * @param integer $min_visibility
     * @return number
     */
    static function countByProject(Project $project, $category = null, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      if ($category instanceof TaskCategory) {
        return Tasks::count(array('project_id = ? AND type = ? AND category_id = ? AND state >= ? AND visibility >= ?', $project->getId(), 'Task', $category->getId(), $min_state, $min_visibility));
      } else {
        return Tasks::count(array('project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $project->getId(), 'Task', $min_state, $min_visibility));
      } // if
    } // countByProject
    
    /**
     * Return open tasks by project
     *
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    static function findOpenByProject(Project $project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ? AND completed_on IS NULL', $project->getId(), 'Task', $min_state, $min_visibility),
        'order' => 'ISNULL(position) ASC, position, priority DESC',
      ));
    } // findOpenByProject
    
    /**
     * Return completed tasks by project
     *
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    static function findCompletedByProject(Project $project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return Tasks::find(array(
        'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ? AND completed_on IS NOT NULL', $project->getId(), 'Task', $min_state, $min_visibility),
        'order' => 'completed_on DESC'
      ));
    } // findCompletedByProject
    
    /**
     * Return tasks by a task category
     *
     * @param TaskCategory $category
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    static function findByCategory(TaskCategory $category, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('category_id = ? AND type = ? AND state >= ? AND visibility >= ?', $category->getId(), 'Task', $min_state, $min_visibility),
        'order' => self::ORDER_ANY,
      ));
    } // findByCategory
    
    /**
     * Return number of tasks from a given category
     * 
     * @param TaskCategory $category
     * @param integer $min_state
     * @param integer $min_visibility
     * @return integer
     */
    static function countByCategory(TaskCategory $category, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return Tasks::count(array('category_id = ? AND type IN (?) AND state >= ? AND visibility >= ?', $category->getId(), 'Task', $min_state, $min_visibility));
    } // countByCategory
    
    /**
     * Return open tasks by category
     *
     * @param TaskCategory $category
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    static function findOpenByCategory(TaskCategory $category, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('category_id = ? AND type = ? AND state >= ? AND visibility >= ? AND completed_on IS NULL', $category->getId(), 'Task', $min_state, $min_visibility),
        'order' => self::ORDER_ANY,
      ));
    } // findOpenByCategory
    
    /**
     * Return all tasks by a given milestone
     *
     * @param Milestone $milestone
     * @param integer $min_state
     * @param integer $min_visibility
     * @param integer $limit
     * @param array $exclude
     * @param int $timestamp
     * @return DBResult|Task[]
     */
    static function findByMilestone(Milestone $milestone, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL, $limit = null, $exclude = null, $timestamp = null) {
      $conditions = array('milestone_id = ? AND project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $milestone->getId(), $milestone->getProjectId(), 'Task', $min_state, $min_visibility); // Milestone ID + Project ID (integrity issue from activeCollab 2)
      if ($exclude && $timestamp) {
        $conditions[0] .= ' AND id NOT IN (?) AND created_on < ?';
        $conditions[] = $exclude;
        $conditions[] = date(DATETIME_MYSQL, $timestamp); 
      }
      return Tasks::find(array(
        'conditions' => $conditions,
        'order' => self::ORDER_ANY,
        'limit' => $limit,
      ));
    } // findByMilestone
    
    /**
     * Return number of tasks by milestone
     *
     * @param Milestone $milestone
     * @param integer $min_state
     * @param integer $min_visibility
     * @return int
     */
    static function countByMilestone(Milestone $milestone, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return Tasks::count(array('milestone_id = ? AND project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $milestone->getId(), $milestone->getProjectId(), 'Task', $min_state, $min_visibility)); // Milestone ID + Project ID (integrity issue from activeCollab 2)
    } // countByMilestone
    
    /**
     * Find open tasks by milestone
     *
     * @param Milestone $milestone
     * @param User $user
     * @param integer $min_state
     * @return array
     */
    static function findOpenByMilestone(Milestone $milestone, User $user, $min_state = STATE_VISIBLE) {
      return ProjectObjects::find(array(
        'conditions' => array('milestone_id = ? AND type = ? AND state >= ? AND visibility >= ? AND completed_on IS NULL', $milestone->getId(), 'Task', $min_state, $user->getMinVisibility()),
        'order' => self::ORDER_OPEN,
      ));
    } // findOpenByMilestone

    /**
     * Return active milestone tasks that given $user can access
     *
     * @param Milestone $milestone
     * @param User $user
     * @return array
     */
    static function findActiveByMilestone(Milestone $milestone, User $user) {
      return Tasks::find(array(
        'conditions' => array('milestone_id = ? AND project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $milestone->getId(), $milestone->getProjectId(), 'Task', STATE_VISIBLE, $user->getMinVisibility()),
        'order' => 'priority DESC'
      ));
    } // findActiveByMilestone
    
    /**
     * Find milestone tasks
     * 
     * If $limit_result is defined, than top $limit_result tasks will be 
     * returned (great for keeping the list of completed items short)
     *
     * @param Milestone $milestone
     * @param User $user
     * @param integer $min_state
     * @param integer $limit_result
     * @return array
     */
    static function findCompletedByMilestone(Milestone $milestone, User $user, $min_state = STATE_VISIBLE, $limit_result = null) {
      if($limit_result) {
        $offset = 0;
        $limit = (integer) $limit_result;
      } else {
        $offset = null;
        $limit = null;
      } // if
      
      return Tasks::find(array(
        'conditions' => array('milestone_id = ? AND type = ? AND state >= ? AND visibility >= ? AND completed_on IS NOT NULL', $milestone->getId(), 'Task', $min_state, $user->getMinVisibility()),
        'order' => self::ORDER_COMPLETED,
        'limit' => $limit,
        'offset' => $offset,
      ));
    } // findCompletedByMilestone
    
    /**
     * Return total number of completed tasks in a milestone
     *
     * @param Milestone $milestone
     * @param User $user
     * @param integer $min_state
     * @return integer
     */
    static function countCompletedByMilestone(Milestone $milestone, User $user, $min_state = STATE_VISIBLE) {
      return Tasks::count(array('milestone_id = ? AND type = ? AND state >= ? AND visibility >= ? AND completed_on IS NOT NULL', $milestone->getId(), 'Task', $min_state, $user->getMinVisibility()));
    } // countCompletedByMilestone

    /**
     * Return task by task ID
     *
     * @param Project $project
     * @param integer $id
     * @return Task
     */
    static function findByTaskId(Project $project, $id) {
      return Tasks::find(array(
        'conditions' => array('project_id = ? AND integer_field_1 = ? AND type = ? AND state > ?', $project->getId(), $id, 'Task', STATE_DELETED),
        'one' => true,
      ));
    } // findByTaskId

    /**
     * Return tasks by task ids
     *
     * @param Project $project
     * @param array $ids
     * @return array
     */
    static function findByTaskIds(Project $project, $ids) {
      return Tasks::find(array(
        'conditions' => array('project_id = ? AND integer_field_1 IN (?) AND type = ? AND state > ?', $project->getId(), $ids, 'Task', STATE_DELETED)
      ));
    } // findByTaskIds
    
    /**
     * Return ID for next task
     * 
     * $project can be an instance of Project class or project_id
     *
     * @param Project $project
     * @return integer
     */
    static function findNextTaskIdByProject($project) {
      $project_id = $project instanceof Project ? $project->getId() : (integer) $project;
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      
      $row = DB::executeFirstRow("SELECT MAX(integer_field_1) AS 'max_id' FROM $project_objects_table WHERE project_id = ? AND type = ? AND state > ?", $project_id, 'Task', STATE_DELETED);
      if(is_array($row)) {
        return $row['max_id'] + 1;
      } else {
        return 1;
      } // if
    } // findNextTaskIdByProject
    
    /**
     * Paginate complete tasks by project
     *
     * @param Project $project
     * @param integer $page
     * @param integer $per_page
     * @param integer $min_state
     * @param integer $min_visibility
     * @return null
     */
    static function paginateCompletedByProject(Project $project, $page = 1, $per_page = 10, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return Tasks::paginate(array(
        'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ? AND completed_on IS NOT NULL', $project->getId(), 'Task', $min_state, $min_visibility),
        'order' => self::ORDER_COMPLETED
      ), $page, $per_page);
    } // paginateCompletedByProject
    
    /**
     * Paginate complete tasks by category
     *
     * @param TaskCategory $category
     * @param integer $page
     * @param integer $per_page
     * @param integer $min_state
     * @param integer $min_visibility
     * @return null
     */
    static function paginateCompletedByCategory(TaskCategory $category, $page = 1, $per_page = 10, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return Tasks::paginate(array(
        'conditions' => array('category_id = ? AND type = ? AND state >= ? AND visibility >= ? AND completed_on IS NOT NULL', $category->getId(), 'Task', $min_state, $min_visibility),
        'order' => self::ORDER_COMPLETED
      ), $page, $per_page);
    } // paginateCompletedByCategory
    
    /**
     * Find all tasks in project, and prepare them for objects list
     * 
     * @param Project $project
     * @param User $user
     * @param int $state
     * @return array
     */
    static function findForObjectsList(Project $project, User $user, $state = STATE_VISIBLE) {
      $result = array();

      $custom_fields = $escaped_custom_field_names = array();

      foreach(CustomFields::getEnabledCustomFieldsByType('Task') as $field_name => $details) {
        $custom_fields[] = $field_name;
        $escaped_custom_field_names[] = DB::escapeFieldName($field_name);
      } // if

      if(count($escaped_custom_field_names)) {
        $escaped_custom_field_names = ', ' . implode(', ', $escaped_custom_field_names);
      } else {
        $escaped_custom_field_names = '';
      } // if

      $tasks = DB::execute("SELECT id, name, category_id, milestone_id, completed_on, integer_field_1 as task_id, label_id, assignee_id, priority, delegated_by_id, state, visibility $escaped_custom_field_names FROM " . TABLE_PREFIX . "project_objects WHERE type = 'Task' AND project_id = ? AND state = ? AND visibility >= ? ORDER BY " . self::ORDER_ANY, $project->getId(), $state, $user->getMinVisibility());
      if ($tasks) {
        $task_url = Router::assemble('project_task', array('project_slug' => $project->getSlug(), 'task_id' => '--TASKID--'));
        $project_id = $project->getId();

        $labels = Labels::getIdDetailsMap('AssignmentLabel');
        
        foreach ($tasks as $task) {
          list($total_subtasks, $open_subtasks) = ProjectProgress::getObjectProgress(array(
            'project_id' => $project_id, 
            'object_type' => 'Task', 
            'object_id' => $task['id'], 
          ));

          $result[] = array(
            'id'                => $task['id'],
            'name'              => $task['name'],
            'project_id'        => $project_id,
            'category_id'       => $task['category_id'],
            'milestone_id'      => $task['milestone_id'],
            'task_id'           => $task['task_id'],
            'is_completed'      => $task['completed_on'] ? 1 : 0,
            'permalink'         => str_replace('--TASKID--', $task['task_id'], $task_url),
            'label_id'          => $task['label_id'],
            'label'             => $task['label_id'] ? $labels[$task['label_id']] : null,
            'assignee_id'       => $task['assignee_id'],
            'priority'          => $task['priority'],
            'delegated_by_id'   => $task['delegated_by_id'],
            'total_subtasks'    => $total_subtasks,
            'open_subtasks'     => $open_subtasks,
            'estimated_time'    => 0,
            'tracked_time'      => 0,
            'is_favorite'       => Favorites::isFavorite(array('Task', $task['id']), $user),
            'is_archived'       => $task['state'] == STATE_ARCHIVED ? 1 : 0,
            'visibility'        => $task['visibility'],
          );

          if(count($custom_fields)) {
            $last_record = count($result) - 1;

            foreach($custom_fields as $custom_field) {
              $result[$last_record][$custom_field] = $task[$custom_field] ? $task[$custom_field] : null;
            } // foreach
          } // if
        } // foreach
      } // if
      
      return $result;
    } // findForObjectsList

    /**
     * Find all tasks in project and prepare them for export
     *
     * @param Project $project
     * @param User $user
     * @param array $parents_map
     * @param int $changes_since
     * @return array
     */
    static function findForExport(Project $project, User $user, &$parents_map, $changes_since) {
      $result = array();

      if(Tasks::canAccess($user, $project)) {
        $project_objects_table = TABLE_PREFIX . 'project_objects';

        $additional_condition = '';
        if(!is_null($changes_since)) {
          $changes_since_date = DateTimeValue::makeFromTimestamp($changes_since);
          $additional_condition = "AND (created_on > '$changes_since_date' OR updated_on > '$changes_since_date')";
        } // if

        $tasks = DB::execute("SELECT id, type, name, body, body AS 'body_formatted', project_id, milestone_id, category_id, label_id, assignee_id, delegated_by_id, state, visibility, priority, created_by_id, created_on, due_on, updated_by_id, updated_on, completed_by_id, completed_on, is_locked, integer_field_1 AS task_id, date_field_1, version FROM $project_objects_table WHERE type = ? AND project_id = ? AND state >= ? AND visibility >= ? $additional_condition ORDER BY " . self::ORDER_ANY, 'Task', $project->getId(), STATE_ARCHIVED, $user->getMinVisibility());

        if($tasks instanceof DBResult) {
          $tasks->setCasting(array(
            'id' => DBResult::CAST_INT,
            'body_formatted' => function($in) {
              return HTML::toRichText($in);
            },
            'task_id' => DBResult::CAST_INT,
            'project_id' => DBResult::CAST_INT,
            'milestone_id' => DBResult::CAST_INT,
            'category_id' => DBResult::CAST_INT,
            'label_id' => DBResult::CAST_INT,
            'assignee_id' => DBResult::CAST_INT,
            'delegated_by_id' => DBResult::CAST_INT,
            'created_by_id' => DBResult::CAST_INT,
            'updated_by_id' => DBResult::CAST_INT,
            'completed_by_id' => DBResult::CAST_INT
          ));

          $task_url = Router::assemble('project_task', array('project_slug' => $project->getSlug(), 'task_id' => '--TASKID--'));

          foreach($tasks as $task) {
            $users_table = TABLE_PREFIX . 'users';
            $assignments_table = TABLE_PREFIX . 'assignments';

            $other_assignee_ids = DB::executeFirstColumn("SELECT $users_table.id FROM $users_table, $assignments_table WHERE $users_table.id = $assignments_table.user_id AND $assignments_table.parent_type = ? AND $assignments_table.parent_id = ? AND $users_table.state >= ?", 'Task', $task['id'], STATE_ARCHIVED);

            $result[] = array(
              'id'              => $task['id'],
              'type'            => $task['type'],
              'name'            => $task['name'],
              'body'            => $task['body'],
              'body_formatted'  => $task['body_formatted'],
              'task_id'         => $task['task_id'],
              'project_id'      => $task['project_id'],
              'milestone_id'    => $task['milestone_id'],
              'category_id'     => $task['category_id'],
              'label_id'        => $task['label_id'],
              'assignee_id'     => $task['assignee_id'],
              'other_assignees' => $other_assignee_ids,
              'delegated_by_id' => $task['delegated_by_id'],
              'state'           => $task['state'],
              'visibility'      => $task['visibility'],
              'priority'        => $task['priority'],
              'created_by_id'   => $task['created_by_id'],
              'created_on'      => $task['created_on'],
              'due_on'          => $task['due_on'],
              'updated_by_id'   => $task['updated_by_id'],
              'updated_on'      => $task['updated_on'],
              'completed_by_id' => $task['completed_by_id'],
              'completed_on'    => $task['completed_on'],
              'start_on'        => $task['date_field_1'],
              'is_completed'    => $task['completed_on'] === null ? '0' : '1',
              'is_locked'       => $task['is_locked'],
              'permalink'       => str_replace('--TASKID--', $task['id'], $task_url),
              'version'         => $task['version']
            );

            $parents_map[$task['type']][] = $task['id'];
          } // foreach
        } // if
      } // if

      return $result;
    } // findForExport

    /**
     * Export to file by project
     *
     * @param Project $project
     * @param User $user
     * @param string $output_file
     * @param array $parents_map
     * @param int $changes_since
     * @return integer
     * @throws Error
     */
    static function exportToFileByProject(Project $project, User $user, $output_file, &$parents_map, $changes_since) {
      if (!($output_handle = fopen($output_file, 'w+'))) {
        throw new Error(lang('Failed to write JSON file to :file_path', array('file_path' => $output_file)));
      } // if

      // open json array
      fwrite($output_handle, '[');

      $count = 0;
      if(Tasks::canAccess($user, $project)) {
        $project_objects_table = TABLE_PREFIX . 'project_objects';

        $additional_condition = '';
        if(!is_null($changes_since)) {
          $changes_since_date = DateTimeValue::makeFromTimestamp($changes_since);
          $additional_condition = "AND (created_on > '$changes_since_date' OR updated_on > '$changes_since_date')";
        } // if

        $tasks = DB::execute("SELECT id, type, name, body, body AS 'body_formatted', project_id, milestone_id, category_id, label_id, assignee_id, delegated_by_id, state, visibility, priority, created_by_id, created_on, due_on, updated_by_id, updated_on, completed_by_id, completed_on, is_locked, integer_field_1 AS task_id, date_field_1, version FROM $project_objects_table WHERE type = ? AND project_id = ? AND state >= ? AND visibility >= ? $additional_condition ORDER BY " . self::ORDER_ANY, 'Task', $project->getId(), (boolean) $additional_condition ? STATE_TRASHED : STATE_ARCHIVED, $user->getMinVisibility());

        if($tasks instanceof DBResult) {
          $tasks->setCasting(array(
            'id' => DBResult::CAST_INT,
            'body_formatted' => function($in) {
              return HTML::toRichText($in);
            },
            'task_id' => DBResult::CAST_INT,
            'project_id' => DBResult::CAST_INT,
            'milestone_id' => DBResult::CAST_INT,
            'category_id' => DBResult::CAST_INT,
            'label_id' => DBResult::CAST_INT,
            'assignee_id' => DBResult::CAST_INT,
            'delegated_by_id' => DBResult::CAST_INT,
            'created_by_id' => DBResult::CAST_INT,
            'updated_by_id' => DBResult::CAST_INT,
            'completed_by_id' => DBResult::CAST_INT
          ));

          $task_url = Router::assemble('project_task', array('project_slug' => $project->getSlug(), 'task_id' => '--TASKID--'));

          $buffer = '';
          foreach($tasks as $task) {
            $users_table = TABLE_PREFIX . 'users';
            $assignments_table = TABLE_PREFIX . 'assignments';

            $other_assignee_ids = DB::executeFirstColumn("SELECT $users_table.id FROM $users_table, $assignments_table WHERE $users_table.id = $assignments_table.user_id AND $assignments_table.parent_type = ? AND $assignments_table.parent_id = ? AND $users_table.state >= ?", 'Task', $task['id'], STATE_ARCHIVED);

            if($count > 0) $buffer .= ',';

            $buffer .= JSON::encode(array(
              'id'              => $task['id'],
              'type'            => $task['type'],
              'name'            => $task['name'],
              'body'            => $task['body'],
              'body_formatted'  => $task['body_formatted'],
              'task_id'         => $task['task_id'],
              'project_id'      => $task['project_id'],
              'milestone_id'    => $task['milestone_id'],
              'category_id'     => $task['category_id'],
              'label_id'        => $task['label_id'],
              'assignee_id'     => $task['assignee_id'],
              'other_assignees' => $other_assignee_ids,
              'delegated_by_id' => $task['delegated_by_id'],
              'state'           => $task['state'],
              'visibility'      => $task['visibility'],
              'priority'        => $task['priority'],
              'created_by_id'   => $task['created_by_id'],
              'created_on'      => $task['created_on'],
              'due_on'          => $task['due_on'],
              'updated_by_id'   => $task['updated_by_id'],
              'updated_on'      => $task['updated_on'],
              'completed_by_id' => $task['completed_by_id'],
              'completed_on'    => $task['completed_on'],
              'start_on'        => $task['date_field_1'],
              'is_completed'    => $task['completed_on'] === null ? '0' : '1',
              'is_locked'       => $task['is_locked'],
              'permalink'       => str_replace('--TASKID--', $task['id'], $task_url),
              'version'         => $task['version'],
              'is_favorite'     => Favorites::isFavorite(array('Task', $task['id']), $user),
              'total_subtasks'  => first(Subtasks::countByParent(array('Task', $task['id']))),
            ));

            if($count % 15 == 0 && $count > 0) {
              fwrite($output_handle, $buffer);
              $buffer = '';
            } // if

            $parents_map[$task['type']][] = $task['id'];
            $count++;
          } // foreach

          if($buffer) {
            fwrite($output_handle, $buffer);
          } // if
        } // if
      } // if

      // close json array
      fwrite($output_handle, ']');

      // close the handle and set correct permissions
      fclose($output_handle);
      @chmod($output_file, 0777);

      return $count;
    } // exportToFileByProject

    /**
     * Find tasks for outline
     *
     * @param Project $project
     * @param integer $milestone_id
     * @param User $user
     * @param int $state
     * @return array
     */
    static function findForOutline(Project $project, $milestone_id, User $user, $state = STATE_VISIBLE) {
      if ($milestone_id) {
        $task_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'project_objects WHERE project_id = ? AND type = ? AND state >= ? AND visibility >= ? AND completed_on IS NULL AND milestone_id = ?', $project->getId(), 'Task', $state, $user->getMinVisibility(), $milestone_id);
      } else {
        $task_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'project_objects WHERE project_id = ? AND type = ? AND state >= ? AND visibility >= ? AND completed_on IS NULL AND ((milestone_id IS NULL) || (milestone_id < 1))', $project->getId(), 'Task', $state, $user->getMinVisibility());
      } // if

      if (!is_foreachable($task_ids)) {
        return false;
      } // if

      $tasks = DB::execute('SELECT id, integer_field_1 AS task_id, name, body, due_on, date_field_1 AS start_on, assignee_id, priority, visibility, created_by_id, label_id, milestone_id, category_id FROM ' . TABLE_PREFIX . 'project_objects WHERE ID IN(?) ORDER BY ' . self::ORDER_OPEN, $task_ids);

      // casting
      $tasks->setCasting(array(
        'due_on'        => DBResult::CAST_DATE,
        'start_on'      => DBResult::CAST_DATE
      ));

      $tasks_id_prefix_pattern = '--TASK-ID--';
      $task_url_params = array('project_slug' => $project->getSlug(), 'task_id' => $tasks_id_prefix_pattern);
      $view_task_url_pattern = Router::assemble('project_task', $task_url_params);
      $edit_task_url_pattern = Router::assemble('project_task_edit', $task_url_params);
      $trash_task_url_pattern = Router::assemble('project_task_trash', $task_url_params);
      $subscribe_task_url_pattern = Router::assemble('project_task_subscribe', $task_url_params);
      $unsubscribe_task_url_pattern = Router::assemble('project_task_unsubscribe', $task_url_params);
      $reschedule_task_url_pattern = Router::assemble('project_task_reschedule', $task_url_params);
      $tracking_task_url_pattern = AngieApplication::isModuleLoaded('tracking') ? Router::assemble('project_task_tracking', $task_url_params) : '';
      $complete_task_url_pattern = Router::assemble('project_task_complete', $task_url_params);

      // can_manage_tasks
      $can_manage_tasks = ($user->projects()->getPermission('task', $project) >= ProjectRole::PERMISSION_MANAGE);

      // all assignees
      $user_assignments_on_tasks = DB::executeFirstColumn('SELECT parent_id FROM ' . TABLE_PREFIX . 'assignments WHERE parent_id IN (?) AND parent_type = ? AND user_id = ?', $task_ids, 'Task', $user->getId());

      // all subscriptions
      $user_subscriptions_on_tasks = DB::executeFirstColumn('SELECT parent_id FROM ' . TABLE_PREFIX . 'subscriptions WHERE parent_id IN (?) AND parent_type = ? AND user_id = ?', $task_ids, 'Task', $user->getId());

      // other assignees
      $other_assignees = array();
      $raw_other_assignees = DB::execute('SELECT user_id, parent_id FROM ' . TABLE_PREFIX . 'assignments WHERE parent_type = ? AND parent_id IN (?)', 'Task', $task_ids);
      foreach ($raw_other_assignees as $raw_assignee) {
        if (!is_array($other_assignees[$raw_assignee['parent_id']])) {
          $other_assignees[$raw_assignee['parent_id']] = array();
        } // if
        $other_assignees[$raw_assignee['parent_id']][] = array('id' => $raw_assignee['user_id']);
      } // foreach

      // expenses & time
      $expenses = array();
      $time = array();
      $estimates = array();
      if (AngieApplication::isModuleLoaded('tracking')) {
        $raw_expenses = DB::execute('SELECT parent_id, SUM(value) as expense FROM ' . TABLE_PREFIX . 'expenses WHERE parent_id IN (?) AND parent_type = ? GROUP BY parent_id', $task_ids, 'Task');
        if (is_foreachable($raw_expenses)) {
          foreach ($raw_expenses as $raw_expense) {
            $expenses[$raw_expense['parent_id']] = $raw_expense['expense'];
          } // if
        } // if

        $raw_time = DB::execute('SELECT parent_id, SUM(value) as time FROM ' . TABLE_PREFIX . 'time_records WHERE parent_id IN (?) AND parent_type = ? GROUP BY parent_id', $task_ids, 'Task');
        if (is_foreachable($raw_time)) {
          foreach ($raw_time as $raw_single_time) {
            $time[$raw_single_time['parent_id']] = $raw_single_time['time'];
          } // foreach
        } // if

        $raw_estimates = DB::execute('SELECT parent_id, value, job_type_id FROM (SELECT * FROM ' . TABLE_PREFIX . 'estimates WHERE parent_id IN (?) AND parent_type = ? ORDER BY created_on DESC) as estimates_inverted GROUP BY parent_id', $task_ids , 'Task');
        if (is_foreachable($raw_estimates)) {
          foreach ($raw_estimates as $raw_estimate) {
            $estimates[$raw_estimate['parent_id']] = array(
              'value' => $raw_estimate['value'],
              'job_type_id' => $raw_estimate['job_type_id'],
            );
          } // foreach
        } // if
      } // if

      $results = array();
      foreach ($tasks as $subobject) {
        $task_id = $subobject['id'];
        $task_task_id = $subobject['task_id'];

        $results[] = array(
          'id'                  => $task_id,
          'task_id'             => $task_task_id,
          'name'                => $subobject['name'],
          'body'                => $subobject['body'],
          'priority'            => $subobject['priority'],
          'visibility'          => $subobject['visibility'],
          'milestone_id'        => !empty($subobject['milestone_id']) ? $subobject['milestone_id'] : null,
          'category_id'          => !empty($subobject['category_id']) ? $subobject['category_id'] : null,
          'class'               => 'Task',
          'start_on'            => $subobject['start_on'],
          'due_on'              => $subobject['due_on'],
          'assignee_id'         => $subobject['assignee_id'],
          'other_assignees'     => isset($other_assignees[$task_id]) ? $other_assignees[$task_id] : null,
          'label_id'            => !empty($subobject['label_id']) ? $subobject['label_id'] : null,
          'user_is_subscribed'  => in_array($task_id, $user_subscriptions_on_tasks),
          'object_time'         => !empty($time[$task_id]) ? $time[$task_id] : 0,
          'object_expenses'     => !empty($expenses[$task_id]) ? $expenses[$task_id] : 0,
          'estimate'            => !empty($estimates[$task_id]) ? $estimates[$task_id] : null,
          'event_names'         => array(
            'updated'             => 'task_updated'
          ),
          'urls'                => array(
            'view'                => str_replace($tasks_id_prefix_pattern, $task_task_id, $view_task_url_pattern),
            'edit'                => str_replace($tasks_id_prefix_pattern, $task_task_id, $edit_task_url_pattern),
            'trash'               => str_replace($tasks_id_prefix_pattern, $task_task_id, $trash_task_url_pattern),
            'subscribe'           => str_replace($tasks_id_prefix_pattern, $task_task_id, $subscribe_task_url_pattern),
            'unsubscribe'         => str_replace($tasks_id_prefix_pattern, $task_task_id, $unsubscribe_task_url_pattern),
            'reschedule'          => str_replace($tasks_id_prefix_pattern, $task_task_id, $reschedule_task_url_pattern),
            'tracking'            => $tracking_task_url_pattern ? str_replace($tasks_id_prefix_pattern, $task_task_id, $tracking_task_url_pattern) : '',
            'complete'            => str_replace($tasks_id_prefix_pattern, $task_task_id, $complete_task_url_pattern),
          ),
          'permissions'         => array(
            'can_edit'            => can_edit_project_object($subobject, $user, $project, $can_manage_tasks, $user_assignments_on_tasks),
            'can_trash'           => can_trash_project_object($subobject, $user, $project, $can_manage_tasks, $user_assignments_on_tasks),
          )
        );
      } // foreach

      return $results;
    } // findForOutline
    
    /**
     * Find tasks for printing by grouping and filtering criteria
     * 
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @param string $group_by
     * @param array $filter_by
     * @return DBResult
     */
    static function findForPrint(Project $project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL, $group_by = null, $filter_by = null) {   
      // initial condition
      $conditions = array(
        DB::prepare('(project_id = ? AND type = ? AND state = ? AND visibility >= ?)', $project->getId(), 'Task', $min_state, $min_visibility),
      );
      
      if (!in_array($group_by, array('milestone_id', 'category_id', 'label_id', 'assignee_id','delegated_by_id','priority'))) {
        $group_by = null;
      } // if
      
      if($group_by == 'priority') {
        $group_by .= ' DESC';
      }//if
                
      // filter by completion status
      $filter_is_completed = array_var($filter_by, 'is_completed', null);
      if ($filter_is_completed === '0') {
        $conditions[] = '(completed_on IS NULL)';
        $order_by = TASKS::ORDER_OPEN;
      } else if ($filter_is_completed === '1') {
        $conditions[] = '(completed_on IS NOT NULL)';
        $order_by = TASKS::ORDER_COMPLETED;
      } else {
        $order_by = TASKS::ORDER_ANY;
      } // if

      
      // do find tasks
      $tasks = Tasks::find(array(
        'conditions' => implode(' AND ', $conditions),
        'order' => ($group_by ? $group_by . ', ' : '') . $order_by
      ));
      
      return $tasks;
    } // findForPrint

	  /**
	   * Find tasks for calendar by user
	   *
	   * @param User $user
	   * @param null $from
	   * @param null $to
	   * @param bool $assigned
	   * @param bool $all_for_admins_and_pms
	   * @param bool $include_completed_and_archived
	   * @return array|bool
	   */
	  static function findForCalendarByUser(User $user, $from = null, $to = null, $assigned = false, $all_for_admins_and_pms = false, $include_completed_and_archived = false) {
		  $result = array();

		  // prepare tables
		  $project_objects_table = TABLE_PREFIX . "project_objects";
		  $assignments_table = TABLE_PREFIX . "assignments";

		  // initialize conditions
		  $conditions = array();
		  $conditions[] = DB::prepare('type = ? AND visibility >= ?', 'Task', $user->getMinVisibility());
		  $conditions[] = DB::prepare('due_on IS NOT NULL');

		  // add completed and archived condition
		  if ($include_completed_and_archived) {
			  $conditions[] = DB::prepare('state >= ?', STATE_ARCHIVED);
		  } else {
			  $conditions[] = DB::prepare('completed_on IS NULL AND state = ?', STATE_VISIBLE);
		  } // if

		  // add date and time condition
		  if ($from instanceof DateValue && $to instanceof DateValue) {
			  $conditions[] = DB::prepare('(due_on BETWEEN ? AND ?)', $from->toMySQL(), $to->toMySQL());
		  } // if

		  // add assignee condition
		  if ($assigned) {
			  $user_assigned_task_ids = DB::executeFirstColumn("SELECT parent_id FROM $assignments_table WHERE parent_type = ? AND user_id = ?", "Task", $user->getId());
			  if ($user_assigned_task_ids) {
				  $conditions[] = DB::prepare('(id IN (?) OR assignee_id = ?)', $user_assigned_task_ids, $user->getId());
			  } else {
				  $conditions[] = DB::prepare('assignee_id = ?', $user->getId());
			  } // if
		  } // if

		  // add all for admins and project managers condition
		  if (!$all_for_admins_and_pms) {
			  $additional_conditions = DB::prepare('state >= ?', STATE_VISIBLE);

			  $projects = Projects::findByUser($user, true, $additional_conditions);

			  $project_ids = array();
			  if (is_foreachable($projects)) {
				  foreach ($projects as $project) {
					  if ($user->projects()->getPermission('task', $project) >= ProjectRole::PERMISSION_ACCESS) {
						  array_push($project_ids, $project->getId());
					  } // if
				  } // foreach
			  } // if

			  if (!$project_ids) {
				  return false;
			  } // if

			  $conditions[] = DB::prepare('project_id IN (?)', $project_ids);
		  } // if

		  // return false if there is no conditions
		  if (!$conditions) {
			  return false;
		  } // if

		  // find all tasks by given condition
		  $conditions = implode(" AND ", $conditions);

		  // found all task ids
		  $task_ids = DB::executeFirstColumn("SELECT id FROM $project_objects_table WHERE $conditions");

		  // return false if there is no task ids found
		  if (!$task_ids) {
			  return false;
		  } // if

		  // found all task by ids
 		  $tasks = DB::execute("SELECT id, name, project_id, due_on, integer_field_1 as task_id, completed_on, state FROM $project_objects_table WHERE id IN (?)", $task_ids);

		  if (is_foreachable($tasks)) {
			  // casting
			  $tasks->setCasting(array(
				  'due_on' => DBResult::CAST_DATE
			  ));

			  // all assignees
			  $user_assignments_on_tasks = DB::executeFirstColumn('SELECT parent_id FROM ' . TABLE_PREFIX . 'assignments WHERE parent_id IN (?) AND parent_type = ? AND user_id = ?', $task_ids, 'Task', $user->getId());

			  $tasks_id_prefix_pattern = '--TASK-ID--';
			  $project_slug_prefix_pattern = '--PROJECT-SLUG--';
			  $task_url_params = array('project_slug' => $project_slug_prefix_pattern, 'task_id' => $tasks_id_prefix_pattern);
			  $view_task_url_pattern = Router::assemble('project_task', $task_url_params);
			  $edit_task_url_pattern = Router::assemble('project_task_edit', $task_url_params);
			  $reschedule_task_url_pattern = Router::assemble('project_task_reschedule', $task_url_params);

			  foreach ($tasks as $subobject) {
				  $id = $subobject['id'];
          $task_id = $subobject['task_id'];
          $project_id = $subobject['project_id'];
          $state = $subobject['state'];
          $completed_on = $subobject['completed_on'];
          $due_on = $subobject['due_on'];

				  $project = DataObjectPool::get('Project', $project_id);

				  // can_manage_tasks
				  $can_manage_tasks = ($user->projects()->getPermission('task', $project) >= ProjectRole::PERMISSION_MANAGE);

				  $result[] = array(
					  'id'            => $id,
            'type'          => 'Task',
            'parent_id'     => $project_id,
            'parent_type'   => 'Project',
            'name'          => $subobject['name'],
            'ends_on'       => $due_on,
            'starts_on'     => $due_on,
					  'permissions'   => array(
						  'can_edit'        => can_edit_project_object($subobject, $user, $project, $can_manage_tasks, $user_assignments_on_tasks),
						  'can_trash'       => false,
						  'can_reschedule'  => ($user->projects()->getPermission('task', $project) >= ProjectRole::PERMISSION_MANAGE && !$completed_on && $state == STATE_VISIBLE)
					  ),
					  'urls'          => array(
						  'view'          => str_replace($tasks_id_prefix_pattern, $task_id, str_replace($project_slug_prefix_pattern, $project->getSlug(), $view_task_url_pattern)),
						  'edit'          => str_replace($tasks_id_prefix_pattern, $task_id, str_replace($project_slug_prefix_pattern, $project->getSlug(), $edit_task_url_pattern)),
						  'reschedule'    => str_replace($tasks_id_prefix_pattern, $task_id, str_replace($project_slug_prefix_pattern, $project->getSlug(), $reschedule_task_url_pattern))
					  ),
					  'completed'     => $completed_on != null,
					  'archived'      => $state == STATE_ARCHIVED
				  );
			  } // foreach
		  } // if

		  return $result;
	  } // if
    
    /**
     * Get all items from result and describes array for paged list 
     * 
     * @param DBResult $result
     * @param Project $active_project
     * @param User $logged_user
     * @param int $items_limit
     * @return Array
     */
    static function getDescribedTaskArray(DBResult $result, Project $active_project, User $logged_user, $items_limit = null) {
      $return_value = array();
      if ($result instanceof DBResult) {
        $assignment_labels = Labels::getIdDetailsMap('AssignmentLabel');
        
        $user_ids = array();
        foreach($result as $row) {
          if ($row['created_by_id'] && !in_array($row['created_by_id'], $user_ids)) {
            $user_ids[] = $row['created_by_id'];
          } //if
        } //if

        $users_array = count($user_ids) ? Users::findByIds($user_ids)->toArrayIndexedBy('getId') : array();

        foreach($result as $row) {
          $task = array();
          // Task Details
          $task['id'] = $row['id'];
          $task['name'] = clean($row['name']);
          $task['is_favorite'] = Favorites::isFavorite(array('Task', $task['id']), $logged_user);
          $task['is_completed'] = (datetimeval($row['completed_on']) instanceof DateTimeValue) ? 1 : 0;
          $task['priority'] = $row['priority'];
          $task['label'] = $assignment_labels[$row['label_id']];
          
          // Favorite
          $favorite_params = $logged_user->getRoutingContextParams();
          $favorite_params['object_type'] = $row['type'];
          $favorite_params['object_id'] = $row['id'];
          
          // Urls
          $task['urls']['remove_from_favorites'] = Router::assemble($logged_user->getRoutingContext() . '_remove_from_favorites', $favorite_params);
          $task['urls']['add_to_favorites'] = Router::assemble($logged_user->getRoutingContext() . '_add_to_favorites', $favorite_params);
          $task['urls']['view'] = Router::assemble('project_task', array('project_slug' => $active_project->getSlug(), 'task_id' => $row['integer_field_1']));
          $task['urls']['edit'] = Router::assemble('project_task_edit', array('project_slug' => $active_project->getSlug(), 'task_id' => $row['integer_field_1']));
          $task['urls']['trash'] = Router::assemble('project_task_trash', array('project_slug' => $active_project->getSlug(), 'task_id' => $row['integer_field_1']));
          
          // CRUD

          $task['permissions']['can_edit'] = Tasks::canManage($logged_user, $active_project);
          $task['permissions']['can_trash'] = Tasks::canManage($logged_user, $active_project);

          // User & datetime details
          $task['created_on'] = datetimeval($row['created_on']);
          
          if($row['created_by_id'] && isset($users_array[$row['created_by_id']])) {
            $task['created_by'] = $users_array[$row['created_by_id']];
          } elseif($row['created_by_email']) {
            $task['created_by'] = new AnonymousUser($row['created_by_name'], $row['created_by_email']);
          } else {
            $task['created_by'] = null;
          } // if
          $return_value[] = $task;
          
          if (count($return_value) === $items_limit) {
            break;
          } //if
        } // foreach
      } //if
      
      return $return_value;
    } // getDescribedTaskArray

    // ---------------------------------------------------
    //  Utilities
    // ---------------------------------------------------

    /**
     * Returns ID name map that given $user can access
     *
     * @param IUser $user
     * @return array
     */
    static function getIdNameMapByUser(IUser $user) {
      $projects_table = TABLE_PREFIX . 'projects';
      $project_objects_table = TABLE_PREFIX . 'project_objects';

      $rows = DB::execute("SELECT $projects_table.id AS project_id, $project_objects_table.integer_field_1 AS task_id, $project_objects_table.name AS task_name FROM $projects_table, $project_objects_table WHERE $projects_table.id = $project_objects_table.project_id AND $project_objects_table.project_id IN (?) AND $project_objects_table.type = ? AND $project_objects_table.state = ? AND $project_objects_table.visibility >= ?", Projects::findIdsByUser($user), 'Task', STATE_VISIBLE, $user->getMinVisibility());

      if($rows) {
        $result = array();
        foreach($rows as $row) {
          $result[(integer) $row['project_id']][(integer) $row['task_id']] = $row['task_name'];
        } // foreach

        return $result;
      } else {
        return null;
      } // if
    } // getIdNameMapByUser

    /**
     * Return my tasks filter instance
     *
     * @param User $user
     * @return AssignmentFilter
     */
    static function getMyTasksFilter(User $user) {
      $filter = new AssignmentFilter();

      $filter->setCompletedOnFilter(AssignmentFilter::DATE_FILTER_IS_NOT_SET);
      $filter->setProjectFilter(Projects::PROJECT_FILTER_ACTIVE);
      $filter->filterByUsers(array($user->getId()), false);
      $filter->setIncludeSubtasks(true);
      $filter->setIncludeTrackingData(true);
      $filter->setIncludeOtherAssignees(true);
      $filter->setGroupBy(AssignmentFilter::GROUP_BY_PROJECT);
      $filter->setFavoriteOnTopWhenGroupingByProject(true);

      $labels_filter = ConfigOptions::getValueFor('my_tasks_labels_filter', $user);

      if($labels_filter == AssignmentFilter::LABEL_FILTER_SELECTED || $labels_filter == AssignmentFilter::LABEL_FILTER_NOT_SELECTED) {
        $filter->filterByLabelNames(ConfigOptions::getValueFor('my_tasks_labels_filter_data', $user), $labels_filter == AssignmentFilter::LABEL_FILTER_NOT_SELECTED);
      } // if

      if(AngieApplication::isModuleLoaded('tracking')) {
        $filter->setIncludeTrackingData(true);
      } // if

      return $filter;
    } // getMyTasksFilter

    /**
     * Return my late tasks assignment filter
     *
     * @param User $user
     * @return AssignmentFilter
     */
    static function getMyLateTasksFilter(User $user) {
      $filter = Tasks::getMyTasksFilter($user);

      $filter->setDueOnFilter(DataFilter::DATE_FILTER_LATE_OR_TODAY);
      $filter->setGroupBy(AssignmentFilter::DONT_GROUP);

      return $filter;
    } // getMyLateTasksFilter

    // ---------------------------------------------------
    //  Clean-up
    // ---------------------------------------------------

    /**
     * Return number of tasks that are good for archiving
     *
     * If $project is set, tasks will be counted just for that project. If it's missing, all active projects will be
     * used for count
     *
     * @param IUser $user
     * @param Project $project
     * @return integer
     */
    static function countForCleanUp(IUser $user, $project = null) {
      if($project instanceof Project) {
        $project_ids = array($project->getId());
      } else {
        $project_ids = Projects::findIdsByUser($user, true, array('completed_on IS NULL AND state = ?', STATE_VISIBLE));
      } // if

      if(count($project_ids)) {
        return (integer) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'project_objects WHERE project_id IN (?) AND type = ? AND state = ? AND completed_on <= ?', $project_ids, 'Task', STATE_VISIBLE, DateValue::makeFromString('-30 days'));
      } else {
        return 0;
      } // if
    } // countForCleanUp

    /**
     * Find tasks that are ready for clean-up
     *
     * @param IUser $user
     * @param Project $project
     * @return DBResult
     */
    static function findForCleanUp(IUser $user, $project = null) {
      if($project instanceof Project) {
        $project_ids = array($project->getId());
      } else {
        $project_ids = Projects::findIdsByUser($user, true, array('completed_on IS NULL AND state = ?', STATE_VISIBLE));
      } // if

      if(count($project_ids)) {
        return Tasks::findBySql('SELECT * FROM ' . TABLE_PREFIX . 'project_objects WHERE project_id IN (?) AND type = ? AND state = ? AND completed_on <= ? ORDER BY completed_on LIMIT 0, 100', $project_ids, 'Task', STATE_VISIBLE, DateValue::makeFromString('-30 days'));
      } else {
        return null;
      } // if
    } // findForCleanUp

    // ---------------------------------------------------
    //  Duplicate task ID resolving
    // ---------------------------------------------------

    /**
     * This method finds duplicate task IDs and gives them new numbers
     *
     * @throws DBError
     * @return bool
     */
    static function resolveDuplicateTaskIds() {
      $projects = Projects::find();

      if (is_foreachable($projects)) {
        foreach ($projects as $project) {
          /**
           * @var Project $project
           */
          $tasks = self::find(array(
            'conditions' => array('project_id = ? AND type = ?', $project->getId(), 'Task'),
          ));

          if (is_foreachable($tasks)) {
            $temp_array = array();
            foreach ($tasks as $task) {
              /**
               * @var Task $task
               */
              if (in_array($task->getTaskId(), $temp_array)) {
                try {
                  DB::beginWork('Saving task ID changes @ ' . __CLASS__);

                  $task->setTaskId(Tasks::findNextTaskIdByProject($project));
                  $task->save();

                  DB::commit('Task ID changes saved @ ' . __CLASS__);
                } catch(Exception $e) {
                  DB::rollback('Failed to save task ID changes @ ' . __CLASS__);
                  throw new DBError($e->getCode(), $e->getMessage());
                } // try
              } // if

              $temp_array[] = $task->getTaskId();
            } //foreach
          } //if
        } //foreach
      } // if

      return true;
    } //resolveDuplicateTaskIds
    
  }