<?php

  /**
   * Subtasks class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class Subtasks extends FwSubtasks {

    /**
     * Find subtasks for outline
     *
     * @param ISubtasks $parent
     * @param IUser $user
     * @return array
     */
    function findForOutline(ISubtasks $parent, IUser $user) {
      $parent_id = $parent->getId();
      $parent_type = $parent->getType();
      $can_edit_parent = $parent->canEdit($user);
      $subtask_class = 'ProjectObjectSubtask';

      $subtask_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'subtasks WHERE parent_id = ? AND parent_type = ? AND completed_on IS NULL AND state >= ?', $parent_id, $parent_type, STATE_VISIBLE);

      if (!is_foreachable($subtask_ids)) {
        return false;
      } // if

      $subtasks = DB::execute('SELECT id, body, due_on, assignee_id, label_id, priority FROM ' . TABLE_PREFIX . 'subtasks WHERE ID IN(?) ORDER BY ISNULL(position) ASC, position, priority DESC, created_on', $subtask_ids);

      // casting
      $subtasks->setCasting(array(
        'due_on'        => DBResult::CAST_DATE,
        'start_on'      => DBResult::CAST_DATE
      ));

      $subtasks_id_prefix_pattern = '--SUBTASK-ID--';
      $routing_context = $parent->getRoutingContext();
      $routing_params = array_merge($parent->getRoutingContextParams(), array('subtask_id' => $subtasks_id_prefix_pattern));
      $view_subtask_url_pattern = Router::assemble($routing_context . '_subtask', $routing_params);
      $edit_subtask_url_pattern = Router::assemble($routing_context . '_subtask_edit', $routing_params);
      $trash_subtask_url_pattern = Router::assemble($routing_context . '_subtask_trash', $routing_params);
      $subscribe_subtask_url_pattern = Router::assemble($routing_context . '_subtask_subscribe', $routing_params);
      $unsubscribe_subtask_url_pattern = Router::assemble($routing_context . '_subtask_unsubscribe', $routing_params);
      $reschedule_subtask_url_pattern = Router::assemble($routing_context . '_subtask_reschedule', $routing_params);
      $complete_subtask_url_pattern = Router::assemble($routing_context . '_subtask_complete', $routing_params);

      // all subscriptions
      $user_subscriptions_on_tasks = DB::executeFirstColumn('SELECT parent_id FROM ' . TABLE_PREFIX . 'subscriptions WHERE parent_id IN (?) AND parent_type = ? AND user_id = ?', $subtask_ids, $subtask_class, $user->getId());

      $results = array();
      foreach ($subtasks as $subobject) {
        $subtask_id = $subobject['id'];

        $results[] = array(
          'id'                  => $subtask_id,
          'name'                => $subobject['body'],
          'class'               => $subtask_class,
          'priority'            => $subobject['priority'],
          'parent_id'           => $parent_id,
          'parent_class'        => $parent_type,
          'due_on'              => $subobject['due_on'],
          'assignee_id'         => $subobject['assignee_id'],
          'label_id'            => !empty($subobject['label_id']) ? $subobject['label_id'] : null,
          'user_is_subscribed'  => in_array($subtask_id, $user_subscriptions_on_tasks),
          'event_names'         => array(
            'updated'             => 'subtask_updated'
          ),
          'urls'                => array(
            'view'                => str_replace($subtasks_id_prefix_pattern, $subtask_id, $view_subtask_url_pattern),
            'edit'                => str_replace($subtasks_id_prefix_pattern, $subtask_id, $edit_subtask_url_pattern),
            'trash'               => str_replace($subtasks_id_prefix_pattern, $subtask_id, $trash_subtask_url_pattern),
            'subscribe'           => str_replace($subtasks_id_prefix_pattern, $subtask_id, $subscribe_subtask_url_pattern),
            'unsubscribe'         => str_replace($subtasks_id_prefix_pattern, $subtask_id, $unsubscribe_subtask_url_pattern),
            'reschedule'          => str_replace($subtasks_id_prefix_pattern, $subtask_id, $reschedule_subtask_url_pattern),
            'complete'            => str_replace($subtasks_id_prefix_pattern, $subtask_id, $complete_subtask_url_pattern),
          ),
          'permissions'         => array(
            'can_edit'            => $can_edit_parent,
            'can_trash'           => $can_edit_parent,
          )
        );
      } // foreach

      return $results;
    } // findForOutline

    /**
     * Find all project attachments
     *
     * @param Project $project
     * @param integer $min_state
     * @return DBResult
     */
    static function findForApiByProject(Project $project, $min_state = STATE_ARCHIVED) {
      $subtasks_table = TABLE_PREFIX . 'subtasks';

      if($project->getState() >= STATE_VISIBLE) {
        $map = Subtasks::findTypeIdMapOfPotentialParents($project, $min_state);

        if($map) {
          $conditions = array();

          foreach($map as $type => $ids) {
            $conditions[] = DB::prepare('(parent_type = ? AND parent_id IN (?))', $type, $ids);
          } // if

          $conditions = implode(' OR ', $conditions);

          $result = DB::execute("SELECT id, type, parent_type, parent_id, label_id, assignee_id, delegated_by_id, priority, body, due_on, state, created_on, created_by_id, created_by_name, created_by_email, completed_on, completed_by_id, completed_by_name, completed_by_email FROM $subtasks_table WHERE ($conditions) AND state >= ?", $min_state);

          if($result instanceof DBResult) {
            $result->setCasting(array(
              'id' => DBResult::CAST_INT,
              'parent_id' => DBResult::CAST_INT,
              'label_id' => DBResult::CAST_INT,
              'assignee_id' => DBResult::CAST_INT,
              'delegated_by_id' => DBResult::CAST_INT,
              'priority' => DBResult::CAST_INT,
              'state' => DBResult::CAST_INT,
              'created_by_id' => DBResult::CAST_INT,
              'completed_by_id' => DBResult::CAST_INT,
            ));

            $result = $result->toArray();

            if($result) {
              $task_url_pattern = $task_subtask_url_pattern = '';
              $tasks = array();

              if(AngieApplication::isModuleLoaded('tasks')) {
                $task_url_pattern = Router::assemble('project_task', array(
                  'project_slug' => $project->getSlug(),
                  'task_id' => '--TASK-ID--',
                ));

                $task_subtask_url_pattern = Router::assemble('project_task_subtask', array(
                  'project_slug' => $project->getSlug(),
                  'task_id' => '--TASK-ID--',
                  'subtask_id' => '--SUBTASK-ID--',
                ));

                $tasks = array();

                // Cache task data
                $task_rows = DB::execute("SELECT id, integer_field_1 AS 'task_id' FROM " . TABLE_PREFIX . 'project_objects WHERE project_id = ? AND type = ? AND state >= ?', $project->getId(), 'Task', STATE_TRASHED);

                if($task_rows) {
                  foreach($task_rows as $task_row) {
                    $tasks[(integer) $task_row['id']] = (integer) $task_row['task_id'];
                  } // foreach
                } // if
              } // if

              $todo_list_url_pattern = $todo_list_subtask_url_pattern = '';

              if(AngieApplication::isModuleLoaded('todo')) {
                $todo_list_url_pattern = Router::assemble('project_todo_list', array(
                  'project_slug' => $project->getSlug(),
                  'todo_list_id' => '--TODO-LIST-ID--',
                ));

                $todo_list_subtask_url_pattern = Router::assemble('project_todo_list_subtask', array(
                  'project_slug' => $project->getSlug(),
                  'todo_list_id' => '--TODO-LIST-ID--',
                  'subtask_id' => '--SUBTASK-ID--',
                ));
              } // if

              // Prepare parent URL-s and subtask permalinks
              foreach($result as $k => $subtask) {
                $result[$k]['parent_url'] = '';
                $result[$k]['permalink'] = '';

                if($subtask['parent_type'] == 'Task') {
                  $task_id = $subtask['parent_id'];

                  if($tasks[$task_id]) {
                    $result[$k]['parent_url'] = str_replace('--TASK-ID--', $tasks[$task_id], $task_url_pattern);
                    $result[$k]['permalink'] = str_replace(array('--TASK-ID--', '--SUBTASK-ID--'), array($tasks[$task_id], $subtask['id']), $task_subtask_url_pattern);
                  } // if
                } elseif($subtask['parent_type'] == 'TodoList') {
                  $result[$k]['parent_url'] = str_replace('--TODO-LIST-ID--', $subtask['parent_id'], $todo_list_url_pattern);
                  $result[$k]['permalink'] = str_replace(array('--TODO-LIST-ID--', '--SUBTASK-ID--'), array($subtask['parent_id'], $subtask['id']), $todo_list_subtask_url_pattern);
                } // if
              } // foreach
            } // if

            return $result;
          } // if
        } // if
      } // if

      return null;
    } // findForApiByProject

    /**
     * Find all subtasks in project and prepare them for export
     *
     * @param Project $project
     * @param array $parents_map
     * @param integer $changes_since
     * @return array
     */
    static function findForExport(Project $project, $parents_map, $changes_since) {
      $subtasks = array();

      $subtasks_table = TABLE_PREFIX . 'subtasks';

      if(is_foreachable($parents_map)) {
        $conditions = array();

        foreach($parents_map as $type => $ids) {
          $conditions[] = DB::prepare('(parent_type = ? AND parent_id IN (?))', $type, $ids);
        } // if

        $conditions = implode(' OR ', $conditions);

        $additional_condition = '';
        if(!is_null($changes_since)) {
          $changes_since_date = DateTimeValue::makeFromTimestamp($changes_since);
          $additional_condition = "AND created_on > '$changes_since_date'";
        } // if

        $subtasks = DB::execute("SELECT id, type, parent_type, parent_id, label_id, assignee_id, delegated_by_id, priority, body, body AS 'body_formatted', due_on, state, created_on, created_by_id, created_by_name, created_by_email, completed_on, completed_by_id, completed_by_name, completed_by_email FROM $subtasks_table WHERE ($conditions) AND state >= ? $additional_condition", STATE_ARCHIVED);

        if($subtasks instanceof DBResult) {
          $subtasks->setCasting(array(
            'id' => DBResult::CAST_INT,
            'body_formatted' => function($in) {
              return HTML::toRichText($in);
            },
            'parent_id' => DBResult::CAST_INT,
            'label_id' => DBResult::CAST_INT,
            'assignee_id' => DBResult::CAST_INT,
            'delegated_by_id' => DBResult::CAST_INT,
            'priority' => DBResult::CAST_INT,
            'state' => DBResult::CAST_INT,
            'created_by_id' => DBResult::CAST_INT,
            'completed_by_id' => DBResult::CAST_INT
          ));

          $subtasks = $subtasks->toArray();

          if($subtasks) {
            $task_url_pattern = $task_subtask_url_pattern = '';
            $tasks = array();

            if(AngieApplication::isModuleLoaded('tasks')) {
              $task_url_pattern = Router::assemble('project_task', array(
                'project_slug' => $project->getSlug(),
                'task_id' => '--TASK-ID--',
              ));

              $task_subtask_url_pattern = Router::assemble('project_task_subtask', array(
                'project_slug' => $project->getSlug(),
                'task_id' => '--TASK-ID--',
                'subtask_id' => '--SUBTASK-ID--',
              ));

              // Cache task data
              $task_rows = DB::execute("SELECT id, integer_field_1 AS 'task_id' FROM " . TABLE_PREFIX . 'project_objects WHERE project_id = ? AND type = ? AND state >= ?', $project->getId(), 'Task', STATE_ARCHIVED);

              if($task_rows) {
                foreach($task_rows as $task_row) {
                  $tasks[(integer) $task_row['id']] = (integer) $task_row['task_id'];
                } // foreach
              } // if
            } // if

            // Prepare parent URL-s and subtask permalinks
            foreach($subtasks as $k => $subtask) {
              $subtasks[$k]['parent_url'] = '';
              $subtasks[$k]['permalink'] = '';

              if($subtask['parent_type'] == 'Task') {
                $task_id = $subtask['parent_id'];

                if($tasks[$task_id]) {
                  $subtasks[$k]['parent_url'] = str_replace('--TASK-ID--', $tasks[$task_id], $task_url_pattern);
                  $subtasks[$k]['permalink'] = str_replace(array('--TASK-ID--', '--SUBTASK-ID--'), array($tasks[$task_id], $subtask['id']), $task_subtask_url_pattern);
                } // if
              } // if
            } // foreach
          } // if
        } // if
      } // if

      return $subtasks;
    } // findForExport

    /**
     * Find all subtasks in project and prepare them for export
     *
     * @param Project $project
     * @param string $output_file
     * @param array $parents_map
     * @param integer $changes_since
     * @return array
     * @throws Error
     */
    static function exportToFileByProject(Project $project, $output_file, $parents_map, $changes_since) {
      if(!($output_handle = fopen($output_file, 'w+'))) {
        throw new Error(lang('Failed to write JSON file to :file_path', array('file_path' => $output_file)));
      } // if

      // Open json array
      fwrite($output_handle, '[');

      $subtasks_table = TABLE_PREFIX . 'subtasks';

      $count = 0;
      if(is_foreachable($parents_map)) {
        $conditions = array();

        foreach($parents_map as $type => $ids) {
          $conditions[] = DB::prepare('(parent_type = ? AND parent_id IN (?))', $type, $ids);
        } // if

        $conditions = implode(' OR ', $conditions);

        $additional_condition = '';
        if(!is_null($changes_since)) {
          $changes_since_date = DateTimeValue::makeFromTimestamp($changes_since);
          $additional_condition = "AND (created_on > '$changes_since_date' OR updated_on > '$changes_since_date')";
        } // if

        $subtasks = DB::execute("SELECT id, type, parent_type, parent_id, label_id, assignee_id, delegated_by_id, priority, body, body AS 'body_formatted', due_on, state, created_on, created_by_id, created_by_name, created_by_email, completed_on, completed_by_id, completed_by_name, completed_by_email FROM $subtasks_table WHERE ($conditions) AND state >= ? $additional_condition", (boolean) $additional_condition ? STATE_TRASHED : STATE_ARCHIVED);

        if($subtasks instanceof DBResult) {
          $subtasks->setCasting(array(
            'id' => DBResult::CAST_INT,
            'body_formatted' => function($in) {
              return HTML::toRichText($in);
            },
            'parent_id' => DBResult::CAST_INT,
            'label_id' => DBResult::CAST_INT,
            'assignee_id' => DBResult::CAST_INT,
            'delegated_by_id' => DBResult::CAST_INT,
            'priority' => DBResult::CAST_INT,
            'state' => DBResult::CAST_INT,
            'created_by_id' => DBResult::CAST_INT,
            'completed_by_id' => DBResult::CAST_INT
          ));

          foreach($subtasks as $subtask) {
            $results[] = array(
              'id'                  => $subtask['id'],
              'type'                => $subtask['type'],
              'parent_type'         => $subtask['parent_type'],
              'parent_id'           => $subtask['parent_id'],
              'label_id'            => $subtask['label_id'],
              'assignee_id'         => $subtask['assignee_id'],
              'delegated_by_id'     => $subtask['delegated_by_id'],
              'priority'            => $subtask['priority'],
              'body'                => $subtask['body'],
              'body_formatted'      => $subtask['body_formatted'],
              'due_on'              => $subtask['due_on'],
              'state'               => $subtask['state'],
              'created_on'          => $subtask['created_on'],
              'created_by_id'       => $subtask['created_by_id'],
              'created_by_name'     => $subtask['created_by_name'],
              'created_by_email'    => $subtask['created_by_email'],
              'completed_on'        => $subtask['completed_on'],
              'completed_by_id'     => $subtask['completed_by_id'],
              'completed_by_name'   => $subtask['completed_by_name'],
              'completed_by_email'  => $subtask['completed_by_email']
            );
          } // foreach

          if($results) {
            $task_url_pattern = $task_subtask_url_pattern = '';
            $tasks = array();

            if(AngieApplication::isModuleLoaded('tasks')) {
              $task_url_pattern = Router::assemble('project_task', array(
                'project_slug' => $project->getSlug(),
                'task_id' => '--TASK-ID--',
              ));

              $task_subtask_url_pattern = Router::assemble('project_task_subtask', array(
                'project_slug' => $project->getSlug(),
                'task_id' => '--TASK-ID--',
                'subtask_id' => '--SUBTASK-ID--',
              ));

              // Cache task data
              $task_rows = DB::execute("SELECT id, integer_field_1 AS 'task_id' FROM " . TABLE_PREFIX . 'project_objects WHERE project_id = ? AND type = ? AND state >= ?', $project->getId(), 'Task', STATE_ARCHIVED);

              if($task_rows) {
                foreach($task_rows as $task_row) {
                  $tasks[(integer) $task_row['id']] = (integer) $task_row['task_id'];
                } // foreach
              } // if
            } // if

            // Prepare parent URL-s and subtask permalinks
            foreach($results as $k => $subtask) {
              $results[$k]['parent_url'] = '';
              $results[$k]['permalink'] = '';

              if($subtask['parent_type'] == 'Task') {
                $task_id = $subtask['parent_id'];

                if($tasks[$task_id]) {
                  $results[$k]['parent_url'] = str_replace('--TASK-ID--', $tasks[$task_id], $task_url_pattern);
                  $results[$k]['permalink'] = str_replace(array('--TASK-ID--', '--SUBTASK-ID--'), array($tasks[$task_id], $subtask['id']), $task_subtask_url_pattern);
                } // if
              } // if
            } // foreach
          } // if

          $buffer = '';
          foreach($results as $k => $result) {
            if($count > 0) $buffer .= ',';

            $buffer .= JSON::encode($result);

            if($count % 15 == 0 && $count > 0) {
              fwrite($output_handle, $buffer);
              $buffer = '';
            } // if

            $count++;
          } // foreach

          if($buffer) {
            fwrite($output_handle, $buffer);
          } // if
        } // if
      } // if

      // Close json array
      fwrite($output_handle, ']');

      // Close the handle and set correct permissions
      fclose($output_handle);
      @chmod($output_file, 0777);

      return $count;
    } // exportToFileByProject

    /**
     * Find type ID map of potential subtask parents in a given project
     *
     * @param Project $project
     * @param integer $min_state
     * @return array
     */
    static function findTypeIdMapOfPotentialParents(Project $project, $min_state = STATE_ARCHIVED) {
      $map = array();

      $rows = DB::execute('SELECT id, type FROM ' . TABLE_PREFIX . 'project_objects WHERE project_id = ? AND state >= ? AND type IN (?)', $project->getId(), $min_state, array('Task', 'TodoList'));
      if($rows) {
        foreach($rows as $row) {
          if(isset($map[$row['type']])) {
            $map[$row['type']][] = (integer) $row['id'];
          } else {
            $map[$row['type']] = array((integer) $row['id']);
          } // if
        } // foreach
      } // if

      EventsManager::trigger('on_extend_project_items_type_id_map', array(&$project, $min_state, &$map));

      return count($map) ? $map : null;
    } // findTypeIdMapOfPotentialParents

	  /**
	   * Find subtasks for calendar by user
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

		  // prepare task tables
		  $project_objects_table = TABLE_PREFIX . "project_objects";

		  // initialize task conditions
		  $task_conditions = array();
		  $task_conditions[] = DB::prepare('type = ? AND visibility >= ?', 'Task', $user->getMinVisibility());

		  // add completed and archived condition
		  if ($include_completed_and_archived) {
			  $task_conditions[] = DB::prepare('state >= ?', STATE_ARCHIVED);
		  } else {
			  $task_conditions[] = DB::prepare('completed_on IS NULL AND state = ?', STATE_VISIBLE);
		  } // if

		  // add all for admins and project manages condition
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

			  $task_conditions[] = DB::prepare('project_id IN (?)', $project_ids);
		  } // if

		  // return false if there is no defined conditions
		  if (!$task_conditions) {
			  return false;
		  } // if

		  // find tasks with given conditions
		  $task_conditions = implode(" AND ", $task_conditions);
		  $tasks = DB::execute("SELECT id, project_id, integer_field_1 as task_id FROM $project_objects_table WHERE $task_conditions");

		  // return false if there is no task found
		  if (!$tasks) {
			  return false;
		  } // if

		  // prepare task map
		  $task_ids = array();
		  $task_id_map = array();
		  $task_id_project_id_map = array();
		  if (is_foreachable($tasks)) {
			  foreach ($tasks as $subobject) {
				  $task_id = $subobject['id'];
				  $task_ids[] = $task_id;
				  $task_id_map[$task_id] = $subobject['task_id'];
				  $task_id_project_id_map[$task_id] = $subobject['project_id'];
			  } // foreach
		  } // if

		  // return false if there is no task ids defined after task map
		  if (!$task_ids) {
			  return false;
		  } // if

		  // prepare subtask tables
		  $subtasks_table = TABLE_PREFIX . "subtasks";
		  $assignments_table = TABLE_PREFIX . "assignments";

		  // initialize subtask condiditions
		  $subtask_conditions = array();
		  $subtask_conditions[] = DB::prepare('parent_id IN (?) AND parent_type = ?', $task_ids, 'Task');
		  $subtask_conditions[] = DB::prepare('due_on IS NOT NULL');

		  // add completed and archived condition
		  if ($include_completed_and_archived) {
			  $subtask_conditions[] = DB::prepare('state >= ?', STATE_ARCHIVED);
		  } else {
			  $subtask_conditions[] = DB::prepare('completed_on IS NULL AND state = ?', STATE_VISIBLE);
		  } // if

		  // add date time condition
		  if ($from instanceof DateValue && $to instanceof DateValue) {
			  $subtask_conditions[] = DB::prepare('due_on BETWEEN ? AND ?', $from->toMySQL(), $to->toMySQL());
		  } // if

		  // add assignee condition
		  if ($assigned) {
			  $user_assigned_subtask_ids = DB::executeFirstColumn("SELECT parent_id FROM $assignments_table WHERE parent_type = ? AND user_id = ?", "Subtask", $user->getId());
			  if ($user_assigned_subtask_ids) {
				  $subtask_conditions[] = DB::prepare('(id IN (?) OR assignee_id = ?)', $user_assigned_subtask_ids, $user->getId());
			  } else {
				  $subtask_conditions[] = DB::prepare('assignee_id = ?', $user->getId());
			  } // if
		  } // if

		  // return false if there is no subtask conditions defined
		  if (!$subtask_conditions) {
			  return false;
		  } // if

		  // find subtasks with given condition
		  $subtask_conditions = implode(" AND ", $subtask_conditions);
		  $subtasks = DB::execute("SELECT id, body as name, parent_id, parent_type, due_on, state, completed_on FROM $subtasks_table WHERE $subtask_conditions");

		  // return false if there is no subtasks
		  if (!$subtasks) {
			  return false;
		  } // if

		  if (is_foreachable($subtasks)) {
			  // casting
			  $subtasks->setCasting(array(
				  'due_on' => DBResult::CAST_DATE
			  ));

			  $subtasks_id_prefix_pattern = '--SUBTASK-ID--';
			  $tasks_id_prefix_pattern = '--TASK-ID--';
			  $project_slug_prefix_pattern = '--PROJECT-SLUG--';
			  $task_url_params = array('project_slug' => $project_slug_prefix_pattern, 'task_id' => $tasks_id_prefix_pattern);
			  $routing_context = 'project_task';
			  $routing_params = array_merge($task_url_params, array('subtask_id' => $subtasks_id_prefix_pattern));
			  $view_subtask_url_pattern = Router::assemble($routing_context . '_subtask', $routing_params);
			  $edit_subtask_url_pattern = Router::assemble($routing_context . '_subtask_edit', $routing_params);
			  $reschedule_subtask_url_pattern = Router::assemble($routing_context . '_subtask_reschedule', $routing_params);

			  foreach ($subtasks as $subobject) {
				  $id = $subobject['id'];
				  $task_id = $subobject['parent_id'];
				  $project_id = $task_id_project_id_map[$task_id];
				  $completed_on = $subobject['completed_on'];
          $due_on = $subobject['due_on'];

				  $project = DataObjectPool::get('Project', $project_id);
				  $parent = DataObjectPool::get('Task', $task_id);

				  $result[] = array(
					  'id'              => $id,
					  'type'            => 'ProjectObjectSubtask',
					  'parent_id'       => $project_id,
					  'parent_type'     => 'Project',
					  'org_parent_id'   => $subobject['parent_id'],
					  'org_parent_type' => $subobject['parent_type'],
					  'name'            => $subobject['name'],
					  'ends_on'         => $due_on,
					  'starts_on'       => $due_on,
					  'permissions'     => array(
						  'can_edit'        => $parent instanceof Task ? $parent->canEdit($user) : false,
						  'can_trash'       => false,
						  'can_reschedule'  => ($user->projects()->getPermission('task', $project) >= ProjectRole::PERMISSION_MANAGE && !$completed_on && $subobject['state'] == STATE_VISIBLE)
					  ),
					  'urls'            => array(
						  'view'            => str_replace($subtasks_id_prefix_pattern, $id, str_replace($tasks_id_prefix_pattern, $task_id_map[$task_id], str_replace($project_slug_prefix_pattern, $project->getSlug(), $view_subtask_url_pattern))),
						  'edit'            => str_replace($subtasks_id_prefix_pattern, $id, str_replace($tasks_id_prefix_pattern, $task_id_map[$task_id], str_replace($project_slug_prefix_pattern, $project->getSlug(), $edit_subtask_url_pattern))),
						  'reschedule'      => str_replace($subtasks_id_prefix_pattern, $id, str_replace($tasks_id_prefix_pattern, $task_id_map[$task_id], str_replace($project_slug_prefix_pattern, $project->getSlug(), $reschedule_subtask_url_pattern)))
					  ),
					  'completed'       => $completed_on != null,
					  'archived'        => $subobject['state'] == STATE_ARCHIVED
				  );
			  } // foreach
		  } // if

		  return $result;
	  } // findForCalendarByUser
    
  }