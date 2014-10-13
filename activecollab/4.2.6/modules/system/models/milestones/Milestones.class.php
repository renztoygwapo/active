<?php

  /**
   * Milestones manager class
   *
   * @package activeCollab.modules.milestones
   * @subpackage models
   */
  class Milestones extends ProjectObjects {
    
    /**
     * Default ordering of milestones
     * 
     * @var string
     */
    static private $order_milestones_by = 'NOT ISNULL(completed_on), ISNULL(date_field_1), date_field_1, position, created_on';
    
    /**
     * Returns true if $user can access milestones seciton of $project
     * 
     * @param IUser $user
     * @param Project $project
     * @param boolean $check_tab
     * @return boolean
     */
    static function canAccess(IUser $user, Project $project, $check_tab = true) {
      return ProjectObjects::canAccess($user, $project, 'milestone', ($check_tab ? 'milestones' : null));
    } // canAccess
    
    /**
     * Returns true if $user can add milestones to $project
     * 
     * @param IUser $user
     * @param Project $project
     * @param boolean $check_tab
     * @return boolean
     */
    static function canAdd(IUser $user, Project $project, $check_tab = true) {
      return ProjectObjects::canAdd($user, $project, 'milestone', ($check_tab ? 'milestones' : null));
    } // canAdd
    
    /**
     * Returns true if $user can manage discussions in $project
     * 
     * @param IUser $user
     * @param Project $project
     * @param boolean $check_tab
     * @return boolean
     */
    static function canManage(IUser $user, Project $project, $check_tab = true) {
      return ProjectObjects::canManage($user, $project, 'milestone', ($check_tab ? 'milestones' : null));
    } // canManage
    
    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------
    
    /**
     * Return milestones by a given list of ID-s
     *
     * @param array $ids
     * @param integer $min_state
     * @param integer $min_visibility
     * @return Milestone[]|null
     */
    static function findByIds($ids, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('id IN (?) AND type = ? AND state >= ? AND visibility >= ?', $ids, 'Milestone', $min_state, $min_visibility),
        'order' => self::$order_milestones_by,
      ));
    } // findByIds
    
    /**
     * Return all visible milestone by a project
     *
     * @param Project $project
     * @param integer $min_visibility
     * @return Milestone[]
     */
    static function findAllByProject($project, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $project->getId(), 'Milestone', STATE_VISIBLE, $min_visibility),
        'order' => self::$order_milestones_by,
      ));
    } // findAllByProject
  
    /**
     * Return all milestones for a given project
     *
     * @param Project $project
     * @param User $user
     * @return Milestone[]
     */
    static function findByProject(Project $project, User $user) {
      if(Milestones::canAccess($user, $project)) {
        return ProjectObjects::find(array(
          'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $project->getId(), 'Milestone', STATE_VISIBLE, $user->getMinVisibility()),
          'order' => self::$order_milestones_by,
        ));
      } // if
      return null;
    } // findByProject

    /**
     * Find archived notebooks by project
     *
     * @param Project $project
     * @param integer $state
     * @param integer $min_visibility
     * @return array
     */
    static function findArchivedByProject(Project $project, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('project_id = ? AND type = ? AND state = ? AND visibility >= ?', $project->getId(), 'Milestone', STATE_ARCHIVED, $min_visibility),
        'order' => self::$order_milestones_by
      ));
    } // findArchivedByProject
    
    /**
     * Count milestones by project
     * 
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return number
     */
    static function countByProject(Project $project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
       return Milestones::count(array('project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $project->getId(), 'Milestone', $min_state, $min_visibility));
    } // countByProject
    
    /**
     * Return milestones for timeline
     * 
     * @param Project $project
     * @param User $user
     * @return DBResult
     */
    static function findForTimeline(Project $project, User $user, $state = STATE_VISIBLE) {
      if(Milestones::canAccess($user, $project)) {
        return ProjectObjects::find(array(
          'conditions' => array('project_id = ? AND type = ? AND state = ? AND visibility >= ?', $project->getId(), 'Milestone', $state, $user->getMinVisibility()),
          'order' => self::$order_milestones_by,
        ));
      } // if
      return null;      
    } // findForTimeline

    /**
     * Find for outline
     *
     * @static
     * @param Project $project
     * @param User $user
     * @param int $state
     */
    static function findForOutline(Project $project, User $user, $state = STATE_VISIBLE) {
      $results = array();

      $milestone_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'project_objects WHERE project_id = ? AND type = ? AND state >= ? AND visibility >= ? AND completed_on IS NULL', $project->getId(), 'Milestone', $state, $user->getMinVisibility());
      $milestones = DB::execute('SELECT id, name, body, priority, due_on, date_field_1 AS start_on, assignee_id, visibility, created_by_id FROM ' . TABLE_PREFIX . 'project_objects WHERE ID IN(?) ORDER BY ' . self::$order_milestones_by, $milestone_ids);

      if (is_foreachable($milestones)) {
        // casting
        $milestones->setCasting(array(
          'due_on'        => DBResult::CAST_DATE,
          'start_on'      => DBResult::CAST_DATE
        ));

        // urls
        $milestone_id_prefix_pattern = '--MILESTONE-ID--';
        $milestone_url_params = array('project_slug' => $project->getSlug(), 'milestone_id' => $milestone_id_prefix_pattern);
        $view_milestone_url_pattern = Router::assemble('project_milestone', $milestone_url_params);
        $edit_milestone_url_pattern = Router::assemble('project_milestone_edit', $milestone_url_params);
        $trash_milestone_url_pattern = Router::assemble('project_milestone_trash', $milestone_url_params);
        $subscribe_milestone_url_pattern = Router::assemble('project_milestone_subscribe', $milestone_url_params);
        $unsubscribe_milestone_url_pattern = Router::assemble('project_milestone_unsubscribe', $milestone_url_params);
        $reschedule_milestone_url_pattern = Router::assemble('project_milestone_reschedule', $milestone_url_params);
        $complete_milestone_url_pattern = Router::assemble('project_milestone_complete', $milestone_url_params);

        // can_manage_milestones
        $can_manage_milestones = ($user->projects()->getPermission('milestone', $project) >= ProjectRole::PERMISSION_MANAGE);

        // all assignees
        $user_assignments_on_milestones = DB::executeFirstColumn('SELECT parent_id FROM ' . TABLE_PREFIX . 'assignments WHERE parent_id IN (?) AND parent_type = ? AND user_id = ?', $milestone_ids, 'Milestone', $user->getId());

        // all subscriptions
        $user_subscriptions_on_milestones = DB::executeFirstColumn('SELECT parent_id FROM ' . TABLE_PREFIX . 'subscriptions WHERE parent_id IN (?) AND parent_type = ? AND user_id = ?', $milestone_ids, 'Milestone', $user->getId());

        $other_assignees = array();
        $raw_other_assignees = DB::execute('SELECT user_id, parent_id FROM ' . TABLE_PREFIX . 'assignments WHERE parent_type = ? AND parent_id IN (?)', 'Milestone', $milestone_ids);
        foreach ($raw_other_assignees as $raw_assignee) {
          if (!is_array($other_assignees[$raw_assignee['parent_id']])) {
            $other_assignees[$raw_assignee['parent_id']] = array();
          } // if
          $other_assignees[$raw_assignee['parent_id']][] = array('id' => $raw_assignee['user_id']);
        } // foreach

        foreach ($milestones as $subobject) {
          $milestone_id = $subobject['id'];

          $results[] = array(
            'id'                  => $milestone_id,
            'name'                => $subobject['name'],
            'body'                => $subobject['body'],
            'priority'            => $subobject['priority'],
            'class'               => 'Milestone',
            'start_on'            => $subobject['start_on'],
            'due_on'              => $subobject['due_on'],
            'assignee_id'         => $subobject['assignee_id'],
            'other_assignees'     => isset($other_assignees[$milestone_id]) ? $other_assignees[$milestone_id] : null,
            'user_is_subscribed'  => in_array($milestone_id, $user_subscriptions_on_milestones),
            'event_names'         => array(
              'updated'             => 'milestone_updated'
            ),
            'urls'                => array(
              'view'                => str_replace('--MILESTONE-ID--', $milestone_id, $view_milestone_url_pattern),
              'edit'                => str_replace('--MILESTONE-ID--', $milestone_id, $edit_milestone_url_pattern),
              'trash'               => str_replace('--MILESTONE-ID--', $milestone_id, $trash_milestone_url_pattern),
              'subscribe'           => str_replace('--MILESTONE-ID--', $milestone_id, $subscribe_milestone_url_pattern),
              'unsubscribe'         => str_replace('--MILESTONE-ID--', $milestone_id, $unsubscribe_milestone_url_pattern),
              'reschedule'          => str_replace('--MILESTONE-ID--', $milestone_id, $reschedule_milestone_url_pattern),
              'complete'            => str_replace('--MILESTONE-ID--', $milestone_id, $complete_milestone_url_pattern),
            ),
            'permissions'         => array(
              'can_edit'            => can_edit_project_object($subobject, $user, $project, $can_manage_milestones, $user_assignments_on_milestones),
              'can_trash'           => can_trash_project_object($subobject, $user, $project, $can_manage_milestones, $user_assignments_on_milestones),
            )
          );
        } // foreach
      } // if

      return $results;
    } // findForOutline

    /**
     * Find all milestones in project and prepare them for export
     *
     * @param Project $project
     * @param User $user
     * @param array $parents_map
     * @param int $changes_since
     * @return array
     */
    static function findForExport(Project $project, User $user, &$parents_map, $changes_since) {
      $result = array();

      if(Milestones::canAccess($user, $project)) {
        $project_objects_table = TABLE_PREFIX . 'project_objects';

        $additional_condition = '';
        if(!is_null($changes_since)) {
          $changes_since_date = DateTimeValue::makeFromTimestamp($changes_since);
          $additional_condition = "AND (created_on > '$changes_since_date' OR updated_on > '$changes_since_date')";
        } // if

        $milestones = DB::execute("SELECT id, type, name, body, body AS 'body_formatted', project_id, assignee_id, delegated_by_id, state, visibility, priority, created_by_id, created_on, due_on, updated_by_id, updated_on, completed_by_id, completed_on, is_locked, date_field_1, version FROM $project_objects_table WHERE type = ? AND project_id = ? AND state >= ? AND visibility >= ? $additional_condition ORDER BY " . self::$order_milestones_by, 'Milestone', $project->getId(), STATE_ARCHIVED, $user->getMinVisibility());

        if($milestones instanceof DBResult) {
          $milestones->setCasting(array(
            'id' => DBResult::CAST_INT,
            'body_formatted' => function($in) {
              return HTML::toRichText($in);
            },
            'project_id' => DBResult::CAST_INT,
            'assignee_id' => DBResult::CAST_INT,
            'delegated_by_id' => DBResult::CAST_INT,
            'created_by_id' => DBResult::CAST_INT,
            'updated_by_id' => DBResult::CAST_INT,
            'completed_by_id' => DBResult::CAST_INT
          ));

          $milestone_url = Router::assemble('project_milestone', array('project_slug' => $project->getSlug(), 'milestone_id' => '--MILESTONEID--'));

          foreach($milestones as $milestone) {
            $current = Milestones::findById($milestone['id']);

            // Progress
            list($total_tasks, $open_tasks) = ProjectProgress::getMilestoneProgress($current);

            // Other assignee ID-s
            $users_table = TABLE_PREFIX . 'users';
            $assignments_table = TABLE_PREFIX . 'assignments';

            $other_assignee_ids = DB::executeFirstColumn("SELECT $users_table.id FROM $users_table, $assignments_table WHERE $users_table.id = $assignments_table.user_id AND $assignments_table.parent_type = ? AND $assignments_table.parent_id = ? AND $users_table.state >= ?", 'Milestone', $milestone['id'], STATE_ARCHIVED);

            $result[] = array(
              'id'              => $milestone['id'],
              'type'            => $milestone['type'],
              'name'            => $milestone['name'],
              'body'            => $milestone['body'],
              'body_formatted'  => $milestone['body_formatted'],
              'project_id'      => $milestone['project_id'],
              'assignee_id'     => $milestone['assignee_id'],
              'other_assignees' => $other_assignee_ids,
              'delegated_by_id' => $milestone['delegated_by_id'],
              'state'           => $milestone['state'],
              'visibility'      => $milestone['visibility'],
              'priority'        => $milestone['priority'],
              'created_by_id'   => $milestone['created_by_id'],
              'created_on'      => $milestone['created_on'],
              'due_on'          => $milestone['due_on'],
              'updated_by_id'   => $milestone['updated_by_id'],
              'updated_on'      => $milestone['updated_on'],
              'completed_by_id' => $milestone['completed_by_id'],
              'completed_on'    => $milestone['completed_on'],
              'start_on'        => $milestone['date_field_1'],
              'is_completed'    => $milestone['completed_on'] === null ? '0' : '1',
              'is_locked'       => $milestone['is_locked'],
              'permalink'       => str_replace('--MILESTONEID--', $milestone['id'], $milestone_url),
              'version'         => $milestone['version'],
              'progress'        => array(
                'total_tasks'     => $total_tasks,
                'open_tasks'      => $open_tasks,
                'percents_done'   => $current->getPercentsDone()
              )
            );

            $parents_map[$milestone['type']][] = $milestone['id'];
          } // foreach
        } // if
      } // if

      return $result;
    } // findForExport

    /**
     * Find all milestones in project and prepare them for export
     *
     * @param Project $project
     * @param User $user
     * @param string $output_file
     * @param array $parents_map
     * @param int $changes_since
     * @return array
     */
    static function exportToFileByProject(Project $project, User $user, $output_file, &$parents_map, $changes_since) {
      if(!($output_handle = fopen($output_file, 'w+'))) {
        throw new Error(lang('Failed to write JSON file to :file_path', array('file_path' => $output_file)));
      } // if

      // Open json array
      fwrite($output_handle, '[');

      $count = 0;
      if(Milestones::canAccess($user, $project)) {
        $project_objects_table = TABLE_PREFIX . 'project_objects';

        $additional_condition = '';
        if(!is_null($changes_since)) {
          $changes_since_date = DateTimeValue::makeFromTimestamp($changes_since);
          $additional_condition = "AND (created_on > '$changes_since_date' OR updated_on > '$changes_since_date')";
        } // if

        $milestones = DB::execute("SELECT id, type, name, body, body AS 'body_formatted', project_id, assignee_id, delegated_by_id, state, visibility, priority, created_by_id, created_on, due_on, updated_by_id, updated_on, completed_by_id, completed_on, is_locked, date_field_1, version FROM $project_objects_table WHERE type = ? AND project_id = ? AND state >= ? AND visibility >= ? $additional_condition ORDER BY " . self::$order_milestones_by, 'Milestone', $project->getId(), (boolean) $additional_condition ? STATE_TRASHED : STATE_ARCHIVED, $user->getMinVisibility());

        if($milestones instanceof DBResult) {
          $milestones->setCasting(array(
            'id' => DBResult::CAST_INT,
            'body_formatted' => function($in) {
              return HTML::toRichText($in);
            },
            'project_id' => DBResult::CAST_INT,
            'assignee_id' => DBResult::CAST_INT,
            'delegated_by_id' => DBResult::CAST_INT,
            'created_by_id' => DBResult::CAST_INT,
            'updated_by_id' => DBResult::CAST_INT,
            'completed_by_id' => DBResult::CAST_INT
          ));

          $milestone_url = Router::assemble('project_milestone', array('project_slug' => $project->getSlug(), 'milestone_id' => '--MILESTONEID--'));

          $buffer = '';
          foreach($milestones as $milestone) {
            $current = Milestones::findById($milestone['id']);

            // Progress
            list($total_tasks, $open_tasks) = ProjectProgress::getMilestoneProgress($current);

            // Other assignee ID-s
            $users_table = TABLE_PREFIX . 'users';
            $assignments_table = TABLE_PREFIX . 'assignments';

            $other_assignee_ids = DB::executeFirstColumn("SELECT $users_table.id FROM $users_table, $assignments_table WHERE $users_table.id = $assignments_table.user_id AND $assignments_table.parent_type = ? AND $assignments_table.parent_id = ? AND $users_table.state >= ?", 'Milestone', $milestone['id'], STATE_ARCHIVED);

            if($count > 0) $buffer .= ',';

            $buffer .= JSON::encode(array(
              'id'              => $milestone['id'],
              'type'            => $milestone['type'],
              'name'            => $milestone['name'],
              'body'            => $milestone['body'],
              'body_formatted'  => $milestone['body_formatted'],
              'project_id'      => $milestone['project_id'],
              'assignee_id'     => $milestone['assignee_id'],
              'other_assignees' => $other_assignee_ids,
              'delegated_by_id' => $milestone['delegated_by_id'],
              'state'           => $milestone['state'],
              'visibility'      => $milestone['visibility'],
              'priority'        => $milestone['priority'],
              'created_by_id'   => $milestone['created_by_id'],
              'created_on'      => $milestone['created_on'],
              'due_on'          => $milestone['due_on'],
              'updated_by_id'   => $milestone['updated_by_id'],
              'updated_on'      => $milestone['updated_on'],
              'completed_by_id' => $milestone['completed_by_id'],
              'completed_on'    => $milestone['completed_on'],
              'start_on'        => $milestone['date_field_1'],
              'is_completed'    => $milestone['completed_on'] === null ? '0' : '1',
              'is_locked'       => $milestone['is_locked'],
              'permalink'       => str_replace('--MILESTONEID--', $milestone['id'], $milestone_url),
              'version'         => $milestone['version'],
              'progress'        => array(
                'total_tasks'     => $total_tasks,
                'open_tasks'      => $open_tasks,
                'percents_done'   => $current->getPercentsDone()
              )
            ));

            if($count % 15 == 0 && $count > 0) {
              fwrite($output_handle, $buffer);
              $buffer = '';
            } // if

            $parents_map[$milestone['type']][] = $milestone['id'];
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
     * Find milestones for printing by grouping and filtering criteria
     *
     * @param Project $project
     * @param int $min_state
     * @param int $min_visibility
     * @return array|DBResult
     */
    static function findForPrint(Project $project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      // initial condition
      $conditions = array(
        DB::prepare('(project_id = ? AND type = ? AND state = ? AND visibility >= ?)', $project->getId(), 'Milestone', $min_state, $min_visibility),
      );

      // do find discussions
      $milestones = Milestones::find(array(
        'conditions' => implode(' AND ', $conditions),
        'order' => Milestones::$order_milestones_by
      ));

      return $milestones;
    } // findForPrint
    
    /**
     * Return all active milestones in a given project
     *
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    static function findActiveByProject($project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ? AND completed_on IS NULL', $project->getId(), 'Milestone', $min_state, $min_visibility),
        'order' => self::$order_milestones_by
      ));
    } // findActiveByProject
    
    /**
     * Return completed milestones by project
     *
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    static function findCompletedByProject($project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ? AND completed_on IS NOT NULL', $project->getId(), 'Milestone', $min_state, $min_visibility),
        'order' => self::$order_milestones_by,
      ));
    } // findCompletedByProject
    
    /**
     * Find successive milestones by a given milestone
     *
     * @param Milestone $milestone
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    static function findSuccessiveByMilestones(Milestone $milestone, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      $start_on = $milestone->getStartOn();
      
      if($start_on instanceof DateValue) {
        return Milestones::find(array(
          'conditions' => array('project_id = ? AND type = ? AND date_field_1 > ? AND state >= ? AND visibility >= ? AND id != ?', $milestone->getProjectId(), 'Milestone', $start_on, $min_state, $min_visibility, $milestone->getId()),
          'order' => 'date_field_1',
        ));
      } else {
        return null;
      } // if
    } // findSuccessiveByMilestones

	  /**
	   * Find milestones by a given user and describe them as calendar events
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
		  $conditions[] = DB::prepare('type = ? AND visibility >= ?', 'Milestone', $user->getMinVisibility());
		  $conditions[] = DB::prepare('date_field_1 IS NOT NULL AND due_on IS NOT NULL');

		  // add completed and archived condition
		  if ($include_completed_and_archived) {
			  $conditions[] = DB::prepare('state >= ?', STATE_ARCHIVED);
		  } else {
			  $conditions[] = DB::prepare('completed_on IS NULL AND state = ?', STATE_VISIBLE);
		  } // if

		  // add date time condition
		  if ($from instanceof DateValue && $to instanceof DateValue) {
			  $conditions[] = DB::prepare('((date_field_1 BETWEEN ? AND ?) OR (due_on BETWEEN ? AND ?) OR (date_field_1 < ? AND due_on > ?))', $from->toMySQL(), $to->toMySQL(), $from->toMySQL(), $to->toMySQL(), $from->toMySQL(), $to->toMySQL());
		  } // if

		  // add assignee condition
		  if ($assigned) {
			  $user_assigned_milestone_ids = DB::executeFirstColumn("SELECT parent_id FROM $assignments_table WHERE parent_type = ? AND user_id = ?", "Milestone", $user->getId());
			  if ($user_assigned_milestone_ids) {
				  $conditions[] = DB::prepare('(id IN (?) OR assignee_id = ?)', $user_assigned_milestone_ids, $user->getId());
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
					  if ($user->projects()->getPermission('milestone', $project) >= ProjectRole::PERMISSION_ACCESS) {
						  array_push($project_ids, $project->getId());
					  } // if
				  } // foreach
			  } // if

			  if (!$project_ids) {
				  return false;
			  } // if

			  $conditions[] = DB::prepare('project_id IN (?)', $project_ids);
		  } // if

		  // return if there is no conditions defined
		  if (!$conditions) {
			  return false;
		  } // if

		  // finally execute query
		  $conditions = implode(" AND ", $conditions);

		  // find all milestone ids by conditions
		  $milestone_ids = DB::executeFirstColumn("SELECT id FROM $project_objects_table WHERE $conditions");

		  // return false if there is no milestone ids found
		  if (!$milestone_ids) {
			  return false;
		  } // if

		  // find all milestones by ids
		  $milestones = DB::execute("SELECT id, name, project_id, date_field_1 as start_on, due_on, completed_on, state FROM $project_objects_table WHERE id IN (?)", $milestone_ids);

		  if (is_foreachable($milestones)) {
			  // casting
			  $milestones->setCasting(array(
				  'start_on'  => DBResult::CAST_DATE,
				  'due_on'    => DBResult::CAST_DATE
			  ));

			  // all assignees
			  $user_assignments_on_milestones = DB::executeFirstColumn('SELECT parent_id FROM ' . TABLE_PREFIX . 'assignments WHERE parent_id IN (?) AND parent_type = ? AND user_id = ?', $milestone_ids, 'Milestone', $user->getId());

			  // urls
			  $milestone_id_prefix_pattern = '--MILESTONE-ID--';
			  $project_slug_prefix_pattern = '--PROJECT-SLUG--';
			  $milestone_url_params = array('project_slug' => $project_slug_prefix_pattern, 'milestone_id' => $milestone_id_prefix_pattern);
			  $view_milestone_url_pattern = Router::assemble('project_milestone', $milestone_url_params);
			  $edit_milestone_url_pattern = Router::assemble('project_milestone_edit', $milestone_url_params);
			  $reschedule_milestone_url_pattern = Router::assemble('project_milestone_reschedule', $milestone_url_params);

			  foreach ($milestones as $subobject) {
				  $id = $subobject['id'];
          $project_id = $subobject['project_id'];
          $state = $subobject['state'];
          $completed_on = $subobject['completed_on'];
          $start_on = $subobject['start_on'];
          $due_on = $subobject['due_on'];

				  // get project as object from pool
				  $project = DataObjectPool::get('Project', $project_id);

				  // can_manage_milestones
				  $can_manage_milestones = ($user->projects()->getPermission('milestone', $project) >= ProjectRole::PERMISSION_MANAGE);

				  $result[] = array(
					  'id'            => $id,
            'type'          => 'Milestone',
            'parent_id'     => $project_id,
            'parent_type'   => 'Project',
            'name'          => $subobject['name'],
            'ends_on'       => $due_on,
            'starts_on'     => $start_on,
					  'permissions'   => array(
						  'can_edit'        => can_edit_project_object($subobject, $user, $project, $can_manage_milestones, $user_assignments_on_milestones),
						  'can_trash'       => false,
						  'can_reschedule'  => ($user->projects()->getPermission('milestone', $project) >= ProjectRole::PERMISSION_MANAGE && !$completed_on && $state == STATE_VISIBLE)
					  ),
					  'urls'          => array(
						  'view'          => str_replace($milestone_id_prefix_pattern, $id, str_replace($project_slug_prefix_pattern, $project->getSlug(), $view_milestone_url_pattern)),
						  'edit'          => str_replace($milestone_id_prefix_pattern, $id, str_replace($project_slug_prefix_pattern, $project->getSlug(), $edit_milestone_url_pattern)),
						  'reschedule'    => str_replace($milestone_id_prefix_pattern, $id, str_replace($project_slug_prefix_pattern, $project->getSlug(), $reschedule_milestone_url_pattern))
					  ),
					  'completed'     => $completed_on != null,
					  'archived'      => $state == STATE_ARCHIVED
				  );
			  } // foreach
		  } // if

		  return $result;
	  } // if

    // ---------------------------------------------------
    //  Utilities
    // ---------------------------------------------------
    
    /**
     * Returns ID name map
     * 
     * $filter can be:
     * 
     * - Project instance, only milestones from that project will be returned
     * - Array of milestone IDs
     * - NULL, in that case all milestones with given state will be returned
     *
     * @param mixed $filter
     * @param integer $min_state
     * @return array
     */
    static function getIdNameMap($filter = null, $min_state = STATE_VISIBLE) {
      if($filter instanceof Project) {
        $rows = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'project_objects WHERE project_id = ? AND type = ? AND state >= ? ORDER BY ' . self::$order_milestones_by, $filter->getId(), 'Milestone', $min_state);
      } elseif(is_array($filter)) {
        $rows = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'project_objects WHERE id IN (?) AND type = ? AND state >= ? ORDER BY ' . self::$order_milestones_by, $filter, 'Milestone', $min_state);
      } else {
        $rows = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'project_objects WHERE type = ? AND state >= ? ORDER BY ' . self::$order_milestones_by, 'Milestone', $min_state);
      } // if
      
      if($rows) {
        $result = array();
        
        foreach($rows as $row) {
          $result[(integer) $row['id']] = $row['name'];
        } // foreach
        
        return $result;
      } else {
        return null;
      } // if
    } // getIdNameMap
    
    /**
     * Return ID-s by list of milestone names
     * 
     * @param array $names
     * @param Project $project
     * @return array
     */
    static function getIdsByNames($names, $project = null) {
      if($names) {
        if($project instanceof Project) {
          $ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'project_objects WHERE project_id = ? AND name IN (?) AND type = ?', $project->getId(), $names, 'Milestone');
        } else {
          $ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'project_objects WHERE name IN (?) AND type = ?', $names, 'Milestone');
        } // if
        
        if($ids) {
          foreach($ids as $k => $v) {
            $ids[$k] = (integer) $v;
          } // foreach
        } // if
        
        return $ids;
      } else {
        return null;
      } // if
    } // getIdsByNames

    /**
     * Return unique milestone names
     *
     * @param array $project_ids
     * @param int $min_state
     * @return array
     */
    static function getUniqueNames($project_ids = null, $min_state = STATE_VISIBLE) {
      if($project_ids) {
        return DB::executeFirstColumn('SELECT DISTINCT name FROM ' . TABLE_PREFIX . 'project_objects WHERE project_id IN (?) AND type = ? AND state >= ? ORDER BY name', $project_ids, 'Milestone', $min_state);
      } else {
        return DB::executeFirstColumn('SELECT DISTINCT name FROM ' . TABLE_PREFIX . 'project_objects WHERE type = ? AND state >= ? ORDER BY name', 'Milestone', $min_state);
      } // if
    } // getUniqueNames
    
    /**
     * Return date when first project milestone starts on
     * 
     * @param Project $project
     * @return DateValue
     */
    static function getFirstMilestoneStartsOn(Project $project) {
      $first_milestone_starts_on = DB::executeFirstCell('SELECT date_field_1 FROM ' . TABLE_PREFIX . 'project_objects WHERE project_id = ? AND type = ? AND state >= ? AND date_field_1 IS NOT NULL ORDER BY date_field_1', $project->getId(), 'Milestone', STATE_VISIBLE);
      
      if($first_milestone_starts_on) {
        return DateValue::makeFromString($first_milestone_starts_on);
      } else {
        return DateValue::make($project->getCreatedOn()->getMonth(), $project->getCreatedOn()->getDay(), $project->getCreatedOn()->getYear());
      } // if
    } // getFirstMilestoneStartsOn

    /**
     * Fix milestone IDs for a given project
     *
     * @param Project $project
     */
    static function fixMilestoneIds(Project $project) {
      $project_objects_table = TABLE_PREFIX . 'project_objects';

      $milestone_ids = DB::executeFirstColumn("SELECT DISTINCT id FROM $project_objects_table WHERE type = 'Milestone' AND project_id = ? AND state >= ?", $project->getId(), STATE_TRASHED);

      // Reset all milestone ID-s if they are set, but don't belong to project's milestone
      if($milestone_ids) {
        DB::execute("UPDATE $project_objects_table SET milestone_id = NULL WHERE milestone_id IS NOT NULL AND milestone_id NOT IN (?) AND project_id = ?", $milestone_ids, $project->getId());

      // Reset all milestone ID-s if they are set, because this project does not have any visible milestones
      } else {
        DB::execute("UPDATE $project_objects_table SET milestone_id = NULL WHERE milestone_id IS NOT NULL AND project_id = ?", $project->getId());
      } // if
    } // fixMilestoneIds
  
  }