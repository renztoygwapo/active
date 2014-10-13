<?php

  /**
   * AssignmentFilter class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class AssignmentFilter extends DataFilter {

    // User filter
    const USER_FILTER_ANYBODY = 'anybody';
    const USER_FILTER_ANONYMOUS = 'anonymous';
    const USER_FILTER_NOT_ASSIGNED = 'not_assigned';
    const USER_FILTER_LOGGED_USER = 'logged_user';
    const USER_FILTER_LOGGED_USER_RESPONSIBLE = 'logged_user_responsible';
    const USER_FILTER_COMPANY = 'company';
    const USER_FILTER_COMPANY_RESPONSIBLE = 'company_responsible';
    const USER_FILTER_SELECTED = 'selected';
    const USER_FILTER_SELECTED_RESPONSIBLE = 'selected_responsible';

    // Label filter
    const LABEL_FILTER_ANY = 'any';
    const LABEL_FILTER_IS_NOT_SET = 'is_not_set';
    const LABEL_FILTER_SELECTED = 'selected';
    const LABEL_FILTER_NOT_SELECTED = 'not_selected';

    // Category filter
    const CATEGORY_FILTER_ANY = 'any';
    const CATEGORY_FILTER_IS_NOT_SET = 'is_not_set';
    const CATEGORY_FILTER_SELECTED = 'selected';

    // Milestone filter
    const MILESTONE_FILTER_ANY = 'any';
    const MILESTONE_FILTER_IS_NOT_SET = 'is_not_set';
    const MILESTONE_FILTER_SELECTED = 'selected';

    // Group
    const DONT_GROUP = 'dont';
    const GROUP_BY_ASSIGNEE = 'assignee';
    const GROUP_BY_PROJECT = 'project';
    const GROUP_BY_PROJECT_CLIENT = 'project_client';
    const GROUP_BY_MILESTONE = 'milestone';
    const GROUP_BY_CATEGORY = 'category';
    const GROUP_BY_LABEL = 'label';
    const GROUP_BY_CREATED_ON = 'created_on';
    const GROUP_BY_DUE_ON = 'due_on';
    const GROUP_BY_COMPLETED_ON = 'completed_on';
    const GROUP_BY_CUSTOM_FIELD_1 = 'custom_field_1';
    const GROUP_BY_CUSTOM_FIELD_2 = 'custom_field_2';
    const GROUP_BY_CUSTOM_FIELD_3 = 'custom_field_3';

    // Additional column names
    const ADDITIONAL_COLUMN_ASSIGNEE = 'assignee';
    const ADDITIONAL_COLUMN_PROJECT = 'project';
    const ADDITIONAL_COLUMN_CATEGORY = 'category';
    const ADDITIONAL_COLUMN_MILESTONE = 'milestone';
    const ADDITIONAL_COLUMN_CREATED_ON = 'created_on';
    const ADDITIONAL_COLUMN_AGE = 'age';
    const ADDITIONAL_COLUMN_CREATED_BY = 'created_by';
    const ADDITIONAL_COLUMN_DUE_ON = 'due_on';
    const ADDITIONAL_COLUMN_COMPLETED_ON = 'completed_on';
    const ADDITIONAL_COLUMN_CUSTOM_FIELD_1 = 'custom_field_1';
    const ADDITIONAL_COLUMN_CUSTOM_FIELD_2 = 'custom_field_2';
    const ADDITIONAL_COLUMN_CUSTOM_FIELD_3 = 'custom_field_3';
    const ADDITIONAL_COLUMN_TRACKED_TIME = 'tracked_time';
    const ADDITIONAL_COLUMN_ESTIMATED_TIME = 'estimated_time';

    /**
     * Execute this filter and return matching assignments
     *
     * $exclude is an array where key is class name and value
     * is array of ID-s that should be excluded
     *
     * @param IUser $user
     * @param null $additional
     * @return array|null
     * @throws InvalidInstanceError
     */
    function run(IUser $user, $additional = null) {
      if($user instanceof User) {
        $exclude = $additional && isset($additional['exclude']) ? $additional['exclude'] : null;

        // Get projects that we can query based on given criteria
        $project_ids = Projects::getProjectIdsByDataFilter($this, $user);

        // Query subtasks based on given criteria (optional, checked internally)
        list($include_subtasks, $subtasks, $subtask_parents) = $this->querySubtasks($user, $project_ids, $exclude);

        // Query project objects (extended with subtask parents, if needed)
        list($assignments, $projects, $categories, $milestones) = $this->queryProjectObjects($user, $project_ids, $exclude, $subtask_parents);
        
        // Query other assignees
        list($include_other_assignees, $other_asignees_data) = $this->queryOtherAssignees($user, $assignments);

        // Query tracking data
        list($include_tracking_data, $tracking_data) = $this->queryTrackingData($user, $assignments);

        if($assignments instanceof DBResult) {
          $result = $this->groupAssignments($user, $assignments, $projects, $categories, $milestones, $subtasks);

          // Now add subtasks and prepare individual rows with additional data, type cast etc
          foreach($result as $k => $v) {
            if(count($result[$k]['assignments'])) {
              foreach($result[$k]['assignments'] as $assignment_id => $assignment) {
                $result[$k]['assignments'][$assignment_id]['project'] = $projects && isset($projects[$result[$k]['assignments'][$assignment_id]['project_id']]) ? $projects[$result[$k]['assignments'][$assignment_id]['project_id']] : null;
                $result[$k]['assignments'][$assignment_id]['milestone'] = $milestones && isset($milestones[$result[$k]['assignments'][$assignment_id]['milestone_id']]) ? $milestones[$result[$k]['assignments'][$assignment_id]['milestone_id']] : null;
                $result[$k]['assignments'][$assignment_id]['category'] = $categories && isset($categories[$result[$k]['assignments'][$assignment_id]['category_id']]) ? $categories[$result[$k]['assignments'][$assignment_id]['category_id']] : null;

                $result[$k]['assignments'][$assignment_id]['assignee'] = $this->getUserDisplayName($result[$k]['assignments'][$assignment_id]['assignee_id']);
                $result[$k]['assignments'][$assignment_id]['created_by'] = $this->getUserDisplayName($result[$k]['assignments'][$assignment_id]['created_by_id'], array(
                  'full_name' => $assignment['created_by_name'],
                  'email' => $assignment['created_by_email'],
                ));
                $result[$k]['assignments'][$assignment_id]['completed_by'] = $this->getUserDisplayName($result[$k]['assignments'][$assignment_id]['completed_by_id'], array(
                  'full_name' => $assignment['completed_by_name'],
                  'email' => $assignment['completed_by_email'],
                ));

                if($include_subtasks && $subtasks && isset($subtasks[$assignment_id])) {
                  foreach($subtasks[$assignment_id] as $subtask_id => $subtask) {
                    $subtasks[$assignment_id][$subtask_id]['permalink'] = $this->getSubtaskPermalink($result[$k]['assignments'][$assignment_id]['project_id'], $result[$k]['assignments'][$assignment_id]['task_id'], $subtask_id);
                  } // foreach

                  $result[$k]['assignments'][$assignment_id]['subtasks'] = $subtasks[$assignment_id];
                } // if

                $result[$k]['assignments'][$assignment_id]['permalink'] = $this->getTaskPermalink($assignment);

                if($include_other_assignees) {
                	if($other_asignees_data && isset($other_asignees_data[$assignment_id])) {
                    $result[$k]['assignments'][$assignment_id]['other_assignees'] = isset($other_asignees_data[$assignment_id]) ? $other_asignees_data[$assignment_id] : null;
                  } else {
                  	$result[$k]['assignments'][$assignment_id]['other_assignees'] = null;
                  } // if
                } // if

                if($include_tracking_data) {
                  if($tracking_data && isset($tracking_data[$assignment_id])) {
                    $result[$k]['assignments'][$assignment_id]['estimated_time'] = isset($tracking_data[$assignment_id]['estimated_time']) ? $tracking_data[$assignment_id]['estimated_time'] : null;
                    $result[$k]['assignments'][$assignment_id]['estimated_job_type_id'] = isset($tracking_data[$assignment_id]['estimated_job_type_id']) ? $tracking_data[$assignment_id]['estimated_job_type_id'] : null;
                    $result[$k]['assignments'][$assignment_id]['tracked_time'] = isset($tracking_data[$assignment_id]['tracked_time']) ? $tracking_data[$assignment_id]['tracked_time'] : null;
                  } else {
                    $result[$k]['assignments'][$assignment_id]['estimated_time'] = null;
                    $result[$k]['assignments'][$assignment_id]['tracked_time'] = null;
                  } // if
                } // if
              } // foreach
            } else {
              unset($result[$k]);
            } // if
          } // foreach

          return $result;
        } else {
          return null;
        } // if
      } else {
        throw new InvalidInstanceError('user', $user, 'User');
      } // if
    } // run

    /**
     * Return data so it is good for export
     *
     * @param IUser $user
     * @param mixed $additional
     * @return array|void
     */
    function runForExport(IUser $user, $additional = null) {
      $result = $this->run($user, $additional);

      if($result) {
        $labels = Labels::getIdNameMap('AssignmentLabel');
        $include_tracking_data = $this->getIncludeTrackingData();

        $columns = array(
          'Assignment ID',
          'Type',
          'Project',
          'Project ID',
          'Assignee',
          'Assignee ID',
          'Priority',
          'Label',
          'Label ID',
          'Category',
          'Category ID',
          'Milestone',
          'Milestone ID',
          'Created On',
          'Created By',
          'Created By ID',
          'Due On',
          'Completed On',
          'Completed By',
          'Completed By ID',
          'Name',
          'Task ID',
        );

        if($include_tracking_data) {
          $columns[] = 'Estimated Time';
          $columns[] = 'Estimated Job Type';
          $columns[] = 'Tracked Time';
        } // if

        $custom_fields = array();

        foreach(CustomFields::getEnabledCustomFieldsByType('Task') as $field_name => $details) {
          if($details['is_enabled']) {
            $custom_fields[] = $field_name;

            $columns[] = lang('Custom Field: :name', array('name' => $details['label']));
          } // if
        } // foreach

        $this->beginExport($columns, array_var($additional, 'export_format'));

        foreach($result as $v) {
          if($v['assignments']) {
            foreach($v['assignments'] as $assignment) {
              $record = array(
                $assignment['id'],
                $assignment['type'],
                $assignment['project'],
                $assignment['project_id'],
                $assignment['assignee_id'] ? $assignment['assignee'] : null,
                $assignment['assignee_id'],
                $assignment['priority'],
                $assignment['label_id'] && isset($labels[$assignment['label_id']]) ? $labels[$assignment['label_id']] : null,
                $assignment['label_id'],
                $assignment['category_id'] ? $assignment['category'] : null,
                $assignment['category_id'],
                $assignment['milestone_id'] ? $assignment['milestone'] : null,
                $assignment['milestone_id'],
                $assignment['created_on'] instanceof DateTimeValue ? $assignment['created_on']->toMySQL() : null,
                $assignment['created_by_id'] ? $assignment['created_by'] : null,
                $assignment['created_by_id'],
                $assignment['due_on'] instanceof DateValue ? $assignment['due_on']->toMySQL() : null,
                $assignment['completed_on'] instanceof DateTimeValue ? $assignment['completed_on']->toMySQL() : null,
                $assignment['completed_by_id'] ? $assignment['completed_by'] : null,
                $assignment['completed_by_id'],
                $assignment['name'],
                ($assignment['type'] == 'Task' ? $assignment['task_id'] : ''),
              );

              if($include_tracking_data) {
                $record[] = $assignment['estimated_time'];
                $record[] = $assignment['estimated_job_type_id'];
                $record[] = $assignment['tracked_time'];
              } // if

              foreach($custom_fields as $custom_field) {
                $record[] = $assignment[$custom_field];
              } // foreach

              $this->exportWriteLine($record);

              if(isset($assignment['subtasks']) && $assignment['subtasks']) {
                foreach($assignment['subtasks'] as $subtask) {
                  $this->exportWriteLine(array(
                    $subtask['id'],
                    'Subtask',
                    $assignment['project'],
                    $assignment['project_id'],
                    $subtask['assignee_id'] ? $subtask['assignee'] : null,
                    $subtask['assignee_id'],
                    $subtask['priority'],
                    $subtask['label_id'] ? $subtask['label'] : null,
                    $subtask['label_id'],
                    $assignment['category_id'] ? $assignment['category'] : null,
                    $assignment['category_id'],
                    $assignment['milestone_id'] ? $assignment['milestone'] : null,
                    $assignment['milestone_id'],
                    $subtask['created_on'] instanceof DateTimeValue ? $subtask['created_on']->toMySQL() : null,
                    $subtask['created_by_id'] ? $subtask['created_by'] : null,
                    $subtask['created_by_id'],
                    $subtask['due_on'] instanceof DateValue ? $subtask['due_on']->toMySQL() : null,
                    $subtask['completed_on'] instanceof DateTimeValue ? $subtask['completed_on']->toMySQL() : null,
                    $subtask['completed_by_id'] ? $subtask['completed_by'] : null,
                    $subtask['completed_by_id'],
                    $subtask['body'],
                  ));
                } // foreach
              } // if
            } // foreach
          } // if
        } // foreach

        return $this->completeExport();
      } // if

      return null;
    } // runForExport

    /**
     * Cached map of user display names indexed by user ID
     *
     * @var array
     */
    private $users_map = false;

    /**
     * Get display name based on given parameters
     *
     * @param integer $user_id
     * @param mixed $user_display_name_elements
     * @return string
     */
    private function getUserDisplayName($user_id, $user_display_name_elements = null) {
      if($user_id) {
        if($this->users_map === false) {
          $this->users_map = Users::getIdNameMap(null, true);
        } // if

        if($this->users_map && isset($this->users_map[$user_id])) {
          return $this->users_map[$user_id];
        } elseif($user_display_name_elements) {
          return Users::getUserDisplayName($user_display_name_elements);
        } else {
          return lang('Unknown User');
        } // if
      } else {
        return null;
      } // if
    } // getUserDisplayName

    /**
     * Cached permalink patterns
     *
     * @var array
     */
    private $task_url_pattern;

    /**
     * Return task permalink
     *
     * @param array $task
     * @return string
     */
    function getTaskPermalink($task) {
      if(empty($this->task_url_pattern)) {
        $this->task_url_pattern = Router::assemble('project_task', array('project_slug' => '--PROJECT-SLUG--', 'task_id' => '--TASK-ID--'));
      } // if

      return str_replace(array('--PROJECT-SLUG--', '--TASK-ID--'), array($task['project_id'], $task['task_id']), $this->task_url_pattern);
    } // getTaskPermalink

    /**
     * Cached subtask URL pattern
     *
     * @var string
     */
    private $subtask_url_pattern;

    /**
     * Return subtask URL pattern
     *
     * @param integer $project_id
     * @param integer $task_id
     * @param integer $subtask_id
     * @return string
     */
    function getSubtaskPermalink($project_id, $task_id, $subtask_id) {
      if(empty($this->subtask_url_pattern)) {
        $this->subtask_url_pattern = Router::assemble('project_task_subtask', array('project_slug' => '--PROJECT-SLUG--', 'task_id' => '--TASK-ID--', 'subtask_id' => '--SUBTASK-ID--'));
      } // if

      return str_replace(array('--PROJECT-SLUG--', '--TASK-ID--', '--SUBTASK-ID--'), array($project_id, $task_id, $subtask_id), $this->subtask_url_pattern);
    } // getSubtaskPermalink

    /**
     * Query subtasks
     *
     * @param User $user
     * @param array $project_ids
     * @param array $exclude
     * @return array
     * @throws DataFilterConditionsError
     */
    private function querySubtasks($user, $project_ids, $exclude) {
      $subtasks_table = TABLE_PREFIX . 'subtasks';
      $project_objects_table = TABLE_PREFIX . 'project_objects';

      $subtasks = $subtask_parents = null;
      $include_subtasks = false;

      if($user->isProjectManager() && $this->getIncludeAllProjects()) {
        $type_filter = DB::prepare("($project_objects_table.project_id IN (?) AND $project_objects_table.type = ?)", $project_ids, 'Task');
      } else {
        $type_filter = $user->projects()->getVisibleTypesFilter($project_ids, array('Task'));
      } // if

      if(empty($type_filter)) {
        throw new DataFilterConditionsError('project_filter', $this->getProjectFilter(), null, 'User can\'t access any of the supported sections in any of the projects that this filter hits');
      } // if

      if($this->getIncludeSubtasks() && $this->getMilestoneFilter() == self::MILESTONE_FILTER_ANY && $this->getCategoryFilter() == self::CATEGORY_FILTER_ANY) {
        $include_subtasks = true;

        // This flag is ignored for subtasks, but we need memory allocated here
        // because we need to pass a reference to prepareConditions table
        $use_assignments_table = false;

        $conditions = $this->prepareConditions($user, $subtasks_table, $use_assignments_table, false);
        $exclude_conditions = $this->prepareExcludeConditions($exclude, $subtasks_table, 'parent_type', 'parent_id');

        if($exclude_conditions) {
          $conditions = "($conditions AND $exclude_conditions)";
        } // if

        switch($this->getGroupBy()) {
          case self::GROUP_BY_CREATED_ON:
            $order_by = "$subtasks_table.created_on, $subtasks_table.priority DESC";
            break;
          case self::GROUP_BY_DUE_ON:
            $order_by = "ISNULL($subtasks_table.due_on), $subtasks_table.due_on, $subtasks_table.priority DESC";
            break;
          case self::GROUP_BY_COMPLETED_ON:
            $order_by = "ISNULL($subtasks_table.completed_on), $subtasks_table.completed_on";
            break;
          default:
            $order_by = "$subtasks_table.priority DESC, completed_on";
        } // switch

        $subtask_rows = DB::execute("SELECT $subtasks_table.id, $subtasks_table.parent_type, $subtasks_table.parent_id, $subtasks_table.label_id, $subtasks_table.assignee_id, $subtasks_table.priority, $subtasks_table.body, $subtasks_table.created_on, DATEDIFF(UTC_TIMESTAMP(), $subtasks_table.created_on) AS 'age', $subtasks_table.created_by_id, $subtasks_table.created_by_name, $subtasks_table.created_by_email, $subtasks_table.due_on, $subtasks_table.completed_on, $subtasks_table.completed_by_id, $subtasks_table.completed_by_name, $subtasks_table.completed_by_email FROM $subtasks_table, $project_objects_table WHERE ($project_objects_table.type = $subtasks_table.parent_type AND $project_objects_table.id = $subtasks_table.parent_id) AND ($type_filter) AND ($conditions) ORDER BY $order_by");
        if($subtask_rows instanceof DBResult) {
          $subtask_rows->setCasting(array(
            'id' => DBResult::CAST_INT,
            'parent_id' => DBResult::CAST_INT,
          	'label_id' => DBResult::CAST_INT,
            'assignee_id' => DBResult::CAST_INT,
            'priority' => DBResult::CAST_INT,
            'created_on' => DBResult::CAST_DATETIME,
            'age' => DBResult::CAST_INT,
            'created_by_id' => DBResult::CAST_INT,
            'due_on' => DBResult::CAST_DATE,
            'completed_on' => DBResult::CAST_DATETIME,
          	'completed_by_id' => DBResult::CAST_INT,
          ));

          $subtasks = array();
          $subtask_parents = array();

          // Prepare subtaks parents, as well as subtasks for JSON encoding
          foreach($subtask_rows as $subtask_row) {
            $subtask_id = (integer) $subtask_row['id'];
            $parent_type = $subtask_row['parent_type'];
            $parent_id = (integer) $subtask_row['parent_id'];

            if(isset($subtasks[$parent_id])) {
              $subtasks[$parent_id][$subtask_id] = $subtask_row;
            } else {
              $subtasks[$parent_id] = array($subtask_id => $subtask_row);
            } // if

            $subtasks[$parent_id][$subtask_id]['assignee'] =$this->getUserDisplayName($subtasks[$parent_id][$subtask_id]['assignee_id']);

            $subtasks[$parent_id][$subtask_id]['created_by'] = $this->getUserDisplayName($subtasks[$parent_id][$subtask_id]['created_by_id'], array(
              'full_name' => $subtask_row['created_by_name'],
              'email' => $subtask_row['created_by_email'],
            ));

            $subtasks[$parent_id][$subtask_id]['completed_by'] = $this->getUserDisplayName($subtasks[$parent_id][$subtask_id]['completed_by_id'], array(
              'full_name' => $subtask_row['completed_by_name'],
              'email' => $subtask_row['completed_by_email'],
            ));

            if(isset($subtask_parents[$parent_type])) {
              $subtask_parents[$parent_type][] = $parent_id;
            } else {
              $subtask_parents[$parent_type] = array($parent_id);
            } // if
          } // foreach
        } // if
      } // if

      return array($include_subtasks, $subtasks, $subtask_parents);
    } // querySubtasks
    
    /**
     * Query other assignees
     *
     * @param User $user
     * @param DBResult $assignments
     * @return array
     */
    function queryOtherAssignees(User $user, $assignments) {
    	$include_other_assignees = false;
      $other_assignees_data = null;
      
      if($this->getIncludeOtherAssignees() && $assignments) {
      	$include_other_assignees = true;

        $task_ids = array();

        foreach($assignments as $assignment) {
          if($assignment['type'] == 'Task' && $assignment['assignee_id']) {
            $task_ids[] = (integer) $assignment['id'];
          } // if
        } // foreach
        
        if(count($task_ids)) {
        	$task_ids = array_unique($task_ids);
        	
        	$users_table = TABLE_PREFIX . 'users';
          $assignments_table = TABLE_PREFIX . 'assignments';
          
          // Get other assigness
          $rows = DB::execute("SELECT $assignments_table.parent_id, $assignments_table.user_id FROM $users_table, $assignments_table WHERE $users_table.id = $assignments_table.user_id AND $users_table.state >= ? AND $assignments_table.parent_type = ? AND $assignments_table.parent_id IN (?)", STATE_VISIBLE, 'Task', $task_ids);
          if($rows) {
          	$rows->setCasting(array(
              'parent_id' => DBResult::CAST_INT,
              'user_id' => DBResult::CAST_INT,
            ));

            foreach($rows as $row) {
            	$other_assignees_data[$row['parent_id']][] = $row['user_id'];
            } // foreach
          } // if
        } // if
      } // if
    	
    	return array($include_other_assignees, $other_assignees_data);
    } // queryOtherAssignees

    /**
     * Query tracked time and estimates
     *
     * @param User $user
     * @param DBResult $assignments
     * @return array
     */
    function queryTrackingData(User $user, $assignments) {
      $include_tracking_data = false;
      $tracking_data = null;

      if(AngieApplication::isModuleLoaded('tracking') && $this->getIncludeTrackingData() && $user->isProjectManager() && $assignments) {
        $include_tracking_data = true;

        $task_ids = array();

        foreach($assignments as $assignment) {
          if($assignment['type'] == 'Task') {
            $task_ids[] = (integer) $assignment['id'];
          } // if
        } // foreach

        if(count($task_ids)) {
          $task_ids = array_unique($task_ids);

          // Get estimates
          $rows = DB::execute("SELECT parent_id, job_type_id, value FROM " . TABLE_PREFIX . "estimates WHERE parent_type = 'Task' AND parent_id IN (?) ORDER BY created_on DESC", $task_ids);
          if($rows) {
            $rows->setCasting(array(
              'parent_id' => DBResult::CAST_INT,
              'job_type_id' => DBResult::CAST_INT,
              'value' => DBResult::CAST_FLOAT,
            ));

            foreach($rows as $row) {
              $assignment_id = $row['parent_id'];

              if(isset($tracking_data[$assignment_id])) {
                continue;
              } // if

              $tracking_data[$assignment_id] = array(
                'estimated_time' => $row['value'],
                'estimated_job_type_id' => $row['job_type_id'],
              );
            } // foreach
          } // if

          // Get tracked time
          $rows = DB::execute("SELECT parent_id, SUM(value) AS 'tracked_time' FROM " . TABLE_PREFIX . "time_records WHERE state >= ? AND parent_type = 'Task' AND parent_id IN (?) GROUP BY parent_type, parent_id", STATE_VISIBLE, $task_ids);
          if($rows) {
            $rows->setCasting(array(
              'parent_id' => DBResult::CAST_INT,
              'tracked_time' => DBResult::CAST_FLOAT,
            ));

            foreach($rows as $row) {
              $assignment_id = $row['parent_id'];

              if(isset($tracking_data[$assignment_id])) {
                $tracking_data[$assignment_id]['tracked_time'] = $row['tracked_time'];
              } else {
                $tracking_data[$assignment_id] = array(
                  'tracked_time' => $row['tracked_time']
                );
              } // if
            } // foreach
          } // if
        } // if
      } // if

      return array($include_tracking_data, $tracking_data);
    } // queryTrackingData

    /**
     * Query project objects based on given parameters
     *
     * @param User $user
     * @param array $project_ids
     * @param array $exclude
     * @param mixed $subtask_parents
     * @return DBResult
     */
    private function queryProjectObjects($user, $project_ids, $exclude, $subtask_parents) {
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      $assignments_table = TABLE_PREFIX . 'assignments';

      switch($this->getGroupBy()) {
        case self::GROUP_BY_CREATED_ON:
          $order_by = "$project_objects_table.created_on, $project_objects_table.priority DESC";
          break;
        case self::GROUP_BY_DUE_ON:
          $order_by = "ISNULL($project_objects_table.due_on), $project_objects_table.due_on, $project_objects_table.priority DESC, ISNULL($project_objects_table.position), $project_objects_table.position";
          break;
        case self::GROUP_BY_COMPLETED_ON:
          $order_by = "ISNULL($project_objects_table.completed_on), $project_objects_table.completed_on";
          break;
        default:
          $order_by = "$project_objects_table.priority DESC, ISNULL($project_objects_table.position), $project_objects_table.position";
      } // switch

      if($user->isProjectManager() && $this->getIncludeAllProjects()) {
        $type_filter = DB::prepare("($project_objects_table.project_id IN (?) AND $project_objects_table.type = (?))", $project_ids, 'Task');
      } else {
        $type_filter = $user->projects()->getVisibleTypesFilter($project_ids, array('Task'));
      } // if

      if ($type_filter) {

        $use_assignments_table = false;

        $conditions = $this->prepareConditions($user, $project_objects_table, $use_assignments_table, true);
        $exclude_conditions = $this->prepareExcludeConditions($exclude, $project_objects_table, 'type', 'id');

        if($exclude_conditions) {
          $conditions = "($conditions AND $exclude_conditions)";
        } // if

        $custom_fields = array();

        foreach(CustomFields::getEnabledCustomFieldsByType('Task') as $field_name => $details) {
          $custom_fields[] = "$project_objects_table.$field_name";
        } // foreach

        $custom_fields = count($custom_fields) ? ', ' . implode(', ', $custom_fields) : '';

        $fields = "$project_objects_table.id, $project_objects_table.type, $project_objects_table.project_id, $project_objects_table.assignee_id, $project_objects_table.label_id, $project_objects_table.category_id, $project_objects_table.milestone_id, $project_objects_table.name, $project_objects_table.body, $project_objects_table.created_on, DATEDIFF(UTC_TIMESTAMP(), $project_objects_table.created_on) AS 'age', $project_objects_table.created_by_id, $project_objects_table.created_by_name, $project_objects_table.created_by_email, $project_objects_table.due_on, $project_objects_table.completed_on, $project_objects_table.completed_by_id, $project_objects_table.completed_by_name, $project_objects_table.completed_by_email, $project_objects_table.priority, $project_objects_table.integer_field_1 AS 'task_id' $custom_fields";

        if(isset($subtask_parents)) {
          foreach($subtask_parents as $k => $v) {
            $subtask_parents[$k] = DB::prepare("($project_objects_table.type = ? AND $project_objects_table.id IN (?))", $k, $v);
          } // foreach

          $subtask_parents = implode(' OR ', $subtask_parents);

          if($use_assignments_table) {
            if($conditions) {
              $select_assignments_sql = "SELECT DISTINCT $fields FROM $project_objects_table LEFT OUTER JOIN $assignments_table ON $project_objects_table.type = $assignments_table.parent_type AND $project_objects_table.id = $assignments_table.parent_id WHERE ((($conditions) AND ($type_filter)) OR ($subtask_parents)) ORDER BY $order_by";
            } else {
              $select_assignments_sql = "SELECT DISTINCT $fields FROM $project_objects_table LEFT OUTER JOIN $assignments_table ON $project_objects_table.type = $assignments_table.parent_type AND $project_objects_table.id = $assignments_table.parent_id WHERE $subtask_parents ORDER BY $order_by";
            } // if
          } else {
            if($conditions) {
              $select_assignments_sql = "SELECT DISTINCT $fields FROM $project_objects_table WHERE ((($conditions) AND ($type_filter)) OR ($subtask_parents)) ORDER BY $order_by";
            } else {
              $select_assignments_sql = "SELECT DISTINCT $fields FROM $project_objects_table WHERE $subtask_parents ORDER BY $order_by";
            } // if
          } // if
        } else {
          if($conditions) {
            if($use_assignments_table) {
              $select_assignments_sql = "SELECT DISTINCT $fields FROM $project_objects_table LEFT OUTER JOIN $assignments_table ON $project_objects_table.type = $assignments_table.parent_type AND $project_objects_table.id = $assignments_table.parent_id WHERE ($conditions) AND ($type_filter) ORDER BY $order_by";
            } else {
              $select_assignments_sql = "SELECT DISTINCT $fields FROM $project_objects_table WHERE ($conditions) AND ($type_filter) ORDER BY $order_by";
            } // if
          } else {
            $select_assignments_sql = null; // No subtasks to extend search, and no records that could match this filter
          } // if
        } // if
      } else {
        $select_assignments_sql = null;
      } //if

      $assignments = $select_assignments_sql ? DB::execute($select_assignments_sql) : null;

      $projects = array();
      $categories = array();
      $milestones = array();

      if($assignments instanceof DBResult) {
        $assignments->setCasting(array(
          'id' => DBResult::CAST_INT,
          'project_id' => DBResult::CAST_INT,
          'assignee_id' => DBResult::CAST_INT,
          'priority' => DBResult::CAST_INT,
          'label_id' => DBResult::CAST_INT,
          'category_id' => DBResult::CAST_INT,
          'milestone_id' => DBResult::CAST_INT,
          'created_on' => DBResult::CAST_DATETIME,
          'age' => DBResult::CAST_INT,
          'created_by_id' => DBResult::CAST_INT,
          'due_on' => DBResult::CAST_DATE,
          'completed_on' => DBResult::CAST_DATETIME,
          'completed_by_id' => DBResult::CAST_INT,
          'task_id' => DBResult::CAST_INT,
        ));

        foreach($assignments as $assignment) {
          if($assignment['project_id'] && !isset($projects[$assignment['project_id']])) {
            $projects[(integer) $assignment['project_id']] = null;
          } // if

          if($assignment['category_id'] && !isset($projects[$assignment['category_id']])) {
            $categories[(integer) $assignment['category_id']] = null;
          } // if

          if($assignment['milestone_id'] && !isset($projects[$assignment['milestone_id']])) {
            $milestones[(integer) $assignment['milestone_id']] = null;
          } // if
        } // foreach

        $projects = count($projects) ? Projects::getIdNameMapByIds(array_keys($projects)) : null;
        $categories = count($categories) ? Categories::getIdNameMap(null, 'TaskCategory') : null;
        $milestones = count($milestones) ? Milestones::getIdNameMap(array_keys($milestones)) : null;
      } // if

      return array($assignments, $projects, $categories, $milestones);
    } // queryProjectObjects

    /**
     * Group assignments based on given criteria
     *
     * @param User $user
     * @param DBResult|array $assignments
     * @param array $projects
     * @param array $categories
     * @param array $milestones
     * @param array $subtasks
     * @return array
     */
    private function groupAssignments($user, $assignments, $projects, $categories, $milestones, $subtasks) {
      $projects_table = TABLE_PREFIX . 'projects';

      switch($this->getGroupBy()) {

        // Group assignments by assignee
        case self::GROUP_BY_ASSIGNEE:
          $user_ids = array();

          foreach($assignments as $assignment) {
            if($assignment['type'] == 'Task' && $assignment['assignee_id']) {
              if(!in_array($assignment['assignee_id'], $user_ids)) {
                $user_ids[] = $assignment['assignee_id'];
              } // if
            } // if
          } // foreach

          $result = array();
          if(count($user_ids)) {
            $user_id_name_map = Users::getIdNameMap($user_ids);

            foreach($user_id_name_map as $user_id => $user_name) {
              $result["user-$user_id"] = array(
                'label' => $user_name,
                'assignments' => array(),
              );
            } // foreach
          } // if

          $result['unknown-user'] = array(
            'label' => lang('Unassigned'),
            'assignments' => array(),
          );

          foreach($assignments as $assignment) {
            $assignee_id = $assignment['assignee_id'];

            if(isset($result["user-$assignee_id"])) {
              $result["user-$assignee_id"]['assignments'][$assignment['id']] = $assignment;
            } else {
              $result['unknown-user']['assignments'][$assignment['id']] = $assignment;
            } // if
          } // foreach

          break;

        // Group assignments by project
        case self::GROUP_BY_PROJECT:
          $result = $this->groupAssignmentsByProject($user, $assignments, $projects, $this->getFavoriteOnTopWhenGroupingByProject());

          break;

        // Group assignments by project client
        case self::GROUP_BY_PROJECT_CLIENT:
          $owner_company_id = Companies::findOwnerCompany()->getId();
          $project_clients = null;

          if($projects) {
            $companies_table = TABLE_PREFIX . 'companies';

            $rows = DB::execute("SELECT $projects_table.id AS 'project_id', $companies_table.id AS 'client_id', $companies_table.name AS 'client_name' FROM $projects_table, $companies_table WHERE $projects_table.company_id = $companies_table.id AND $projects_table.id IN (?) AND $companies_table.id != ? ORDER BY $companies_table.name", array_keys($projects), $owner_company_id);

            if($rows instanceof DBResult) {
              $project_clients = array();

              foreach($rows as $row) {
                $client_id = (integer) $row['client_id'];
                $project_id = (integer) $row['project_id'];

                $project_clients[$project_id] = $client_id;

                if(!isset($result["client-$client_id"])) {
                  $result["client-$client_id"] = array(
                    'label' => $row['client_name'],
                		'assignments' => array(),
                  );
                } // if
              } // foreach
            } // if
          } // if

          $result['internal-projects'] = array(
            'label' => lang('Internal'),
            'assignments' => array(),
          );

          foreach($assignments as $assignment) {
            $project_id = (integer) $assignment['project_id'];

            if(isset($project_clients[$project_id]) && $project_clients[$project_id]) {
              $result['client-' . $project_clients[$project_id]]['assignments'][$assignment['id']] = $assignment;
            } else {
              $result['internal-projects']['assignments'][$assignment['id']] = $assignment;
            } // if
          } // foreach

          break;

        // Milestone
        case self::GROUP_BY_MILESTONE:
          return $this->groupAssignmentsByMilestone($user, $assignments);

        // Category
        case self::GROUP_BY_CATEGORY:
          $result = array();
          $not_set = array();
          $category_ids = array();

          // Build assignments map
          foreach($assignments as $assignment) {
            $category_id = $assignment['category_id'];

            if($category_id) {
              $key = "category-{$category_id}";

              if(isset($result[$key])) {
                $result[$key]['assignments'][$assignment['id']] = $assignment;
              } else {
                $category_ids[] = $category_id;

                $result[$key] = array(
                  'label' => "Category #{$category_id}",
                  'assignments' => array(
                    $assignment['id'] => $assignment,
                  )
                );
              } // if

            } else {
              $not_set[$assignment['id']] = $assignment;
            } // if
          } // if

          // Now update names
          if($category_ids) {
            $categories_table = TABLE_PREFIX . 'categories';

            $rows = DB::execute("SELECT $categories_table.id, $categories_table.name, $projects_table.name AS 'project_name' FROM $categories_table LEFT OUTER JOIN $projects_table ON $categories_table.parent_type = 'Project' AND $categories_table.parent_id = $projects_table.id WHERE $categories_table.id IN (?)", $category_ids);
            if($rows) {
              foreach($rows as $row) {
                $result['category-' . $row['id']]['label'] = $row['project_name'] ? $row['project_name'] . ' > ' . $row['name'] : $row['name'];
              } // foreach
            } // if
          } // if

          $result['not_set'] = array(
            'label' => lang('Category not Set'),
            'assignments' => $not_set,
          );

          break;

        // Label
        case self::GROUP_BY_LABEL:
          $result = array();
          $not_set = array();
          $label_ids = array();

          // Build assignments map
          foreach($assignments as $assignment) {
            $label_id = $assignment['label_id'];

            if($label_id) {
              $key = "label-{$label_id}";

              if(isset($result[$key])) {
                $result[$key]['assignments'][$assignment['id']] = $assignment;
              } else {
                $label_ids[] = $label_id;

                $result[$key] = array(
                  'label' => "Label #{$label_id}",
                  'assignments' => array(
                    $assignment['id'] => $assignment,
                  )
                );
              } // if

            } else {
              $not_set[$assignment['id']] = $assignment;
            } // if
          } // if

          // Now update names
          if($label_ids) {
            $rows = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'labels WHERE id IN (?)', $label_ids);

            if($rows) {
              foreach($rows as $row) {
                $result['label-' . $row['id']]['label'] = $row['name'];
              } // foreach
            } // if
          } // if

          $result['not_set'] = array(
            'label' => lang('Label not Set'),
            'assignments' => $not_set,
          );

          break;

        // Group by the date when assignment was created
        case self::GROUP_BY_CREATED_ON:
          $result = array();
          $unknown = array();

          foreach($assignments as $assignment) {
            if($assignment['created_on'] instanceof DateTimeValue) {
              $formatted_date = $assignment['created_on']->formatDateForUser($user, 0);
              $timestamp = $assignment['created_on']->beginningOfDay()->getTimestamp();
              if(!isset($result[$timestamp])) {
                $result[$timestamp] = array(
                  'label' => (string) $formatted_date,
                	'assignments' => array(),
                );
              } // if

              $result[$timestamp]['assignments'][$assignment['id']] = $assignment;
            } else {
              $unknown[$assignment['id']] = $assignment;
            } // if
          } // foreach

          // recently added tasks are at the top
          krsort($result, SORT_NUMERIC);

          $result['unknown'] = array(
            'label' => lang('Unknown'),
            'assignments' => $unknown,
          );

          break;

        // Group by the date when assignment is due
        case self::GROUP_BY_DUE_ON:
          return $this->groupAssignmentsByDueOn($user, $assignments, $subtasks);

        // Group by the date when assignment was completed
        case self::GROUP_BY_COMPLETED_ON:
          $result = array();
          $open_assignments = array();

          foreach($assignments as $assignment) {
            if($assignment['completed_on'] instanceof DateTimeValue) {
              $formatted_date = $assignment['completed_on']->formatDateForUser($user, 0);
              $timestamp = $assignment['completed_on']->beginningOfDay()->getTimestamp();

              if(!isset($result[$timestamp])) {
                $result[$timestamp] = array(
                  'label' => $formatted_date,
                	'assignments' => array(),
                );
              } // if

              $result[$timestamp]['assignments'][$assignment['id']] = $assignment;
            } else {
              $open_assignments[$assignment['id']] = $assignment;
            } // if
          } // foreach

          // most recently completed tasks are at the top
          krsort($result, SORT_NUMERIC);

          $result['open'] = array(
            'label' => lang('Open'),
            'assignments' => $open_assignments,
          );

          break;

        // Group by custom fields
        case self::GROUP_BY_CUSTOM_FIELD_1:
          return $this->groupAssignmentsByCustomField($user, $assignments, 'custom_field_1');
        case self::GROUP_BY_CUSTOM_FIELD_2:
          return $this->groupAssignmentsByCustomField($user, $assignments, 'custom_field_2');
        case self::GROUP_BY_CUSTOM_FIELD_3:
          return $this->groupAssignmentsByCustomField($user, $assignments, 'custom_field_3');

        // Don't group - list them all
        default:
          $result['all'] = array(
            'label' => lang('All Assignments'),
            'assignments' => array(),
          );

          foreach($assignments as $assignment) {
            $result['all']['assignments'][$assignment['id']] = $assignment;
          } // foreach
      } // switch

      return $result;
    } // groupAssignments

    /**
     * Group assignments by due date
     *
     * @param IUser $user
     * @param $assignments
     * @param array $subtasks
     * @return array
     */
    private function groupAssignmentsByDueOn($user, $assignments, $subtasks) {
      $result = array();
      $not_set = array();

      foreach($assignments as $assignment) {
        if($this->getIncludeSubtasks()) {
          $assignment_id = $assignment['id'];
          $reference_date = $assignment['due_on'];

          if($subtasks && isset($subtasks[$assignment_id])) {
            foreach($subtasks[$assignment_id] as $subtask) {
              if($subtask['completed_on'] instanceof DateValue) {
                continue; // Ignore completed subtasks
              } // if

              if(isset($subtask['due_on']) && $subtask['due_on'] instanceof DateValue) {
                if(empty($reference_date) || $reference_date->getTimestamp() > $subtask['due_on']->getTimestamp()) {
                  $reference_date = $subtask['due_on'];
                } // if
              } // if
            } // foreach
          } // if

          if($reference_date instanceof DateValue) {
            $formatted_date = lang('Due on :date', array('date' => $reference_date->formatForUser($user, 0)));
            $reference_timestamp = $reference_date->getTimestamp();
          } else {
            $formatted_date = $reference_timestamp = null;
          } // if
        } else {
          if($assignment['due_on'] instanceof DateValue) {
            $formatted_date = lang('Due on :date', array('date' => $assignment['due_on']->formatForUser($user, 0)));
            $reference_timestamp = $assignment['due_on']->getTimestamp();
          } else {
            $formatted_date = $reference_timestamp = null;
          } // if

          //$formatted_date = $assignment['due_on'] instanceof DateValue ? lang('Due on :date', array('date' => $assignment['due_on']->formatForUser($user, 0))) : null;
        } // if

        if($reference_timestamp && $formatted_date) {
          if(!isset($result[$reference_timestamp])) {
            $result[$reference_timestamp] = array(
              'label' => $formatted_date,
              'assignments' => array(),
            );
          } // if

          $result[$reference_timestamp]['assignments'][$assignment['id']] = $assignment;
        } else {
          $not_set[$assignment['id']] = $assignment;
        } // if
      } // foreach

      ksort($result);

      $result['not-set'] = array(
        'label' => lang('Due Date not Set'),
        'assignments' => $not_set,
      );

      return $result;
    } // groupAssignmentsByDueOn

    /**
     * Group assignments by project
     *
     * @param User $user
     * @param array $assignments
     * @param array $projects
     * @param boolean $favorite_projects_first
     * @return array
     */
    private function groupAssignmentsByProject($user, $assignments, $projects, $favorite_projects_first = true) {
      if($projects) {
        $url_pattern = Router::assemble('project', array('project_slug' => '--PROJECT-SLUG--'));

        if($favorite_projects_first) {
          $favorite = $non_favorite = array();

          foreach($projects as $k => $v) {
            if(Favorites::isFavorite(array('Project', $k), $user)) {
              $favorite["project-$k"] = array(
                'label' => $v,
                'url' => str_replace('--PROJECT-SLUG--', $k, $url_pattern),
                'assignments' => array(),
              );
            } else {
              $non_favorite["project-$k"] = array(
                'label' => $v,
                'url' => str_replace('--PROJECT-SLUG--', $k, $url_pattern),
                'assignments' => array(),
              );
            } // if
          } // foreach

          if(count($favorite) && count($non_favorite)) {
            $result = array_merge($favorite, $non_favorite);
          } elseif(count($favorite)) {
            $result = $favorite;
          } elseif(count($non_favorite)) {
            $result = $non_favorite;
          } else {
            $result = array();
          } // if
        } else {
          $result = array();

          foreach($projects as $k => $v) {
            $result["project-$k"] = array(
              'label' => $v,
              'url' => str_replace('--PROJECT-SLUG--', $k, $url_pattern),
              'assignments' => array(),
            );
          } // foreach
        } // if
      } // if

      $result['unknow-project'] = array(
        'label' => lang('Unknown'),
        'assignments' => array(),
      );

      foreach($assignments as $assignment) {
        $project_id = $assignment['project_id'];

        if(isset($result["project-$project_id"])) {
          $result["project-$project_id"]['assignments'][$assignment['id']] = $assignment;
        } else {
          $result['unknow-project']['assignments'][$assignment['id']] = $assignment;
        } // if
      } // foreach

      return $result;
    } // groupAssignmentsByProject

    /**
     * Group assignments by milestone
     *
     * @param User $user
     * @param array $assignments
     * @return array
     */
    function groupAssignmentsByMilestone($user, $assignments) {
      $result = array();
      $not_set = array();
      $milestone_ids = array();

      $project_objects_table = TABLE_PREFIX . 'project_objects';
      $projects_table = TABLE_PREFIX . 'projects';

      // Build assignments map
      foreach($assignments as $assignment) {
        $milestone_id = $assignment['milestone_id'];

        if($milestone_id) {
          $key = "milestone-{$milestone_id}";

          if(isset($result[$key])) {
            $result[$key]['assignments'][$assignment['id']] = $assignment;
          } else {
            $milestone_ids[] = $milestone_id;

            $result[$key] = array(
              'label' => "Milestone #{$milestone_id}",
              'assignments' => array(
                $assignment['id'] => $assignment,
              )
            );
          } // if

        } else {
          $key = $assignment['project_id'];

          if(isset($not_set[$key])) {
            $not_set[$key]['assignments'][$assignment['id']] = $assignment;
          } else {
            $not_set[$key] = array(
              'label' => $assignment['project_name'] . ' > ' . lang('Milestone not Set'),
              'assignments' => array($assignment['id'] => $assignment),
            );
          } // if
        } // if
      } // if

      // Now update names
      if($milestone_ids) {
        $rows = DB::execute("SELECT $project_objects_table.id, $project_objects_table.name, $projects_table.name AS 'project_name' FROM $project_objects_table LEFT OUTER JOIN $projects_table ON $project_objects_table.project_id = $projects_table.id WHERE $project_objects_table.type = 'Milestone' AND $project_objects_table.id IN (?)", $milestone_ids);
        if($rows) {
          foreach($rows as $row) {
            $result['milestone-' . $row['id']]['label'] = $row['project_name'] ? $row['project_name'] . ' > ' . $row['name'] : $row['name'];
          } // foreach
        } // if
      } // if

      if(count($not_set)) {
        $rows = DB::execute("SELECT id, name FROM $projects_table WHERE id IN (?) AND state >= ?", array_keys($not_set), STATE_ARCHIVED);
        if($rows) {
          foreach($rows as $row) {
            $not_set[$row['id']]['label'] = $row['name'] . ' > ' . lang('Milestone not Set');
          } // foreach
        } // if

        foreach($not_set as $k => $v) {
          $result["project-{$k}"] = $v;
        } // foreach
      } // if

      return $result;
    } // groupAssignmentsByMilestone

    /**
     * Group assignments by custom field
     *
     * @param User $user
     * @param DBResult $assignments
     * @param string $field_name
     * @return array
     */
    function groupAssignmentsByCustomField($user, $assignments, $field_name) {
      $result = array();
      $not_set = array();

      // Build assignments map
      foreach($assignments as $assignment) {
        $value = isset($assignment[$field_name]) && trim($assignment[$field_name]) ? trim($assignment[$field_name]) : null;

        if($value) {
          if(isset($result[$value])) {
            $result[$value]['assignments'][$assignment['id']] = $assignment;
          } else {
            $result[$value] = array(
              'label' => $value,
              'assignments' => array(
                $assignment['id'] => $assignment,
              )
            );
          } // if

        } else {
          $not_set[$assignment['id']] = $assignment;
        } // if
      } // if

      ksort($result);

      $result['not_set'] = array(
        'label' => lang('Not Set'),
        'assignments' => $not_set,
      );

      return $result;
    } // groupAssignmentsByCustomField

    /**
     * Prepare conditions based on filter settings
     *
     * $extended_assignee_filter determins whether generator will treat table as
     * having multiple assignees support (tasks, milestones etc), or single
     * assignee support (subtasks)
     *
     * @param User $user
     * @param string $table_name
     * @param boolean $use_assignments_table
     * @param boolean $extended_assignee_filter
     * @return string
     * @throws DataFilterConditionsError
     * @throws InvalidParamError
     */
    private function prepareConditions(User $user, $table_name, &$use_assignments_table, $extended_assignee_filter) {
      $assignments_table = TABLE_PREFIX . 'assignments';
      $project_objects_table = TABLE_PREFIX . 'project_objects';

      $conditions = array();

      if($table_name == $project_objects_table) {
        $conditions[] = DB::prepare("($table_name.state >= ? AND $table_name.visibility >= ?)", STATE_VISIBLE, $user->getMinVisibility());
      } else {
        $conditions[] = DB::prepare("($table_name.state >= ?)", STATE_VISIBLE);
      } // if

      // User filter
      switch($this->getUserFilter()) {
        case self::USER_FILTER_ANYBODY:
          break;

        // Not assigned to anyone
        case self::USER_FILTER_NOT_ASSIGNED:
          $conditions[] = "($table_name.assignee_id IS NULL OR $table_name.assignee_id = '0')";
          break;

        // Logged user, applicable only to project objects
        case self::USER_FILTER_LOGGED_USER:
          $user_id = DB::escape($user->getId());

          if($extended_assignee_filter && $table_name == $project_objects_table) {
            $use_assignments_table = true;

            $conditions[] = "($table_name.assignee_id = $user_id OR ($assignments_table.parent_type = $table_name.type AND $assignments_table.parent_id = $table_name.id AND $assignments_table.user_id = $user_id))";
          } else {
            $conditions[] = DB::prepare("($table_name.assignee_id = $user_id)", $user_id);
          } // if
          break;

        // Logged user is responsible
        case self::USER_FILTER_LOGGED_USER_RESPONSIBLE:
          $conditions[] = DB::prepare("($table_name.assignee_id = ?)", $user->getId());
          break;

        // All members of a specific company, responsible or assigned
        case self::USER_FILTER_COMPANY:
          $company_id = $this->getUserFilterCompanyId();

          if($company_id) {
            $company = Companies::findById($company_id);

            if($company instanceof Company) {
              $visible_user_ids = $user->visibleUserIds($company);

              if($visible_user_ids) {
                $visible_user_ids = DB::escape($visible_user_ids);

                if($extended_assignee_filter && $table_name == $project_objects_table) {
                  $use_assignments_table = true;

                  $conditions[] = "($table_name.assignee_id IN ($visible_user_ids) OR ($assignments_table.parent_type = $table_name.type AND $assignments_table.parent_id = $table_name.id AND $assignments_table.user_id IN ($visible_user_ids)))";
                } else {
                  $conditions[] = DB::prepare("($table_name.assignee_id IN ($visible_user_ids))", $visible_user_ids);
                } // if
              } else {
                throw new DataFilterConditionsError('user_filter', self::USER_FILTER_COMPANY, $company_id, "User can't see any members of target company");
              } // if
            } else {
              throw new DataFilterConditionsError('user_filter', self::USER_FILTER_COMPANY, $company_id, 'Company not found');
            } // if
          } else {
            throw new DataFilterConditionsError('user_filter', self::USER_FILTER_COMPANY, $company_id, 'Company not selected');
          } // if

          break;

        // All members of a specific company, responsible only
        case self::USER_FILTER_COMPANY_RESPONSIBLE:
          $company_id = $this->getUserFilterCompanyId();

          if($company_id) {
            $company = Companies::findById($company_id);

            if($company instanceof Company) {
              $visible_user_ids = $user->visibleUserIds($company);

              if($visible_user_ids) {
                $conditions[] = DB::prepare("($table_name.assignee_id IN (?))", $visible_user_ids);
              } else {
                throw new DataFilterConditionsError('user_filter', self::USER_FILTER_COMPANY_RESPONSIBLE, $company_id, "User can't see any members of target company");
              } // if
            } else {
              throw new DataFilterConditionsError('user_filter', self::USER_FILTER_COMPANY_RESPONSIBLE, $company_id, 'Company not found');
            } // if
          } else {
            throw new DataFilterConditionsError('user_filter', self::USER_FILTER_COMPANY_RESPONSIBLE, $company_id, 'Company not selected');
          } // if

          break;

        // Selected users, responslbe or assigned
        case self::USER_FILTER_SELECTED:
          $user_ids = $this->getUserFilterSelectedUsers();

          if($user_ids) {
            $visible_user_ids = $user->visibleUserIds();

            if($visible_user_ids) {
              foreach($user_ids as $k => $v) {
                if(!in_array($v, $visible_user_ids)) {
                  unset($user_ids[$k]);
                } // if
              } // foreach

              if(count($user_ids)) {
                $user_ids = DB::escape($user_ids);

                if($extended_assignee_filter && $table_name == $project_objects_table) {
                  $use_assignments_table = true;

                  $conditions[] = "($table_name.assignee_id IN ($user_ids) OR ($assignments_table.parent_type = $table_name.type AND $assignments_table.parent_id = $table_name.id AND $assignments_table.user_id IN ($user_ids)))";
                } else {
                  $conditions[] = DB::prepare("($table_name.assignee_id IN ($user_ids))", $visible_user_ids);
                } // if
              } else {
                throw new DataFilterConditionsError('user_filter', self::USER_FILTER_SELECTED, $user_ids, 'Non of the selected users is visible');
              } // if
            } else {
              throw new DataFilterConditionsError('user_filter', self::USER_FILTER_SELECTED, $user_ids, "User can't see anyone else");
            } // if
          } else {
            throw new DataFilterConditionsError('user_filter', self::USER_FILTER_SELECTED, $user_ids, 'No users selected');
          } // if

          break;

        // Selected users, responslbe only
        case self::USER_FILTER_SELECTED_RESPONSIBLE:
          $user_ids = $this->getUserFilterSelectedUsers();

          if($user_ids) {
            $visible_user_ids = $user->visibleUserIds();

            if($visible_user_ids) {
              foreach($user_ids as $k => $v) {
                if(!in_array($v, $visible_user_ids)) {
                  unset($user_ids[$k]);
                } // if
              } // foreach

              if(count($user_ids)) {
                $conditions[] = DB::prepare("($table_name.assignee_id IN (?))", $user_ids);
              } else {
                throw new DataFilterConditionsError('user_filter', self::USER_FILTER_SELECTED_RESPONSIBLE, $user_ids, 'Non of the selected users is visible');
              } // if
            } else {
              throw new DataFilterConditionsError('user_filter', self::USER_FILTER_SELECTED_RESPONSIBLE, $user_ids, "User can't see anyone else");
            } // if
          } else {
            throw new DataFilterConditionsError('user_filter', self::USER_FILTER_SELECTED_RESPONSIBLE, $user_ids, 'No users selected');
          } // if

          break;
        default:
          throw new DataFilterConditionsError('user_filter', $this->getUserFilter(), 'mixed', 'Unknown user filter');
      } // switch

      // Label filter
      switch($this->getLabelfilter()) {
        case self::LABEL_FILTER_ANY:
          break;

        case self::LABEL_FILTER_IS_NOT_SET:
          $conditions[] = DB::prepare("($table_name.label_id = ? OR $table_name.label_id IS NULL)", 0);
          break;

        case self::LABEL_FILTER_SELECTED:
          $label_names = $this->getLabelNames() ? explode(',', $this->getLabelNames()) : null;

          if(is_array($label_names)) {
            foreach($label_names as $k => $v) {
              $label_names[$k] = trim($v);
            } // foreach
          } // if

          $label_ids = $label_names ? Labels::getIdsByNames($label_names, 'AssignmentLabel') : null;

          if($label_ids) {
            $conditions[] = DB::prepare("($table_name.label_id IN (?))", $label_ids);
          } else {
            throw new DataFilterConditionsError('label_filter', self::LABEL_FILTER_SELECTED, $label_names, 'There are no labels found by the names provided');
          } // if

          break;

        case self::LABEL_FILTER_NOT_SELECTED:
          $label_names = $this->getLabelNames() ? explode(',', $this->getLabelNames()) : null;

          if(is_array($label_names)) {
            foreach($label_names as $k => $v) {
              $label_names[$k] = trim($v);
            } // foreach
          } // if

          $label_ids = $label_names ? Labels::getIdsByNames($label_names, 'AssignmentLabel') : null;

          if($label_ids) {
            $conditions[] = DB::prepare("($table_name.label_id NOT IN (?))", $label_ids);
          } else {
            throw new DataFilterConditionsError('label_filter', self::LABEL_FILTER_NOT_SELECTED, $label_names, 'There are no labels found by the names provided');
          } // if

          break;

        default:
          throw new DataFilterConditionsError('label_filter', $this->getLabelfilter(), 'mixed', 'Unknown label filter');
      } // switch

      // Milestone and category related filters apply only to project objects
      if($table_name == TABLE_PREFIX . 'project_objects') {

        // Category filter
        switch($this->getCategoryFilter()) {
          case self::CATEGORY_FILTER_ANY:
            break;

          case self::CATEGORY_FILTER_IS_NOT_SET:
            $conditions[] = DB::prepare("($table_name.category_id = ? OR $table_name.category_id IS NULL)", 0);
            break;

          case self::CATEGORY_FILTER_SELECTED:
            $category_names = $this->getCategoryNames() ? explode(',', $this->getCategoryNames()) : null;

            if(is_array($category_names)) {
              foreach($category_names as $k => $v) {
                $category_names[$k] = trim($v);
              } // foreach
            } // if

            $category_ids = $category_names ? Categories::getIdsByNames($category_names, 'TaskCategory') : null;

            if($category_ids) {
              $conditions[] = DB::prepare("($table_name.category_id IN (?))", $category_ids);
            } else {
              throw new DataFilterConditionsError('category_filter', self::CATEGORY_FILTER_SELECTED, $category_names, 'There are no categories found by the names provided');
            } // if

            break;

          default:
            throw new DataFilterConditionsError('category_filter', $this->getCategoryFilter(), 'mixed', 'Unknown category filter');
        } // switch

        // Milestone filter
        switch($this->getMilestoneFilter()) {
          case self::MILESTONE_FILTER_ANY:
            break;

          case self::MILESTONE_FILTER_IS_NOT_SET:
            $conditions[] = DB::prepare("($table_name.milestone_id = ? OR $table_name.milestone_id IS NULL)", 0);
            break;

          case self::MILESTONE_FILTER_SELECTED:
            $milestone_names = $this->getMilestoneNames() ? explode(',', $this->getMilestoneNames()) : null;

            if(is_array($milestone_names)) {
              foreach($milestone_names as $k => $v) {
                $milestone_names[$k] = trim($v);
              } // foreach
            } // if

            $milestone_ids = $milestone_names ? Milestones::getIdsByNames($milestone_names) : null;

            if($milestone_ids) {
              $conditions[] = DB::prepare("($table_name.milestone_id IN (?))", $milestone_ids);
            } else {
              throw new DataFilterConditionsError('milestone_filter', self::MILESTONE_FILTER_SELECTED, $milestone_names, 'There are no milestones found by the names provided');
            } // if

            break;

          default:
            throw new DataFilterConditionsError('milestone_filter', $this->getMilestoneFilter(), 'mixed', 'Unknown milestone filter');
        } // switch

      } // if

      // Created by filter
      switch($this->getCreatedByFilter()) {
        case self::USER_FILTER_ANYBODY:
          break;

        // Anonymous user
        case self::USER_FILTER_ANONYMOUS:
          $conditions[] = DB::prepare("($table_name.created_by_id = ? OR $table_name.created_by_id IS NULL)", 0); break;
          break;

        // Logged user
        case self::USER_FILTER_LOGGED_USER:
          $conditions[] = DB::prepare("($table_name.created_by_id = ?)", $user->getId()); break;

        // All members of a specific company
        case self::USER_FILTER_COMPANY:
          $company_id = $this->getCreatedByCompanyId();

          if($company_id) {
            $company = Companies::findById($company_id);

            if($company instanceof Company) {
              $visible_user_ids = $user->visibleUserIds($company);

              if($visible_user_ids) {
                $conditions[] = DB::prepare("($table_name.created_by_id IN (?))", $visible_user_ids);
              } else {
                throw new DataFilterConditionsError('created_by_filter', self::USER_FILTER_COMPANY, $company_id, "User can't see any members of target company");
              } // if
            } else {
              throw new DataFilterConditionsError('created_by_filter', self::USER_FILTER_COMPANY, null, 'Company not found');
            } // if
          } else {
            throw new DataFilterConditionsError('created_by_filter', self::USER_FILTER_COMPANY, null, 'Company ID not set');
          } // if

          break;

        // Selected users
        case self::USER_FILTER_SELECTED:
          $user_ids = $this->getCreatedByUsers();

          if($user_ids) {
            $visible_user_ids = $user->visibleUserIds();

            if($visible_user_ids) {
              foreach($user_ids as $k => $v) {
                if(!in_array($v, $visible_user_ids)) {
                  unset($user_ids[$k]);
                } // if
              } // foreach

              if(count($user_ids)) {
                $conditions[] = DB::prepare("($table_name.created_by_id IN (?))", $user_ids);
              } else {
                throw new DataFilterConditionsError('created_by_filter', self::USER_FILTER_SELECTED, $user_id, 'Non of the selected users is visible');
              } // if
            } else {
              throw new DataFilterConditionsError('created_by_filter', self::USER_FILTER_SELECTED, null, "User can't see anyone else");
            } // if
          } else {
            throw new DataFilterConditionsError('created_by_filter', self::USER_FILTER_SELECTED, null, 'No users selected');
          } // if

          break;
        default:
          throw new DataFilterConditionsError('created_by_filter', $this->getCreatedByFilter(), 'mixed', 'Unknown created by filter');
      } // switch

      // Delegated by filter
      switch($this->getDelegatedByFilter()) {
        case self::USER_FILTER_ANYBODY:
          break;

        // Logged user
        case self::USER_FILTER_LOGGED_USER:
          $conditions[] = DB::prepare("($table_name.delegated_by_id = ?)", $user->getId()); break;

        // All members of a specific company
        case self::USER_FILTER_COMPANY:
          $company_id = $this->getDelegatedByCompanyId();

          if($company_id) {
            $company = Companies::findById($company_id);

            if($company instanceof Company) {
              $visible_user_ids = $user->visibleUserIds($company);

              if($visible_user_ids) {
                $conditions[] = DB::prepare("($table_name.delegated_by_id IN (?))", $visible_user_ids);
              } else {
                throw new DataFilterConditionsError('delegated_by_filter', self::USER_FILTER_COMPANY, $company_id, "User can't see any members of target company");
              } // if
            } else {
              throw new DataFilterConditionsError('delegated_by_filter', self::USER_FILTER_COMPANY, null, 'Company not found');
            } // if
          } else {
            throw new DataFilterConditionsError('delegated_by_filter', self::USER_FILTER_COMPANY, null, 'Company ID not set');
          } // if

          break;

        // Selected users
        case self::USER_FILTER_SELECTED:
          $user_ids = $this->getDelegatedByUsers();

          if($user_ids) {
            $visible_user_ids = $user->visibleUserIds();

            if($visible_user_ids) {
              foreach($user_ids as $k => $v) {
                if(!in_array($v, $visible_user_ids)) {
                  unset($user_ids[$k]);
                } // if
              } // foreach

              if(count($user_ids)) {
                $conditions[] = DB::prepare("($table_name.delegated_by_id IN (?))", $user_ids);
              } else {
                throw new DataFilterConditionsError('delegated_by_filter', self::USER_FILTER_SELECTED, $user_id, 'Non of the selected users is visible');
              } // if
            } else {
              throw new DataFilterConditionsError('delegated_by_filter', self::USER_FILTER_SELECTED, null, "User can't see anyone else");
            } // if
          } else {
            throw new DataFilterConditionsError('delegated_by_filter', self::USER_FILTER_SELECTED, null, 'No users selected');
          } // if

          break;
        default:
          throw new DataFilterConditionsError('delegated_by_filter', $this->getDelegatedByFilter(), 'mixed', 'Unknown delegated by filter');
      } // switch

      $this->prepareDateFilterConditions($user, 'created', $table_name, $conditions);
      $this->prepareDateFilterConditions($user, 'due', $table_name, $conditions);
      $this->prepareDateFilterConditions($user, 'completed', $table_name, $conditions);

      // Completed by filter
      switch($this->getCompletedByFilter()) {
        case self::USER_FILTER_ANYBODY:
          break;

        // Logged user
        case self::USER_FILTER_LOGGED_USER:
          $conditions[] = DB::prepare("($table_name.completed_by_id = ?)", $user->getId()); break;

        // All members of a specific company
        case self::USER_FILTER_COMPANY:
          $company_id = $this->getCompletedByCompanyId();

          if($company_id) {
            $company = Companies::findById($company_id);

            if($company instanceof Company) {
              $visible_user_ids = $user->visibleUserIds($company);

              if($visible_user_ids) {
                $conditions[] = DB::prepare("($table_name.completed_by_id IN (?))", $visible_user_ids);
              } else {
                throw new DataFilterConditionsError('completed_by_filter', self::USER_FILTER_COMPANY, $company_id, "User can't see any members of target company");
              } // if
            } else {
              throw new DataFilterConditionsError('completed_by_filter', self::USER_FILTER_COMPANY, null, 'Company not found');
            } // if
          } else {
            throw new DataFilterConditionsError('completed_by_filter', self::USER_FILTER_COMPANY, null, 'Company ID not set');
          } // if

          break;

        // Selected users
        case self::USER_FILTER_SELECTED:
          $user_ids = $this->getCompletedByUsers();

          if($user_ids) {
            $visible_user_ids = $user->visibleUserIds();

            if($visible_user_ids) {
              foreach($user_ids as $k => $v) {
                if(!in_array($v, $visible_user_ids)) {
                  unset($user_ids[$k]);
                } // if
              } // foreach

              if(count($user_ids)) {
                $conditions[] = DB::prepare("($table_name.completed_by_id IN (?))", $user_ids);
              } else {
                throw new DataFilterConditionsError('completed_by_filter', self::USER_FILTER_SELECTED, $user_id, 'Non of the selected users is visible');
              } // if
            } else {
              throw new DataFilterConditionsError('completed_by_filter', self::USER_FILTER_SELECTED, null, "User can't see anyone else");
            } // if
          } else {
            throw new DataFilterConditionsError('completed_by_filter', self::USER_FILTER_SELECTED, null, 'No users selected');
          } // if

          break;
        default:
          throw new InvalidParamError('completed_by_filter', $this->getCompletedByFilter(), 'Unknown completed by filter');
      } // switch

      return implode(' AND ', $conditions);
    } // prepareConditions

    /**
     * Return exclude conditions
     *
     * @param array $exclude
     * @param string $table_name
     * @param string $type_field_name
     * @param string $id_field_name
     * @return string
     */
    function prepareExcludeConditions($exclude, $table_name, $type_field_name = 'type', $id_field_name = 'id') {
      if(is_foreachable($exclude)) {
        $result = array();

        foreach($exclude as $type => $ids) {
          if($type && $ids) {
            $result[] = DB::prepare("($table_name.$type_field_name = ? AND $table_name.$id_field_name IN (?))", $type, $ids);
          } // if
        } // foreach

        return count($result) ? 'NOT (' . implode(' AND ', $result) . ')' : '';
      } else {
        return '';
      } // if
    } // prepareExcludeConditions

    /**
     * @param string $field_name
     * @return bool
     */
    function calculateTimezoneWhenFilteringByDate($field_name) {
      if($field_name == 'completed_on') {
        return true;
      } // if

      return parent::calculateTimezoneWhenFilteringByDate($field_name);
    } // calculateTimezoneWhenFilteringByDate

    /**
     * Go through result entries and make sure that they can be reliable converted to JavaScript maps
     *
     * @param $result
     */
    function resultToMap(&$result) {
      if($result) {
        foreach($result as $k => $v) {
          if($result[$k]['assignments']) {
            foreach($result[$k]['assignments'] as $j => $assignment) {
              if(isset($result[$k]['assignments'][$j]['subtasks'])) {
                $result[$k]['assignments'][$j]['subtasks'] = JSON::valueToMap($result[$k]['assignments'][$j]['subtasks']); // Convert subtasks to map
              } // if
            } // foreach

            $result[$k]['assignments'] = JSON::valueToMap($result[$k]['assignments']); // Convert group assignments to map
          } // if
        } // foreach
      } // if
    } // resultToMap

    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
      $result = parent::describe($user, $detailed, $for_interface);

      // User filter
      $result['user_filter'] = $this->getUserFilter();
      switch($result['user_filter']) {
        case self::USER_FILTER_COMPANY:
        case self::USER_FILTER_COMPANY_RESPONSIBLE:
          $result['company_id'] = (integer) $this->getUserFilterCompanyId();
          break;

        case self::USER_FILTER_SELECTED:
        case self::USER_FILTER_SELECTED_RESPONSIBLE:
          $result['user_ids'] = $this->getUserFilterSelectedUsers();
          break;
      } // switch

      // Created by filter
      $result['created_by_filter'] = $this->getCreatedByFilter();
      switch($result['created_by_filter']) {
        case self::USER_FILTER_COMPANY:
          $result['created_by_company_id'] = $this->getCreatedByCompanyId();
          break;
        case self::USER_FILTER_SELECTED:
          $result['created_by_user_ids'] = $this->getCreatedByUsers();
          break;
      } // switch

      // Delegated by filter
      $result['delegated_by_filter'] = $this->getDelegatedByFilter();
      switch($result['delegated_by_filter']) {
        case self::USER_FILTER_COMPANY:
          $result['delegated_by_company_id'] = $this->getDelegatedByCompanyId();
          break;
        case self::USER_FILTER_SELECTED:
          $result['delegated_by_user_ids'] = $this->getDelegatedByUsers();
          break;
      } // switch

      // Label filter
      $result['label_filter'] = $this->getLabelFilter();
      if($result['label_filter'] == self::LABEL_FILTER_SELECTED || $result['label_filter'] == self::LABEL_FILTER_NOT_SELECTED) {
        $result['label_names'] = $this->getLabelNames();
      } // if

      // Category filter
      $result['category_filter'] = $this->getCategoryFilter();
      if($result['category_filter'] == self::CATEGORY_FILTER_SELECTED) {
        $result['category_names'] = $this->getCategoryNames();
      } // if

      // Milestone filter
      $result['milestone_filter'] = $this->getMilestoneFilter();
      if($result['milestone_filter'] == self::MILESTONE_FILTER_SELECTED) {
        $result['milestone_names'] = $this->getMilestoneNames();
      } // if

      $this->describeDateFilter('created', $result);
      $this->describeDateFilter('due', $result);
      $this->describeDateFilter('completed', $result);

      // Delegated by filter
      $result['completed_by_filter'] = $this->getCompletedByFilter();
      switch($result['completed_by_filter']) {
        case self::USER_FILTER_COMPANY:
          $result['completed_by_company_id'] = $this->getCompletedByCompanyId();
          break;
        case self::USER_FILTER_SELECTED:
          $result['completed_by_user_ids'] = $this->getCompletedByUsers();
          break;
      } // switch

      // Project filter
      $result['project_filter'] = $this->getProjectFilter();
      switch($this->getProjectFilter()) {
        case Projects::PROJECT_FILTER_CATEGORY:
          $result['project_category_id'] = $this->getProjectCategoryId();
          break;
        case Projects::PROJECT_FILTER_CLIENT:
          $result['project_client_id'] = $this->getProjectClientId();
          break;
        case Projects::PROJECT_FILTER_SELECTED:
          $result['project_ids'] = $this->getProjectIds();
          break;
      } // switch

      $result['group_by'] = $this->getGroupBy();
      $result['additional_column_1'] = $this->getAdditionalColumn1();
      $result['additional_column_2'] = $this->getAdditionalColumn2();
      $result['include_all_projects'] = (boolean) $this->getIncludeAllProjects();
      $result['include_tracking_data'] = (boolean) $this->getIncludeTrackingData();
      $result['include_subtasks'] = (boolean) $this->getIncludeSubtasks();
      $result['include_other_assignees'] = (boolean) $this->getIncludeOtherAssignees();
      $result['show_stats'] = (boolean) $this->getShowStats();
      $result['is_private'] = (boolean) $this->getIsPrivate();

      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param bool $detailed
     * @return array|void
     * @throws NotImplementedError
     */
    function describeForApi(IUser $user, $detailed = false) {
      throw new NotImplementedError(__METHOD__);
    } // describeForApi

    // ---------------------------------------------------
    //  Getters and Setters
    // ---------------------------------------------------

    /**
     * Set attributes
     *
     * @param array $attributes
     */
    function setAttributes($attributes) {
      if(isset($attributes['user_filter'])) {
        if($attributes['user_filter'] == self::USER_FILTER_COMPANY || $attributes['user_filter'] == self::USER_FILTER_COMPANY_RESPONSIBLE) {
          $this->filterByCompany(array_var($attributes, 'company_id'), ($attributes['user_filter'] == self::USER_FILTER_COMPANY_RESPONSIBLE));
        } elseif($attributes['user_filter'] == self::USER_FILTER_SELECTED || $attributes['user_filter'] == self::USER_FILTER_SELECTED_RESPONSIBLE) {
          $this->filterByUsers(array_var($attributes, 'user_ids'), ($attributes['user_filter'] == self::USER_FILTER_SELECTED_RESPONSIBLE));
        } else {
          $this->setUserFilter($attributes['user_filter']);
        } // if
      } // if

      if(isset($attributes['created_by_filter'])) {
        if($attributes['created_by_filter'] == self::USER_FILTER_COMPANY) {
          $this->filterCreatedByCompany(array_var($attributes, 'created_by_company_id'));
        } elseif($attributes['created_by_filter'] == self::USER_FILTER_SELECTED) {
          $this->filterCreatedByUsers(array_var($attributes, 'created_by_user_ids'));
        } else {
          $this->setCreatedByFilter($attributes['created_by_filter']);
        } // if
      } // if

      if(isset($attributes['delegated_by_filter'])) {
        if($attributes['delegated_by_filter'] == self::USER_FILTER_COMPANY) {
          $this->filterDelegatedByCompany(array_var($attributes, 'delegated_by_company_id'));
        } elseif($attributes['delegated_by_filter'] == self::USER_FILTER_SELECTED) {
          $this->filterDelegatedByUsers(array_var($attributes, 'delegated_by_user_ids'));
        } else {
          $this->setDelegatedByFilter($attributes['delegated_by_filter']);
        } // if
      } // if

      if(isset($attributes['label_filter'])) {
        if($attributes['label_filter'] == self::LABEL_FILTER_SELECTED || $attributes['label_filter'] == self::LABEL_FILTER_NOT_SELECTED) {
          $this->filterByLabelNames(array_var($attributes, 'label_names'), $attributes['label_filter'] == self::LABEL_FILTER_NOT_SELECTED);
        } else {
          $this->setLabelFilter($attributes['label_filter']);
        } // if
      } // if

      if(isset($attributes['category_filter'])) {
        if($attributes['category_filter'] == self::CATEGORY_FILTER_SELECTED) {
          $this->filterByCategoryNames(array_var($attributes, 'category_names'));
        } else {
          $this->setCategoryFilter($attributes['category_filter']);
        } // if
      } // if

      if(isset($attributes['milestone_filter'])) {
        if($attributes['milestone_filter'] == self::MILESTONE_FILTER_SELECTED) {
          $this->filterByMilestoneNames(array_var($attributes, 'milestone_names'));
        } else {
          $this->setMilestoneFilter($attributes['milestone_filter']);
        } // if
      } // if

      $this->setDateFilterAttributes('created', $attributes);
      $this->setDateFilterAttributes('due', $attributes);
      $this->setDateFilterAttributes('completed', $attributes);

      if(isset($attributes['completed_by_filter'])) {
        if($attributes['completed_by_filter'] == self::USER_FILTER_COMPANY) {
          $this->filterCompletedByCompany(array_var($attributes, 'completed_by_company_id'));
        } elseif($attributes['completed_by_filter'] == self::USER_FILTER_SELECTED) {
          $this->filterCompletedByUsers(array_var($attributes, 'completed_by_user_ids'));
        } else {
          $this->setCompletedByFilter($attributes['completed_by_filter']);
        } // if
      } // if

      if(isset($attributes['project_filter'])) {
        if($attributes['project_filter'] == Projects::PROJECT_FILTER_CATEGORY) {
          $this->filterByProjectCategory(array_var($attributes, 'project_category_id'));
        } elseif($attributes['project_filter'] == Projects::PROJECT_FILTER_CLIENT) {
          $this->filterByProjectClient(array_var($attributes, 'project_client_id'));
        } elseif($attributes['project_filter'] == Projects::PROJECT_FILTER_SELECTED) {
          $this->filterByProjects(array_var($attributes, 'project_ids'));
        } else {
          $this->setProjectFilter($attributes['project_filter']);
        } // if
      } // if

      if(isset($attributes['group_by'])) {
        $this->setGroupBy($attributes['group_by']);
      } // if

      if(isset($attributes['additional_column_1'])) {
        $this->setAdditionalColumn1($attributes['additional_column_1']);
      } // if

      if(isset($attributes['additional_column_2'])) {
        $this->setAdditionalColumn2($attributes['additional_column_2']);
      } // if
      
      if(isset($attributes['today_offset'])) {
        $this->setTodayOffset($attributes['today_offset']);
      } // if

      $this->setIsPrivate(isset($attributes['is_private']) && $attributes['is_private']);
      $this->setIncludeAllProjects(isset($attributes['include_all_projects']) && $attributes['include_all_projects']);
      $this->setIncludeTrackingData(isset($attributes['include_tracking_data']) && $attributes['include_tracking_data']);
      $this->setIncludeSubtasks(isset($attributes['include_subtasks']) && $attributes['include_subtasks']);
      $this->setIncludeOtherAssignees(isset($attributes['include_other_assignees']) && $attributes['include_other_assignees']);
      $this->setShowStats(isset($attributes['show_stats']) && $attributes['show_stats']);

      parent::setAttributes($attributes);
    } // setAttributes

    /**
     * Return user filter value
     *
     * @return string
     */
    function getUserFilter() {
      return $this->getAdditionalProperty('user_filter', self::USER_FILTER_ANYBODY);
    } // getUserFilter

    /**
     * Set user filter value
     *
     * @param string $value
     * @return string
     */
    function setUserFilter($value) {
      return $this->setAdditionalProperty('user_filter', $value);
    } // setUserFilter

    /**
     * Set filter by company values
     *
     * @param integer $company_id
     * @param bool $responsible_only
     */
    function filterByCompany($company_id, $responsible_only = false) {
      if($responsible_only) {
        $this->setUserFilter(self::USER_FILTER_COMPANY_RESPONSIBLE);
      } else {
        $this->setUserFilter(self::USER_FILTER_COMPANY);
      } // if

      $this->setAdditionalProperty('company_id', $company_id);
    } // filterByCompany

    /**
     * Return company ID set for user filter
     *
     * @return integer
     */
    function getUserFilterCompanyId() {
      return $this->getAdditionalProperty('company_id');
    } // getUserFilterCompanyId

    /**
     * Set user filter to filter only tracked object for selected users
     *
     * @param array $users
     * @param boolean $responsible_only
     */
    function filterByUsers($users, $responsible_only = false) {
      if($responsible_only) {
        $this->setUserFilter(self::USER_FILTER_SELECTED_RESPONSIBLE);
      } else {
        $this->setUserFilter(self::USER_FILTER_SELECTED);
      } // if

      if(is_array($users)) {
        $user_ids = array();

        foreach($users as $k => $v) {
          $user_ids[$k] = $v instanceof User ? $v->getId() : (integer) $v;
        } // foreach
      } else {
        $user_ids = null;
      } // if

      $this->setAdditionalProperty('selected_users', $user_ids);
    } // filterByUsers

    /**
     * Return array of selected users
     *
     * @return array
     */
    function getUserFilterSelectedUsers() {
      return $this->getAdditionalProperty('selected_users');
    } // getUserFilterSelectedUsers

    /**
     * Return created by filter value
     *
     * @return string
     */
    function getCreatedByFilter() {
      return $this->getAdditionalProperty('created_by_filter', self::USER_FILTER_ANYBODY);
    } // getCreatedByFilter

    /**
     * Set created by filter value
     *
     * @param string $value
     * @return string
     */
    function setCreatedByFilter($value) {
      return $this->setAdditionalProperty('created_by_filter', $value);
    } // setCreatedByFilter

    /**
     * Set filter by company values
     *
     * @param integer $company_id
     */
    function filterCreatedByCompany($company_id) {
      $this->setCreatedByFilter(self::USER_FILTER_COMPANY);

      $this->setAdditionalProperty('created_by_company_id', $company_id);
    } // filterCreatedByCompany

    /**
     * Return company ID set for user filter
     *
     * @return integer
     */
    function getCreatedByCompanyId() {
      return $this->getAdditionalProperty('created_by_company_id');
    } // getCreatedByCompanyId

    /**
     * Set user filter to filter only tracked object for selected users
     *
     * $user_ids can be an array of user ID-s or a single user ID or NULL
     *
     * @param array $user_ids
     */
    function filterCreatedByUsers($user_ids) {
      $this->setCreatedByFilter(self::USER_FILTER_SELECTED);

      if(is_array($user_ids)) {
        foreach($user_ids as $k => $v) {
          $user_ids[$k] = (integer) $v;
        } // foreach
      } else if($user_ids) {
        $user_ids = array($user_ids);
      } else {
        $user_ids = null;
      } // if

      $this->setAdditionalProperty('created_by_users', $user_ids);
    } // filterCreatedByUsers

    /**
     * Return array of selected users
     *
     * @return array
     */
    function getCreatedByUsers() {
      return $this->getAdditionalProperty('created_by_users');
    } // getUserFilterSelectedUsers

    /**
     * Return delegated by filter value
     *
     * @return string
     */
    function getDelegatedByFilter() {
      return $this->getAdditionalProperty('delegated_by_filter', self::USER_FILTER_ANYBODY);
    } // getDelegatedByFilter

    /**
     * Set delegated by filter
     *
     * @param string $value
     * @return string
     */
    function setDelegatedByFilter($value) {
      return $this->setAdditionalProperty('delegated_by_filter', $value);
    } // setDelegatedByFilter

    /**
     * Set delegated by company member filter
     *
     * @param integer $company_id
     */
    function filterDelegatedByCompany($company_id) {
      $this->setDelegatedByFilter(self::USER_FILTER_COMPANY);

      $this->setAdditionalProperty('delegated_by_company_id', $company_id);
    } // filterDelegatedByCompany

    /**
     * Return company ID set for delegated by filter
     *
     * @return integer
     */
    function getDelegatedByCompanyId() {
      return $this->getAdditionalProperty('delegated_by_company_id');
    } // getDelegatedByCompanyId

    /**
     * Set delegated by fileter to the list of users
     *
     * $user_ids can be an array of user ID-s or a single user ID or NULL
     *
     * @param array $user_ids
     */
    function filterDelegatedByUsers($user_ids) {
      $this->setDelegatedByFilter(self::USER_FILTER_SELECTED);

      if(is_array($user_ids)) {
        foreach($user_ids as $k => $v) {
          $user_ids[$k] = (integer) $v;
        } // foreach
      } else if($user_ids) {
        $user_ids = array($user_ids);
      } else {
        $user_ids = null;
      } // if

      $this->setAdditionalProperty('delegated_by_users', $user_ids);
    } // filterDelegatedByUsers

    /**
     * Return array of selected users
     *
     * @return array
     */
    function getDelegatedByUsers() {
      return $this->getAdditionalProperty('delegated_by_users');
    } // getDelegatedByUsers

    /**
     * Return label filter
     *
     * @return string
     */
    function getLabelFilter() {
      return $this->getAdditionalProperty('label_filter', self::LABEL_FILTER_ANY);
    } // getLabelFilter

    /**
     * Set label filter
     *
     * @param string $value
     * @return string
     */
    function setLabelFilter($value) {
      return $this->setAdditionalProperty('label_filter', $value);
    } // setLabelFilter

    /**
     * Filter assignment by given list of labels
     *
     * @param array $label_names
     * @param boolean $invert
     * @return array
     */
    function filterByLabelNames($label_names, $invert = false) {
      if($invert) {
        $this->setLabelFilter(self::LABEL_FILTER_NOT_SELECTED);
      } else {
        $this->setLabelFilter(self::LABEL_FILTER_SELECTED);
      } // if

      $this->setAdditionalProperty('label_names', $label_names);
    } // filterByLabelNames

    /**
     * Return label names
     *
     * @return string
     */
    function getLabelNames() {
      return $this->getAdditionalProperty('label_names');
    } // getLabelNames

    /**
     * Return category filter
     *
     * @return string
     */
    function getCategoryFilter() {
      return $this->getAdditionalProperty('category_filter', self::LABEL_FILTER_ANY);
    } // getCategoryFilter

    /**
     * Set category filter
     *
     * @param string $value
     * @return string
     */
    function setCategoryFilter($value) {
      return $this->setAdditionalProperty('category_filter', $value);
    } // setCategoryFilter

    /**
     * Filter assignment by given list of labels
     *
     * @param array $category_names
     * @return array
     */
    function filterByCategoryNames($category_names) {
      $this->setCategoryFilter(self::CATEGORY_FILTER_SELECTED);
      $this->setAdditionalProperty('category_names', $category_names);
    } // filterByCategoryNames

    /**
     * Return label names
     *
     * @return string
     */
    function getCategoryNames() {
      return $this->getAdditionalProperty('category_names');
    } // getCategoryNames

    /**
     * Return category filter
     *
     * @return string
     */
    function getMilestoneFilter() {
      return $this->getAdditionalProperty('milestone_filter', self::LABEL_FILTER_ANY);
    } // getMilestoneFilter

    /**
     * Set category filter
     *
     * @param string $value
     * @return string
     */
    function setMilestoneFilter($value) {
      return $this->setAdditionalProperty('milestone_filter', $value);
    } // setMilestoneFilter

    /**
     * Filter assignment by given list of milestones
     *
     * @param array $milestone_names
     * @return array
     */
    function filterByMilestoneNames($milestone_names) {
      $this->setMilestoneFilter(self::MILESTONE_FILTER_SELECTED);
      $this->setAdditionalProperty('milestone_names', $milestone_names);
    } // filterByMilestoneNames

    /**
     * Return label names
     *
     * @return string
     */
    function getMilestoneNames() {
      return $this->getAdditionalProperty('milestone_names');
    } // getMilestoneNames

    /**
     * Return created on filter value
     *
     * @return string
     */
    function getCreatedOnFilter() {
      return $this->getAdditionalProperty('created_on_filter', self::DATE_FILTER_ANY);
    } // getCreatedOnFilter

    /**
     * Set created on filter to a given $value
     *
     * @param string $value
     * @return string
     */
    function setCreatedOnFilter($value) {
      return $this->setAdditionalProperty('created_on_filter', $value);
    } // setCreatedOnFilter

    /**
     * @return int
     */
    function getCreatedAge() {
      return (integer) $this->getAdditionalProperty('created_age');
    } // getCreatedOnAge

    /**
     * Set created on age
     *
     * @param integer $value
     * @param string $filter
     * @return string
     * @throws InvalidParamError
     */
    function createdAge($value, $filter = DataFilter::DATE_FILTER_AGE_IS) {
      if($filter == DataFilter::DATE_FILTER_AGE_IS || DataFilter::DATE_FILTER_AGE_IS_LESS_THAN || $filter == DataFilter::DATE_FILTER_AGE_IS_MORE_THAN) {
        $this->setCreatedOnFilter($filter);
      } else {
        throw new InvalidParamError('filter', $filter);
      } // if

      return $this->setAdditionalProperty('created_age', (integer) $value);
    } // createdAge

    /**
     * Filter objects created on a given date
     *
     * @param string $date
     */
    function createdOnDate($date) {
      $this->setCreatedOnFilter(self::DATE_FILTER_SELECTED_DATE);
      $this->setAdditionalProperty('created_on_filter_on', (string) $date);
    } // createdOnDate

    /**
     * Filter objects created on a given date
     *
     * @param string $date
     */
    function createdBeforeDate($date) {
      $this->setCreatedOnFilter(self::DATE_FILTER_BEFORE_SELECTED_DATE);
      $this->setAdditionalProperty('created_on_filter_on', (string) $date);
    } // createdBeforeDate

    /**
     * Filter objects created on a given date
     *
     * @param string $date
     */
    function createdAfterDate($date) {
      $this->setCreatedOnFilter(self::DATE_FILTER_AFTER_SELECTED_DATE);
      $this->setAdditionalProperty('created_on_filter_on', (string) $date);
    } // createdAfterDate

    /**
     * Return selected date for created on filter
     *
     * @return DateValue
     */
    function getCreatedOnDate() {
      $on = $this->getAdditionalProperty('created_on_filter_on');

      return $on ? new DateValue($on) : null;
    } // getCreatedOnDate

    /**
     * Filter assignments created in a given range
     *
     * @param string $from
     * @param string $to
     */
    function createdInRange($from, $to) {
      $this->setCreatedOnFilter(self::DATE_FILTER_SELECTED_RANGE);
      $this->setAdditionalProperty('created_on_filter_from', (string) $from);
      $this->setAdditionalProperty('created_on_filter_to', (string) $to);
    } // createdInRange

    /**
     * Return created on filter range
     *
     * @return array
     */
    function getCreatedInRange() {
      $from = $this->getAdditionalProperty('created_on_filter_from');
      $to = $this->getAdditionalProperty('created_on_filter_to');

      return $from && $to ? array(new DateValue($from), new DateValue($to)) : array(null, null);
    } // getCreatedInRange

    /**
     * Return due date filter value
     *
     * @return string
     */
    function getDueOnFilter() {
      return $this->getAdditionalProperty('due_on_filter', self::DATE_FILTER_ANY);
    } // getDueOnFilter

    /**
     * Set due date filter value
     *
     * @param string $value
     * @return string
     */
    function setDueOnFilter($value) {
      return $this->setAdditionalProperty('due_on_filter', $value);
    } // setDueOnFilter

    /**
     * Filter assignents that are due on a given date
     *
     * @param string $date
     */
    function dueOnDate($date) {
      $this->setDueOnFilter(self::DATE_FILTER_SELECTED_DATE);
      $this->setAdditionalProperty('due_on_filter_on', (string) $date);
    } // dueOnDate

    /**
     * Filter assignents that are due on a given date
     *
     * @param string $date
     */
    function dueBeforeDate($date) {
      $this->setDueOnFilter(self::DATE_FILTER_BEFORE_SELECTED_DATE);
      $this->setAdditionalProperty('due_on_filter_on', (string) $date);
    } // dueBeforeDate

    /**
     * Filter assignents that are due on a given date
     *
     * @param string $date
     */
    function dueAfterDate($date) {
      $this->setDueOnFilter(self::DATE_FILTER_AFTER_SELECTED_DATE);
      $this->setAdditionalProperty('due_on_filter_on', (string) $date);
    } // dueAfterDate

    /**
     * Return due on filter value
     *
     * @return DateValue
     */
    function getDueOnDate() {
      $on = $this->getAdditionalProperty('due_on_filter_on');

      return $on ? new DateValue($on) : null;
    } // getDueOnDate

    /**
     * Return assignments that are due in a given range
     *
     * @param string $from
     * @param string $to
     */
    function dueInRange($from, $to) {
      $this->setDueOnFilter(self::DATE_FILTER_SELECTED_RANGE);
      $this->setAdditionalProperty('due_on_filter_from', (string) $from);
      $this->setAdditionalProperty('due_on_filter_to', (string) $to);
    } // dueInRange

    /**
     * Return due on filter range
     *
     * @return array
     */
    function getDueInRange() {
      $from = $this->getAdditionalProperty('due_on_filter_from');
      $to = $this->getAdditionalProperty('due_on_filter_to');

      return $from && $to ? array(new DateValue($from), new DateValue($to)) : array(null, null);
    } // getDueInRange

    /**
     * Return delegated by filter value
     *
     * @return string
     */
    function getCompletedByFilter() {
      return $this->getAdditionalProperty('completed_by_filter', self::USER_FILTER_ANYBODY);
    } // getCompletedByFilter

    /**
     * Set delegated by filter
     *
     * @param string $value
     * @return string
     */
    function setCompletedByFilter($value) {
      return $this->setAdditionalProperty('completed_by_filter', $value);
    } // setCompletedByFilter

    /**
     * Set delegated by company member filter
     *
     * @param integer $company_id
     */
    function filterCompletedByCompany($company_id) {
      $this->setCompletedByFilter(self::USER_FILTER_COMPANY);

      $this->setAdditionalProperty('completed_by_company_id', $company_id);
    } // filterCompletedByCompany

    /**
     * Return company ID set for delegated by filter
     *
     * @return integer
     */
    function getCompletedByCompanyId() {
      return $this->getAdditionalProperty('completed_by_company_id');
    } // getCompletedByCompanyId

    /**
     * Set delegated by fileter to the list of users
     *
     * $user_ids can be an array of user ID-s or a single user ID or NULL
     *
     * @param array $user_ids
     */
    function filterCompletedByUsers($user_ids) {
      $this->setCompletedByFilter(self::USER_FILTER_SELECTED);

      if(is_array($user_ids)) {
        foreach($user_ids as $k => $v) {
          $user_ids[$k] = (integer) $v;
        } // foreach
      } else if($user_ids) {
        $user_ids = array($user_ids);
      } else {
        $user_ids = null;
      } // if

      $this->setAdditionalProperty('completed_by_users', $user_ids);
    } // filterCompletedByUsers

    /**
     * Return array of selected users
     *
     * @return array
     */
    function getCompletedByUsers() {
      return $this->getAdditionalProperty('completed_by_users');
    } // getCompletedByUsers

    /**
     * Return completed on filter value
     *
     * @return string
     */
    function getCompletedOnFilter() {
      return $this->getAdditionalProperty('completed_on_filter', self::DATE_FILTER_ANY);
    } // getCompletedOnFilter

    /**
     * Set completed on filter value
     *
     * @param string $value
     * @return string
     */
    function setCompletedOnFilter($value) {
      return $this->setAdditionalProperty('completed_on_filter', $value);
    } // setCompletedOnFilter

    /**
     * Filter assignments that are completed on a given date
     *
     * @param string $date
     */
    function completedOnDate($date) {
      $this->setCompletedOnFilter(self::DATE_FILTER_SELECTED_DATE);
      $this->setAdditionalProperty('completed_filter_on', (string) $date);
    } // completedOnDate

    /**
     * Completed before a given date (not including that date)
     *
     * @param string $date
     */
    function completedBeforeDate($date) {
      $this->setCompletedOnFilter(self::DATE_FILTER_BEFORE_SELECTED_DATE);
      $this->setAdditionalProperty('completed_filter_on', (string) $date);
    } // completedBeforeDate

    /**
     * Completed after a given date (not including that date)
     *
     * @param string $date
     */
    function completedAfterDate($date) {
      $this->setCompletedOnFilter(self::DATE_FILTER_AFTER_SELECTED_DATE);
      $this->setAdditionalProperty('completed_filter_on', (string) $date);
    } // completedAfterDate

    /**
     * Return completed on filter value
     *
     * @return DateValue
     */
    function getCompletedOnDate() {
      $on = $this->getAdditionalProperty('completed_filter_on');

      return $on ? new DateValue($on) : null;
    } // getCompletedOnDate

    /**
     * Return assignments filter on a given range
     *
     * @param string $from
     * @param string $to
     */
    function completedInRange($from, $to) {
      $this->setCompletedOnFilter(self::DATE_FILTER_SELECTED_RANGE);
      $this->setAdditionalProperty('completed_on_filter_from', (string) $from);
      $this->setAdditionalProperty('completed_on_filter_to', (string) $to);
    } // completedInRange

    /**
     * Return value of completed filter
     *
     * @return array
     */
    function getCompletedInRange() {
      $from = $this->getAdditionalProperty('completed_on_filter_from');
      $to = $this->getAdditionalProperty('completed_on_filter_to');

      return $from && $to ? array(new DateValue($from), new DateValue($to)) : array(null, null);
    } // getCompletedInRange

    /**
     * Return project filter value
     *
     * @return string
     */
    function getProjectFilter() {
      return $this->getAdditionalProperty('project_filter', Projects::PROJECT_FILTER_ANY);
    } // getProjectFilter

    /**
     * Set project filter value
     *
     * @param string $value
     * @return string
     */
    function setProjectFilter($value) {
      return $this->setAdditionalProperty('project_filter', $value);
    } // setProjectFilter

    /**
     * Set filter to filter records by project category
     *
     * @param integer $project_category_id
     * @return integer
     */
    function filterByProjectCategory($project_category_id) {
      $this->setProjectFilter(Projects::PROJECT_FILTER_CATEGORY);
      $this->setAdditionalProperty('project_category_id', (integer) $project_category_id);
    } // filterByProjectCategory

    /**
     * Return project category ID
     *
     * @return integer
     */
    function getProjectCategoryId() {
      return (integer) $this->getAdditionalProperty('project_category_id');
    } // getProjectCategoryId

    /**
     * Set filter to filter records by project client
     *
     * @param integer $project_client_id
     * @return integer
     */
    function filterByProjectClient($project_client_id) {
      $this->setProjectFilter(Projects::PROJECT_FILTER_CLIENT);
      if($project_client_id instanceof Company) {
        $this->setAdditionalProperty('project_client_id', $project_client_id->getId());
      } else {
        $this->setAdditionalProperty('project_client_id', (integer) $project_client_id);
      } // if
    } // filterByProjectClient

    /**
     * Return project client ID
     *
     * @return integer
     */
    function getProjectClientId() {
      return (integer) $this->getAdditionalProperty('project_client_id');
    } // getProjectClientId

    /**
     * Set this report to filter records by project ID-s
     *
     * @param array $project_ids
     * @return array
     */
    function filterByProjects($project_ids) {
      $this->setProjectFilter(Projects::PROJECT_FILTER_SELECTED);

      if(is_array($project_ids)) {
        foreach($project_ids as $k => $v) {
          $project_ids[$k] = (integer) $v;
        } // foreach
      } else {
        $project_ids = null;
      } // if

      $this->setAdditionalProperty('project_ids', $project_ids);
    } // filterByProjects

    /**
     * Return project ID-s
     *
     * @return array
     */
    function getProjectIds() {
      return $this->getAdditionalProperty('project_ids');
    } // getProjectIds

    /**
     * Return group by setting
     *
     * @return string
     */
    function getGroupBy() {
      return $this->getAdditionalProperty('group_by', self::DONT_GROUP);
    } // getGroupBy

    /**
     * Set group by value
     *
     * @param string $value
     * @return string
     */
    function setGroupBy($value) {
      return $this->setAdditionalProperty('group_by', $value);
    } // setGroupBy

    /**
     * Returns true if this filter returns grouped records
     *
     * @return boolean
     */
    function isGrouped() {
      return $this->getGroupBy() != self::DONT_GROUP;
    } // isGrouped
    
    /**
     * Return today value
     *
     * @return integer
     */
    function getTodayOffset() {
      return $this->getAdditionalProperty('today_offset', time());
    } // getTodayOffset
    
    /**
     * Set today to a given $value
     *
     * @param string $value
     * @return integer
     */
    function setTodayOffset($value) {
      return $this->setAdditionalProperty('today_offset', $value);
    } // setTodayOffset

    /**
     * Return first additional column, if set
     *
     * @return string
     */
    function getAdditionalColumn1() {
      return $this->getAdditionalProperty('additional_column_1');
    } // getAdditionalColumn1

    /**
     * Set first additional column (it can be NULL)
     *
     * @param string $value
     * @return string
     */
    function setAdditionalColumn1($value) {
      return $this->setAdditionalProperty('additional_column_1', $value);
    } // setAdditionalColumn1

    /**
     * Return first additional column, if set
     *
     * @return string
     */
    function getAdditionalColumn2() {
      return $this->getAdditionalProperty('additional_column_2');
    } // getAdditionalColumn2

    /**
     * Set first additional column (it can be NULL)
     *
     * @param string $value
     * @return string
     */
    function setAdditionalColumn2($value) {
      return $this->setAdditionalProperty('additional_column_2', $value);
    } // setAdditionalColumn2

    /**
     * Return true if system should search all project (admins and PM)
     *
     * @return boolean
     */
    function getIncludeAllProjects() {
      return $this->getAdditionalProperty('include_all_projects', false);
    } // getIncludeAllProjects

    /**
     * Set whether system should include all projects (admins and PM)
     *
     * @param boolean $value
     * @return boolean
     */
    function setIncludeAllProjects($value) {
      return $this->setAdditionalProperty('include_all_projects', (boolean) $value);
    } // setIncludeAllProjects

    /**
     * Returns true if this filter also matches subtasks
     *
     * @return boolean
     */
    function getIncludeSubtasks() {
      return $this->getAdditionalProperty('include_subtasks', true);
    } // getIncludeSubtasks

    /**
     * Set include subtasks flag
     *
     * @param boolean $value
     * @return boolean
     */
    function setIncludeSubtasks($value) {
      return $this->setAdditionalProperty('include_subtasks', (boolean) $value);
    } // setIncludeSubtasks

    /**
     * Returns true if this filter also needs to return tracking data
     *
     * @return boolean
     */
    function getIncludeTrackingData() {
      return $this->getAdditionalProperty('include_tracking_data', false);
    } // getIncludeTrackingData

    /**
     * Set include tracking data flag
     *
     * @param boolean $value
     * @return boolean
     */
    function setIncludeTrackingData($value) {
      return $this->setAdditionalProperty('include_tracking_data', (boolean) $value);
    } // setIncludeTrackingData
    
    /**
     * Returns true if this filter also needs to return other assignees
     *
     * @return boolean
     */
    function getIncludeOtherAssignees() {
      return ProjectObjects::isMultipleAssigneesSupportEnabled() && $this->getAdditionalProperty('include_other_assignees', false);
    } // getIncludeOtherAssignees

    /**
     * Set include other assignees flag
     *
     * @param boolean $value
     * @return boolean
     */
    function setIncludeOtherAssignees($value) {
      return $this->setAdditionalProperty('include_other_assignees', (boolean) $value);
    } // setIncludeOtherAssignees

    /**
     * Return show stats flag
     *
     * @return boolean
     */
    function getShowStats() {
      return $this->getAdditionalProperty('show_stats', false);
    } // getShowStats

    /**
     * Set show stats flag
     *
     * @param boolean $value
     * @return boolean
     */
    function setShowStats($value) {
      return $this->setAdditionalProperty('show_stats', (boolean) $value);
    } // setShowStats

    /**
     * Return true if favorite projects should be on top when results are grouped by project
     *
     * @return boolean
     */
    function getFavoriteOnTopWhenGroupingByProject() {
      return (boolean) $this->getAdditionalProperty('favorite_projects_on_top');
    } // getFavoriteOnTopWhenGroupingByProject

    /**
     * Set whether favorite projects should be on top when results are group by project
     *
     * @param boolean $value
     * @return boolean
     */
    function setFavoriteOnTopWhenGroupingByProject($value) {
      return $this->setAdditionalProperty('favorite_projects_on_top', (boolean) $value);
    } // setFavoriteOnTopWhenGroupingByProject

    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------

    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'assignment_filter';
    } // getRoutingContext

    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('assignment_filter_id' => $this->getId());
    } // getRoutingContextParams

  }