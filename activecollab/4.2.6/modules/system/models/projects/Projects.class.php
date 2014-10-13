<?php

  /**
   * Projects class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class Projects extends BaseProjects {

    /**
     * Make new mail to project code
     *
     * @param $length
     * @return string
     */
    static function newMailToProjectCode($length = 7) {
      do {
        $string = microtime();
        $mail_to_project_code = substr(sha1($string), 0, $length);
      } while (Projects::findByMailToProjectCode($mail_to_project_code) instanceof Project);
      return $mail_to_project_code;
    } //newMailToProjectCode

    /**
     * Check if $user can add new project
     *
     * @param User $user
     * @return boolean
     */
    static function canAdd(User $user) {
      return $user->isAdministrator() || $user->isProjectManager() || $user->isManager() || $user->getSystemPermission('can_add_projects');
    } // canAdd
    
    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------

    /**
     * Find project by mail_to_project_code
     *
     * @param string $code
     * @return Project
     */
    static function findByMailToProjectCode($code) {

      return Projects::find(array(
        'conditions' => array('mail_to_project_code = ?', $code),
        'one' => true,
      ));
    } // findByMailToProjectCode


    /**
     * Find project by slug
     *
     * @param string $slug
     * @return Project
     */
    static function findBySlug($slug) {
      $id_slug_map = Projects::getIdSlugMap();

      if($id_slug_map) {
        $project_id = array_search($slug, $id_slug_map);

        if($project_id) {
          return DataObjectPool::get('Project', $project_id);
        } // if
      } // if

      return Projects::find(array(
        'conditions' => array('slug = ?', $slug),
        'one' => true,
      ));
    } // findBySlug
    
    /**
     * Return all projects that $user is involved with
     *
     * @param User $user
     * @param bool $all_for_admins_and_pms
     * @param string $additional_conditions
     * @param string $order_by
     * @return Project[]
     */
    static function findByUser(User $user, $all_for_admins_and_pms = false, $additional_conditions = null, $order_by = null) {
      return self::_findByUser($user, $all_for_admins_and_pms, $additional_conditions, $order_by);
    } // findByUser
    
    /**
     * Return all project for company
     * 
     * @param Company $company
     * @return Project[]
     */
    static function findByCompany(Company $company) {
      return Projects::find(array(
        'conditions' => array('company_id = ?', $company->getId()),
        'order' => 'name',
      ));
    } //findByCompany

    /**
     * Find project IDs that the two users are working on
     *
     * @param User $first_user
     * @param User $second_user
     * @return array
     */
    static function findCommonProjectIds(User $first_user, User $second_user) {
      $project_users_table = TABLE_PREFIX.'project_users';
      return DB::executeFirstColumn("SELECT t1.project_id FROM $project_users_table t1 INNER JOIN $project_users_table t2 ON t1.project_id = t2.project_id WHERE t1.user_id = ? AND t2.user_id = ?", $first_user->getId(), $second_user->getId());
    } // findCommonProjectIds

    /**
     * Find projects that are common for two users
     *
     * @param User $first_user
     * @param User $second_user
     * @param string|null $additional_conditions
     * @param string|null $order_by
     * @return DBResult
     */
    static function findCommonProjects(User $first_user, User $second_user, $additional_conditions = null, $order_by = null) {
      $common_project_ids = self::findCommonProjectIds($first_user, $second_user);
      if (is_array($common_project_ids)) {
        $conditions = $additional_conditions ? " AND " . $additional_conditions : "";

        return Projects::find(array(
          "conditions" => array("id IN (?) $conditions", $common_project_ids),
          'order_by' => $order_by ? $order_by : "name, ISNULL(completed_on) DESC, completed_on DESC",
        ));
      } // if

      return null;
    } // findCommonProjects
    
    /**
     * Return active projects that $user is involved with
     *
     * @param User $user
     * @param boolean $all_for_admins_and_pms
     * @return DBResult
     */
    static function findActiveByUser(User $user, $all_for_admins_and_pms = false) {
      $projects_table = TABLE_PREFIX . 'projects';
      
      return self::_findByUser($user, $all_for_admins_and_pms, array("$projects_table.state >= ? AND $projects_table.completed_on IS NULL", STATE_VISIBLE));
    } // findActiveByUser
    
    /**
     * Return completed projects that $user is involved with
     *
     * @param User $user
     * @param boolean $all_for_admins_and_pms
     * @return DBResult
     */
    static function findCompletedByUser(User $user, $all_for_admins_and_pms = false) {
      $projects_table = TABLE_PREFIX . 'projects';
      
      return self::_findByUser($user, $all_for_admins_and_pms, array("$projects_table.state >= ? AND $projects_table.completed_on IS NOT NULL", STATE_VISIBLE), "completed_on DESC");
    } // findCompletedByUser
    
    /**
     * Find active projects that have budget property set
     * 
     * @param User $user
     * @param boolean $all_for_admins_and_pms
     * @return DBResult
     */
    static function findActiveByUserWithBudget(User $user, $all_for_admins_and_pms = false) {
      $projects_table = TABLE_PREFIX . 'projects';
      
      return self::_findByUser($user, $all_for_admins_and_pms, array("$projects_table.state >= ? AND $projects_table.completed_on IS NULL AND $projects_table.budget > 0", STATE_VISIBLE));
    } // findActiveByUserWithBudget
    
    /**
     * Return projects that $user belongs to
     *
     * @param User $user
     * @param bool $all_for_admins_and_pms
     * @param mixed $additional_conditions
     * @param string $order_by
     * @return Project[]
     */
    private static function _findByUser(User $user, $all_for_admins_and_pms = false, $additional_conditions = null, $order_by = null) {
      if($additional_conditions) {
        $additional_conditions = '(' . DB::prepareConditions($additional_conditions) . ')';
      } // if
      
      $projects_table = TABLE_PREFIX . 'projects';
      $project_users_table = TABLE_PREFIX . 'project_users';

      if(empty($order_by)) {
        $order_by = "$projects_table.name";
      } // if
      
      if($all_for_admins_and_pms && $user->isProjectManager()) {
        if($additional_conditions) {
          return Projects::findBySQL("SELECT * FROM $projects_table WHERE $additional_conditions ORDER BY $order_by");
        } else {
          return Projects::findBySQL("SELECT * FROM $projects_table ORDER BY $order_by");
        } // if
      } else {
        if($additional_conditions) {
          return Projects::findBySQL("SELECT $projects_table.* FROM $projects_table, $project_users_table WHERE $project_users_table.user_id = ? AND $project_users_table.project_id = $projects_table.id AND $additional_conditions ORDER BY $order_by", $user->getId());
        } else {
          return Projects::findBySQL("SELECT $projects_table.* FROM $projects_table, $project_users_table WHERE $project_users_table.user_id = ? AND $project_users_table.project_id = $projects_table.id ORDER BY $order_by", $user->getId());
        } // if
      } // if
    } // _findByUser

	  /**
	   * Return projects for timeline
	   *
     * @param User $user
	   * @return array|bool
	   */
    static function findForTimeline(User $user) {
		  $results = array();

		  $project_ids = self::findIdsByUser($user);

		  if (!$project_ids) {
			  return false;
		  } // if

		  $query = "SELECT p.id as id,
		                   p.slug as slug,
		                   p.name as name,
		                   p.completed_on as completed_on,
		                   p.leader_id as leader_id,
		                   MIN(o.date_field_1) as start_on,
		                   MAX(o.due_on) as due_on
		                   FROM
		                   " . TABLE_PREFIX . "projects as p
		                   LEFT JOIN " . TABLE_PREFIX ."project_objects as o ON o.project_id = p.id AND o.type = ? AND o.state = ?
		                   WHERE p.id IN (?)
		                   AND p.state = ?
		                   GROUP BY id
		                   ORDER BY start_on ASC";
		  $projects = DB::execute($query, "Milestone", STATE_VISIBLE, $project_ids, STATE_VISIBLE);

		  $project_slug_prefix_pattern = "--PROJECT-SLUG--";
		  $project_url_params = array('project_slug' => $project_slug_prefix_pattern);
		  $view_project_url_pattern = Router::assemble('project', $project_url_params);
		  $edit_project_url_pattern = Router::assemble('project_edit', $project_url_params);
		  $trash_project_url_pattern = Router::assemble('project_trash', $project_url_params);
		  $reopen_project_url_pattern = Router::assemble('project_reopen', $project_url_params);
		  $complete_project_url_pattern = Router::assemble('project_complete', $project_url_params);
		  $reschedule_project_url_pattern = Router::assemble('project_reschedule', $project_url_params);
		  $project_milestones_url_pattern = Router::assemble('project_milestones', $project_url_params);

		  if (is_foreachable($projects)) {
			  foreach ($projects as $subobject) {
				  $project_id = $subobject['id'];
				  $project_slug = $subobject['slug'];
				  $start_on = $subobject['start_on'];
				  $due_on = $subobject['due_on'];
				  $completed_on = $subobject['completed_on'];

				  list($total, $open) = !empty($completed_on) ? ProjectProgress::getCompletedProgress() : ProjectProgress::getQuickProgress($project_id);
				  $completed = $total - $open;
				  if($total && $completed) {
					  $percents_done = floor($completed / $total * 100);
				  } else {
					  $completed_on = $subobject['completed_on'];
					  $percents_done = $completed_on ? 100 : 0;
				  } // if

				  $leader = Users::findById($subobject['leader_id']);

				  $results[] = array(
					  'id'            => $project_id,
					  'slug'          => $project_slug,
					  'name'          => $subobject['name'],
					  'start_on'      => $start_on ? new DateValue(strtotime($start_on)) : null,
					  'due_on'        => $due_on ? new DateValue(strtotime($due_on)) : null,
					  'completed_on'  => $completed_on ? new DateValue(strtotime($completed_on)) : null,
					  'percents_done' => $percents_done,
					  'permissions'   => array(
						  'can_edit'      => true,
						  'can_trash'     => true
					  ),
					  'urls'          => array(
						  'edit'          => str_replace($project_slug_prefix_pattern, $project_slug, $edit_project_url_pattern),
						  'trash'         => str_replace($project_slug_prefix_pattern, $project_slug, $trash_project_url_pattern),
						  'open'          => str_replace($project_slug_prefix_pattern, $project_slug, $reopen_project_url_pattern),
						  'complete'      => str_replace($project_slug_prefix_pattern, $project_slug, $complete_project_url_pattern),
						  'reschedule'    => str_replace($project_slug_prefix_pattern, $project_slug, $reschedule_project_url_pattern),
						  'milestones'    => str_replace($project_slug_prefix_pattern, $project_slug, $project_milestones_url_pattern)
					  ),
					  'permalink'     => str_replace($project_slug_prefix_pattern, $project_slug, $view_project_url_pattern),
					  'leader'        => $leader instanceof User ? $leader->getName() : null
				  );
			  } // foreach
		  } // if

		  return $results;
	  } // findForTimeline

	  /**
	   * Find for calendar list
	   *
	   * @param User $user
	   * @param bool $all_for_admins_and_pms
	   * @return array|bool
	   */
	  static function findForCalendarList(User $user, $all_for_admins_and_pms = false) {
		  $result = array();

		  $projects_table = TABLE_PREFIX . "projects";

		  if ($all_for_admins_and_pms) {
			  $projects = DB::execute("SELECT * FROM $projects_table WHERE state = ? AND completed_on IS NULL GROUP BY id ORDER BY name ASC", STATE_VISIBLE);
		  } else {
			  $project_ids = self::findIdsByUser($user);

			  if (!$project_ids) {
				  return false;
			  } // if

			  $projects = DB::execute("SELECT * FROM $projects_table WHERE id IN (?) AND state = ? AND completed_on IS NULL GROUP BY id ORDER BY name ASC", $project_ids, STATE_VISIBLE);
		  } // if

		  if (is_foreachable($projects)) {

			  $project_id_prefix_pattern = "--PROJECT-ID--";
			  $project_slug_prefix_pattern = "--PROJECT-SLUG--";
			  $url_params_for_changes = array('type' => 'project', 'type_id' => $project_id_prefix_pattern);
				$project_url_params =  array('project_slug' => $project_slug_prefix_pattern);
			  $change_color_url_pattern = Router::assemble('calendar_change_color_by_type', $url_params_for_changes);
			  $change_visibility_url_pattern = Router::assemble('calendar_change_visibility_by_type', $url_params_for_changes);
			  $project_ical_subscribe_url_pattern = Router::assemble('project_ical_subscribe', $project_url_params);

			  foreach ($projects as $subobject) {
				  $project_id = $subobject['id'];
				  $project_slug = $subobject['slug'];
				  $config = Calendars::getLoggedUserConfigByTypeId('Project', $project_id);

				  $result[] = array(
					  'id'          => $project_id,
					  'type'        => 'Project',
					  'name'        => $subobject['name'],
					  'color'       => array_var($config, 'color', Calendar::DEFAULT_COLOR),
					  'visible'     => array_var($config, 'visible', 1),
					  'permissions' => array(
						  'can_edit'          => true,
						  'can_remove'        => false,
					  ),
					  'urls'        => array(
						  'edit'              => str_replace($project_id_prefix_pattern, $project_id, $change_color_url_pattern),
						  'change_visibility' => str_replace($project_id_prefix_pattern, $project_id, $change_visibility_url_pattern),
						  'ical'              => str_replace($project_slug_prefix_pattern, $project_slug, $project_ical_subscribe_url_pattern)
					  )
				  );
			  } // foreach
		  } // if

		  return $result;
	  } // findForCalendarList

    /**
     * Finds projects for quick add for $user
     *
     * @param User $user
     * @return Project[]
     */
    static function findForQuickAdd(User $user) {
      $projects_table = TABLE_PREFIX . 'projects';
      $project_users_table = TABLE_PREFIX . 'project_users';
      $favorites_table = TABLE_PREFIX .'favorites';

      // first we find ids of favorite projects
      $favorite_projects = DB::executeFirstColumn("SELECT parent_id AS project_id from $favorites_table WHERE parent_type = ? AND user_id = ?", 'Project', $user->getId());
      // then we use those ids as condition to determine if project is favorite project
      return Projects::findBySQL("SELECT $projects_table.*, ($projects_table.id IN (?)) as is_favorite FROM $projects_table, $project_users_table WHERE $project_users_table.user_id = ? AND $project_users_table.project_id = $projects_table.id AND $projects_table.state >= ? AND $projects_table.completed_on IS NULL ORDER BY is_favorite DESC, $projects_table.name", $favorite_projects, $user->getId(), STATE_VISIBLE);
    } // findForQuickAdd

    /**
     * Find for main menu
     *
     * @param User $user
     * @return array
     */
    static function findForMainMenu(User $user) {
      $grouping = array();

      // client map
      $owner_company = Companies::findOwnerCompany();
      $grouping['company_id'] = Companies::getIdNameMap();
      unset($grouping['company_id'][$owner_company->getId()]);
      $grouping['company_id'][0] = lang('Internal');
      $grouping['company_id'] = 'new App.Map(' . JSON::map($grouping['company_id'], $user) . ')';

      // categories map
      $grouping['category_id'] = Categories::getIdNameMap(null, 'ProjectCategory');
      $grouping['category_id'][0] = lang('Uncategorized');
      $grouping['category_id'] = 'new App.Map(' . JSON::map($grouping['category_id'], $user) . ')';

      // labels map
      $grouping['label_id'] = Labels::getIdNameMap('ProjectLabel');
      $grouping['label_id'][0] = lang('No Label');
      $grouping['label_id'] = 'new App.Map(' . JSON::map($grouping['label_id'], $user) . ')';

      // get the projects cache
      $return = AngieApplication::cache()->getByObject($user, 'main_menu', function() use ($user, $grouping) {
        $projects_tabs_map = array();
        $projects_prepared = array();

        $projects = Projects::findForQuickAdd($user); // then we use those ids as condition to determine if project is favorite project
        if ($projects) {
          foreach ($projects as $project) {
            $tabs = $project->getTabs($user);

            if ($tabs) {
              $prepared_project = array(
                'id'          => $project->getId(),
                'name'        => $project->getName(),
                'company_id'  => (int) $project->getCompanyId(),
                'category_id' => (int) $project->getCategoryId(),
                'label_id'    => (int) $project->getLabelId(),
                'avatar'      => array(
                  'small'         => $project->avatar()->getUrl(IProjectAvatarImplementation::SIZE_SMALL)
                ),
                'urls'        => array(
                  'view'          => $project->getViewUrl()
                )
              );

              $projects_prepared[] = $prepared_project;
              $projects_tabs_map[$project->getId()] = $tabs->toArray();
            } // if
          } // foreach
        } // if

        return array($projects_prepared, $projects_tabs_map, $grouping);
      });

      return $return;
    } // findForMainMenu
    
    /**
     * Return first active project by given user
     *
     * This function is used by start page functionality
     *
     * @param User $user
     * @return Project
     */
    static function findFirstActiveProjectByUser($user) {
      $projects_table = TABLE_PREFIX . 'projects';
      $project_users_table = TABLE_PREFIX . 'project_users';
      
      return Projects::findOneBySQL("SELECT $projects_table.* FROM $projects_table, $project_users_table WHERE $project_users_table.user_id = ? AND $project_users_table.project_id = $projects_table.id AND $projects_table.completed_on IS NULL ORDER BY $projects_table.name LIMIT 0, 1", $user->getId());
    } // findFirstActiveProjectByUser
    
    /**
     * Return project ID-s by conditions
     * 
     * @param IUser $user
     * @param string $additional_conditions
     * @param boolean $all_for_admins_and_pms
     * @return array
     */
    static function findIdsByUser(IUser $user, $all_for_admins_and_pms = false, $additional_conditions = null) {
      $projects_table = TABLE_PREFIX . 'projects';
      $project_users_table = TABLE_PREFIX . 'project_users';
      
      if($all_for_admins_and_pms && $user->isProjectManager()) {
        $conditions = $additional_conditions ? "WHERE $additional_conditions" : '';
        
        return DB::executeFirstColumn("SELECT id FROM $projects_table $conditions ORDER BY name");
      } // if
      
      $conditions = array(DB::prepare("$project_users_table.user_id = ? AND $project_users_table.project_id = $projects_table.id", $user->getId()));
      if($additional_conditions) {
        $conditions[] = "($additional_conditions)";
      } // if
      
      $conditions = implode(' AND ', $conditions);
      
      return DB::executeFirstColumn("SELECT $projects_table.id FROM $projects_table, $project_users_table WHERE $conditions ORDER BY $projects_table.name");
    } // findIdsByUser

    /**
     * Return changed project ID-s since last synchronization
     *
     * @param User $user
     * @param integer $last_sync
     * @return array
     */
    static function findIdsByLastSync(User $user, $last_sync) {
      $result = array();

      if(!is_null($last_sync)) {
        $last_sync_date = DateTimeValue::makeFromTimestamp($last_sync);

        $available_project_ids = Projects::findIdsByUser($user, true, 'state >= ' . STATE_ARCHIVED);
        if(is_foreachable($available_project_ids)) {
          $parents_map = array();

          foreach($available_project_ids as $available_project_id) {
            $available_project = Projects::findById($available_project_id);

            if($available_project instanceof Project) {
              Milestones::findForExport($available_project, $user, $parents_map, null);
              Tasks::findForExport($available_project, $user, $parents_map, null);
              Discussions::findForExport($available_project, $user, $parents_map, null);
              ProjectAssets::findForExport($available_project, $user, $parents_map, null);
              Notebooks::findForExport($available_project, $user, $parents_map, null);
              NotebookPages::findForExport($available_project, $user, $parents_map, null);
            } // if
          } // foreach

          $projects_table = TABLE_PREFIX . 'projects';
          $project_objects_table = TABLE_PREFIX . 'project_objects';

          $project_object_conditions = array();
          foreach($parents_map as $type => $ids) {
            $project_object_conditions[] = DB::prepare("($project_objects_table.id IN (?) AND $project_objects_table.type = ?)", $ids, $type);
          } // if
          $project_object_conditions = implode(' OR ', $project_object_conditions);

          // Check project objects (Milestones, Tasks, Discussions, Assets, Notebooks)
          $updated_project_ids = DB::executeFirstColumn("SELECT DISTINCT $projects_table.id FROM $projects_table JOIN $project_objects_table ON $projects_table.id = $project_objects_table.project_id WHERE ($project_object_conditions) AND $project_objects_table.state >= ? AND $project_objects_table.visibility >= ? AND ($project_objects_table.created_on > '$last_sync_date' OR $project_objects_table.updated_on > '$last_sync_date')", STATE_ARCHIVED, $user->getMinVisibility());
          if(!is_foreachable($updated_project_ids)) {
            $updated_project_ids = array();
          } // if

          $updated_parent_project_ids = array();

          if(is_foreachable($parents_map)) {
            $changed_parent_ids = array();

            // Check Notebook Pages
            if(isset($parents_map['Notebook']) && count($parents_map['Notebook'])) {
              foreach($parents_map['Notebook'] as $notebook_id) {
                $notebook_page_ids = NotebookPages::getAllIdsByNotebook($notebook_id);

                if($notebook_page_ids && is_foreachable($notebook_page_ids)) {
                  $notebook_page_conditions = array(DB::prepare('(id IN (?))', $notebook_page_ids));
                  $notebook_page_conditions = implode(' OR ', $notebook_page_conditions);

                  $notebook_pages = DB::executeFirstColumn("SELECT id FROM " . TABLE_PREFIX . "notebook_pages WHERE ($notebook_page_conditions) AND state >= ? AND (created_on > '$last_sync_date' OR updated_on > '$last_sync_date')", STATE_ARCHIVED);
                  if(is_foreachable($notebook_pages)) {
                    $changed_parent_ids[] = (integer) $notebook_id;
                  } // if
                } // if
              } // foreach
            } // if

            $conditions = array();
            foreach($parents_map as $type => $ids) {
              $conditions[] = DB::prepare('(parent_type = ? AND parent_id IN (?))', $type, $ids);
            } // if
            $conditions = implode(' OR ', $conditions);

            // Check Comments
            $comments = DB::execute("SELECT id, parent_type, parent_id FROM " . TABLE_PREFIX . "comments WHERE ($conditions) AND state >= ? AND (created_on > '$last_sync_date' OR updated_on > '$last_sync_date')", STATE_ARCHIVED);
            if($comments instanceof DBResult) {
              foreach($comments as $comment) {
                if($comment['parent_type'] != 'NotebookPage') {
                  $changed_parent_ids[] = (integer) $comment['parent_id'];
                } else {
                  $notebook_page = DB::executeFirstColumn("SELECT parent_id FROM " . TABLE_PREFIX . "notebook_pages WHERE id = ? AND parent_type = ?", (integer) $comment['parent_id'], 'Notebook');
                  if($notebook_page) {
                    $changed_parent_ids[] = (integer) $notebook_page[0];
                  } // if
                } // if
              } // foreach
            } // if

            // Check Subtasks
            $subtasks = DB::execute("SELECT id, parent_type, parent_id FROM " . TABLE_PREFIX . "subtasks WHERE ($conditions) AND state >= ? AND created_on > '$last_sync_date'", STATE_ARCHIVED);
            if($subtasks instanceof DBResult) {
              foreach($subtasks as $subtask) {
                $changed_parent_ids[] = (integer) $subtask['parent_id'];
              } // foreach
            } // if

            if(is_foreachable($changed_parent_ids)) {
              $updated_parent_project_ids = DB::executeFirstColumn("SELECT DISTINCT $projects_table.id FROM $projects_table JOIN $project_objects_table ON $projects_table.id = $project_objects_table.project_id WHERE $project_objects_table.id IN (?)", implode(', ', array_unique($changed_parent_ids)));
            } // if
          } // if

          $project_ids = array_unique(array_merge($updated_project_ids, $updated_parent_project_ids));

          if(is_foreachable($project_ids)) {
            $result = $project_ids;
          } // if
        } // if
      } // if

      return $result;
    } // findIdsByLastSync

    /**
     * Return contexts by user
     *
     * If $project_ids is null, system will return contexts from all projects
     *
     * @param IUser $user
     * @param array $contexts
     * @param array $ignore_contexts
     * @param array $project_ids
     */
    static function getContextsByUser(IUser $user, &$contexts, &$ignore_contexts, $project_ids = null) {
      if($project_ids && !is_array($project_ids)) {
        $project_ids = array($project_ids);
      } // if
      
      if($user instanceof User) {
        if($user->isProjectManager()) {
          if($project_ids) {
            foreach($project_ids as $project_id) {
              $contexts[] = "projects:projects/$project_id";
              $contexts[] = "projects:projects/$project_id/%";
            } // foreach
          } else {
            $contexts[] = 'projects:projects/%';
          } // if
        } else {
          $projects_table = TABLE_PREFIX . 'projects';
          $project_users_table = TABLE_PREFIX . 'project_users';
          
          if($project_ids) {
            $rows = DB::execute("SELECT $project_users_table.project_id, $projects_table.leader_id, $project_users_table.role_id, $project_users_table.permissions FROM $project_users_table, $projects_table WHERE $project_users_table.project_id = $projects_table.id AND $projects_table.id IN (?) AND $projects_table.state >= ? AND $project_users_table.user_id = ?", $project_ids, STATE_ARCHIVED, $user->getId());
          } else {
            $rows = DB::execute("SELECT $project_users_table.project_id, $projects_table.leader_id, $project_users_table.role_id, $project_users_table.permissions FROM $project_users_table, $projects_table WHERE $project_users_table.project_id = $projects_table.id AND $projects_table.state >= ? AND $project_users_table.user_id = ?", STATE_ARCHIVED, $user->getId());
          } // if
          
          if($rows) {
            $roles = array();
            
            $role_rows = DB::execute('SELECT id, permissions FROM ' . TABLE_PREFIX . 'project_roles');
            if($role_rows) {
              foreach($role_rows as $role_row) {
                $roles[(integer) $role_row['id']] = $role_row['permissions'] ? unserialize($role_row['permissions']) : null;
              } // foreach
            } // if
            
            $context_permission_map = Projects::getProjectSubcontextPermissionsMap();
            $can_see_private = $user->canSeePrivate();
            
            foreach($rows as $row) {
              if($user->getId() == $row['leader_id']) {
                $contexts[] = "projects:projects/$row[project_id]";
                $contexts[] = "projects:projects/$row[project_id]/%";
              } else {
                $visible_project_contexts = array();
                
                if($row['role_id']) {
                  $permissions = isset($roles[$row['role_id']]) ? $roles[$row['role_id']] : null;
                } else {
                  $permissions = $row['permissions'] ? unserialize($row['permissions']) : null;
                } // if
                
                foreach($context_permission_map as $context => $permission) {
                  if($permissions && isset($permissions[$permission]) && $permissions[$permission] >= ProjectRole::PERMISSION_ACCESS) {
                    $visible_project_contexts[] = $can_see_private ? "projects:projects/$row[project_id]/$context/%" : "projects:projects/$row[project_id]/$context/normal/%";
                  } // if
                } // foreach
                
                // All contexts in this project?
                if(count($visible_project_contexts) == count($context_permission_map)) {
                  $contexts[] = "projects:projects/$row[project_id]";
                  $contexts[] = "projects:projects/$row[project_id]/%";

                  if(!$can_see_private) {
                    $ignore_contexts[] = "projects:projects/$row[project_id]/%/private/%";
                  } // if
                  
                // Just specific contexts in this project
                } else {
                  $contexts[] = "projects:projects/$row[project_id]";
                  $contexts = array_merge($contexts, $visible_project_contexts);
                  
                  // Ignore time tracking data
                  if(empty($permissions) || !isset($permissions['tracking']) || $permissions['tracking'] < ProjectRole::PERMISSION_ACCESS) {
                    if($permissions && isset($permissions['task']) && $permissions['task'] >= ProjectRole::PERMISSION_ACCESS) {
                      $ignore_contexts[] = "projects:projects/$row[project_id]/tasks/%/tracking/%";
                    } // if
                    
                    if($permissions && isset($permissions['todo_list']) && $permissions['todo_list'] >= ProjectRole::PERMISSION_ACCESS) {
                      $ignore_contexts[] = "projects:projects/$row[project_id]/todo/%/tracking/%";
                    } // if
                  } // if
                } // if
              } // foreach
            } // if
          } // if
        } // if
      } // if
    } // getContextsByUser
    
    /**
     * Return number of projects that use given currency
     * 
     * @param Currency $currency
     * @return integer
     */
    static function countByCurrency(Currency $currency) {
      if($currency->getIsDefault()) {
        return Projects::count(array('currency_id IS NULL OR currency_id = ?', $currency->getId()));
      } else {
        return Projects::count(array('currency_id = ?', $currency->getId()));
      } // if
    } // countByCurrency
    
    /**
     * Return ID name by given set of project IDs
     * 
     * @param array $ids
     * @return array
     */
    static function getIdNameMapByIds($ids) {
      $result = array();
      
      if($ids) {
        $rows = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'projects ORDER BY name');
        
        if($rows) {
          foreach($rows as $row) {
            $result[(integer) $row['id']] = $row['name'];
          } // foreach
        } // if
      } // if
      
      return $result;
    } // getIdNameMapByIds
    
    /**
     * Return project users IDs map
     * 
     * @param void
     * @return array
     */
    static function getProjectUsersIdMap() {
    	$projects_table = TABLE_PREFIX . 'projects';
      $project_users_table = TABLE_PREFIX . 'project_users';
    	
      $result = array();
      
      $rows = DB::execute("SELECT $project_users_table.user_id, $project_users_table.project_id, $projects_table.name FROM $projects_table, $project_users_table WHERE $projects_table.id = $project_users_table.project_id");
      if($rows) {
        foreach($rows as $row) {
          $result[(integer) $row['user_id']][(integer) $row['project_id']] = $row['name'];
        } // foreach
      } // if
      
      return $result;
    } // getProjectUsersIdMap

    /**
     * Return ID Details map
     *
     * @param array $ids
     * @param array $fields
     * @param mixed $additional_conditions
     * @return array
     */
    static function getIdDetailsMap($fields, $ids = null, $additional_conditions = null) {
      $fields = (array) $fields;

      if(!in_array('id', $fields)) {
        $fields[] = 'id';
      } // if

      $conditions = array();

      if($ids) {
        $conditions[] = DB::prepare('(id IN (?))', $ids);
      } // if

      if($additional_conditions) {
        $conditions[] = '(' .  DB::prepareConditions($additional_conditions) . ')';
      } // if

      $rows = DB::execute('SELECT ' . implode(', ', $fields) . ' FROM ' . TABLE_PREFIX . 'projects ' . (count($conditions) ? ' WHERE ' . implode(' AND ', $conditions) : '') . ' ORDER BY id');

      if($rows) {
        $rows->setCasting(array(
          'id' => DBResult::CAST_INT,
        ));

        $result = array();
        foreach ($rows as $row) {
          $project_id = $row['id'];
          unset($row['id']);

          $result[$project_id] = $row;
        } // foreach

        return $result;
      } else {
        return null;
      } // if
    } // getIdDetailsMap
    
    /**
     * Return ID name map for all project that $user is involved with
     *
     * @param User $user
     * @param integer $min_state
     * @param array $exclude_ids
     * @param mixed $additional_conditions
     * @param boolean $all_for_admins_and_pms
     * @return array
     */
    static function getIdNameMap(User $user, $min_state = STATE_ARCHIVED, $exclude_ids = null, $additional_conditions = null, $all_for_admins_and_pms = false) {
      $conditions = array(DB::prepare('(state >= ?)', $min_state));

      if($exclude_ids) {
        $conditions[] = DB::prepare('(projects.id NOT IN (?))', $exclude_ids);
      } // if

      $conditions = implode(' AND ', $conditions);
      if($additional_conditions) {
        $conditions .= " AND $additional_conditions";
      } // if
      
      return self::_getIdNameMap($user, $all_for_admins_and_pms, $conditions);
    } // getIdNameMap
    
    /**
     * Return ID name map for active project that $user is involved with
     *
     * @param User $user
     * @param array $exclude_ids
     * @param mixed $additional_conditions
     * @param boolean $all_for_admins_and_pms
     * @return array
     */
    static function getActiveIdNameMap(User $user, $exclude_ids = null, $additional_conditions = null, $all_for_admins_and_pms = false) {
      if($additional_conditions) {
        $additional_conditions .= ' AND ' . TABLE_PREFIX . 'projects.completed_on IS NULL';
      } else {
        $additional_conditions = TABLE_PREFIX . 'projects.completed_on IS NULL';
      } // if
      
      if($exclude_ids) {
        $additional_conditions .= ' AND ' . DB::prepare(TABLE_PREFIX . 'projects.id NOT IN (?)', $exclude_ids);
      } // if
      
      return self::_getIdNameMap($user, $all_for_admins_and_pms, $additional_conditions);
    } // getActiveIdNameMap
    
    /**
     * Return ID => name map for a given $user
     *
     * @param User $user
     * @param boolean $all_for_admins_and_pms
     * @param mixed $additional_conditions
     * @return array
     */
    private static function _getIdNameMap(User $user, $all_for_admins_and_pms = false, $additional_conditions = null) {
      $projects_table = TABLE_PREFIX . 'projects';
      $project_users_table = TABLE_PREFIX . 'project_users';
      
      if($additional_conditions) {
        $additional_conditions = DB::prepareConditions($additional_conditions);
      } // if
      
      if($all_for_admins_and_pms && ($user->isAdministrator() || $user->isProjectManager())) {
        if($additional_conditions) {
          $rows = DB::execute("SELECT $projects_table.id, $projects_table.name FROM $projects_table WHERE $additional_conditions ORDER BY $projects_table.name");
        } else {
          $rows = DB::execute("SELECT $projects_table.id, $projects_table.name FROM $projects_table ORDER BY $projects_table.name");
        } // if
      } else {
        if($additional_conditions) {
          $rows = DB::execute("SELECT $projects_table.id, $projects_table.name FROM $projects_table, $project_users_table WHERE $project_users_table.user_id = ? AND $project_users_table.project_id = $projects_table.id AND $additional_conditions ORDER BY $projects_table.name", $user->getId());
        } else {
          $rows = DB::execute("SELECT $projects_table.id, $projects_table.name FROM $projects_table, $project_users_table WHERE $project_users_table.user_id = ? AND $project_users_table.project_id = $projects_table.id ORDER BY $projects_table.name", $user->getId());
        } // if
      } // if
      
      $result = array();
      if(is_foreachable($rows)) {
        foreach($rows as $row) {
          $result[(integer) $row['id']] = $row['name'];
        } // foreach
      } // if
      return $result;
    } // _getIdNameMap
    
    /**
     * Return ID - slug map
     * 
     * @param array $ids
     * @param boolean $use_cache
     * @return array
     */
    static function getIdSlugMap($ids = null, $use_cache = true) {
      $id_slug_map = AngieApplication::cache()->get(array('models', Projects::getModelName(true), 'id_slug_map'), function() {
        $rows = DB::execute('SELECT id, slug FROM ' . TABLE_PREFIX . 'projects ORDER BY slug');

        if($rows) {
          $result = array();

          foreach($rows as $row) {
            $result[(integer) $row['id']] = $row['slug'];
          } // foreach

          return $result;
        } // if

        return array();
      }, !$use_cache);

      if(is_foreachable($ids)) {
        $result = array();

        foreach($ids as $id) {
          if(isset($id_slug_map[$id])) {
            $result[$id] = $id_slug_map[$id];
          } // if
        } // foreach

        return $result;
      } else {
        return $id_slug_map;
      } // if
    } // getIdSlugMap
    
    /**
     * Return projects by user and company
     *
     * @param User $user
     * @param Company $company
     * @param boolean $all_for_admins_and_pms
     * @param mixed $additional_conditions
     * @param string $order_by
     * @return array
     */
    static function findByUserAndCompany(User $user, Company $company, $all_for_admins_and_pms = false, $additional_conditions = null, $order_by = null) {
      $company_id = $company->getIsOwner() ? array(0, $company->getId()) : $company->getId();

      $conditions = DB::prepare(TABLE_PREFIX . 'projects.company_id IN (?)', $company_id);

      if($additional_conditions) {
        $conditions = '(' . DB::prepareConditions($additional_conditions) . " AND $conditions)";
      } // if
      
      return self::_findByUser($user, $all_for_admins_and_pms, $conditions, $order_by);
    } // findByUserAndCompany
    
    /**
     * Return active projects by user and company
     *
     * @param User $user
     * @param Company $company
     * @param boolean $all_for_admins_and_pms
     * @param string|null $order_by
     * @return array
     */
    static function findActiveByUserAndCompany(User $user, Company $company, $all_for_admins_and_pms = false, $order_by = null) {
      $projects_table = TABLE_PREFIX . 'projects';
      $company_id = $company->getIsOwner() ? array(0, $company->getId()) : $company->getId();

      return Projects::findByUserAndCompany($user, $company, $all_for_admins_and_pms, DB::prepare("$projects_table.state >= ? AND $projects_table.completed_on IS NULL", STATE_VISIBLE, $company_id), $order_by);
    } // findActiveByUserAndCompany
    
    /**
     * Return completed projects by user and company
     *
     * @param User $user
     * @param Company $company
     * @param boolean $all_for_admins_and_pms
     * @param string|null $order_by
     * @return array
     */
    static function findCompletedByUserAndCompany(User $user, Company $company, $all_for_admins_and_pms = false, $order_by = null) {
      $projects_table = TABLE_PREFIX . 'projects';
      $company_id = $company->getIsOwner() ? array(0, $company->getId()) : $company->getId();

      return Projects::findByUserAndCompany($user, $company, $all_for_admins_and_pms, DB::prepare("$projects_table.state >= ? AND $projects_table.completed_on IS NOT NULL", STATE_VISIBLE, $company_id), $order_by);
    } // findCompletedByUserAndCompany

    /**
     * Return completed projects by user and company
     *
     * @param User $user
     * @param Company $company
     * @param boolean $all_for_admins_and_pms
     * @return array
     */
    static function findArchivedByUserAndCompany(User $user, Company $company, $all_for_admins_and_pms = false) {
      $company_id = $company->getIsOwner() ? array(0, $company->getId()) : $company->getId();
      return self::_findByUser($user, $all_for_admins_and_pms, DB::prepare(TABLE_PREFIX . 'projects.state = ? AND ' . TABLE_PREFIX . 'projects.company_id IN (?)', STATE_ARCHIVED, $company_id), "completed_on DESC");
    } // findCompletedByUserAndCompany
    
    /**
     * Return projects from a sepcific category
     *
     * @param IUser $user
     * @param ProjectCategory $category
     * @return array
     */
    static function findByCategory(IUser $user, ProjectCategory $category) {
      return Projects::find(array(
        'conditions' => array('category_id = ?', $category->getId()),
        'order_by' => 'created_on DESC'
      ));
    } // findByCategory
    
    /**
     * Return number of projects that are in given category
     * 
     * @param IUser $user
     * @param ProjectCategory $category
     * @return integer
     */
    static function countByCategory(IUser $user, ProjectCategory $category) {
      return Projects::count(array('category_id = ?', $category->getId()));
    } // countByCategory
    
    /**
     * Cached search context permission map
     *
     * @var array
     */
    static private $project_subcontext_permission_map = false;
    
    /**
     * Return search context permissions map
     * 
     * This function returns search context as a key and permissions name that 
     * user needs to have in order to be able to search projects in that context
     * 
     * @return array
     */
    static function getProjectSubcontextPermissionsMap() {
      if(self::$project_subcontext_permission_map === false) {
        self::$project_subcontext_permission_map = array('milestones' => 'milestone');
        
        EventsManager::trigger('on_project_subcontext_permission', array(&self::$project_subcontext_permission_map));
      } // if
      
      return self::$project_subcontext_permission_map;
    } // getProjectSubcontextPermissionsMap

    /**
     * Return projects slice based on given criteria
     *
     * @param integer $num
     * @param array $state
     * @param array $exclude
     * @param integer $timestamp
     * @return DBResult
     */
    static function sliceByStateForCleanup($num = 10, $state = array(STATE_DELETED, STATE_ARCHIVED), $exclude = null, $timestamp = null) {
      $projects_table = TABLE_PREFIX . 'projects';

      if(!is_array($state)) {
        $state = array($state);
      } // if

      if($exclude) {
        $projects = DB::execute("SELECT id, slug, state, name, completed_on FROM $projects_table WHERE state IN (?) AND id NOT IN (?) ORDER BY name LIMIT $num", $state, $exclude);
      } else {
        $projects = DB::execute("SELECT id, slug, state, name, completed_on FROM $projects_table WHERE state IN (?) ORDER BY name LIMIT $num", $state);
      } // if

      $result = array();

      if($projects instanceof DBResult) {
        $projects->setCasting(array(
          'id' => DBResult::CAST_INT,
          'state' => DBResult::CAST_INT,
          'completed_on' => DBResult::CAST_DATETIME
        ));

        $view_url = Router::assemble('project', array('project_slug' => '--PROJECT-SLUG--'));
        $permanently_delete_url = Router::assemble('admin_projects_data_cleanup_permanently_delete_project', array('project_slug' => '--PROJECT-SLUG--'));

        $exporter_url = '#';
        if(AngieApplication::isModuleLoaded('project_exporter')) {
          $exporter_url = Router::assemble('project_exporter', array('project_slug' => '--PROJECT-SLUG--'));
        } // if

        foreach($projects as $project) {
          $result[] = array(
            'id' => $project['id'],
            'name' => $project['name'],
            'state' => $project['state'],
            'disk_usage' => DiskSpace::getUsageByProjectId($project['id']),
            'urls' => array(
              'view' => str_replace('--PROJECT-SLUG--', $project['slug'], $view_url),
              'export' => str_replace('--PROJECT-SLUG--', $project['slug'], $exporter_url),
              'permanently_delete' => str_replace('--PROJECT-SLUG--', $project['slug'], $permanently_delete_url)
            )
          );
        } // foreach
      } // if

      return $result;
    } // sliceByStateForCleanup

    /**
     * Clean soft deleted projects up
     */
    static function cleanupSoftDeletedProjects() {
      $project = Projects::findOneBySql("SELECT id FROM " . TABLE_PREFIX . "projects WHERE state = ? AND updated_on IS NOT NULL AND updated_on NOT BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW() ORDER BY updated_on", STATE_DELETED);

      if($project instanceof Project) {
        $project->delete(true);
      } // if
    } // cleanupSoftDeletedProjects
    
    // ---------------------------------------------------
    //  Templates
    // ---------------------------------------------------
    
    /**
     * Return all project templates
     *
     * @return array
     */
    static function findTemplates() {
      return Projects::find(array(
        'conditions' => array('is_template = ?', true),
        'order' => 'name',
      ));
    } // findTemplates
    
    /**
     * Find all projects, and prepare them for objects list
     * 
     * @param User $user
     * @param int $state
     * @return array
     */
    static function findForObjectsList(User $user, $state = STATE_VISIBLE) {
      $project_url_brief = Router::assemble('project', array('project_slug' => '--PROJECTSLUG--', 'brief' => 1));
      $project_url = Router::assemble('project', array('project_slug' => '--PROJECTSLUG--'));

      $projects_table = TABLE_PREFIX . 'projects';
      $project_users_table = TABLE_PREFIX . 'project_users';

      $custom_fields = array();

      foreach(CustomFields::getEnabledCustomFieldsByType('Project') as $field_name => $details) {
        $custom_fields[] = $field_name;
      } // if

      $result = array();
      if($user->isProjectManager()) {
        $projects = DB::execute("SELECT * FROM $projects_table WHERE state = ? ORDER BY name", $state);
      } else {
        $projects = DB::execute("SELECT $projects_table.* FROM $projects_table, $project_users_table WHERE $project_users_table.user_id = ? AND $project_users_table.project_id = $projects_table.id AND $projects_table.state = ? ORDER BY $projects_table.name", $user->getId(), $state);
      } // if

      $labels = Labels::getIdDetailsMap('ProjectLabel');

      if ($projects instanceof DBResult) {
        $projects->setCasting(array(
          'id' => DBResult::CAST_INT,
          'category_id' => DBResult::CAST_INT,
          'label_id' => DBResult::CAST_INT,
          'company_id' => DBResult::CAST_INT
        ));
        foreach ($projects as $project) {
          if (is_null($project['completed_on'])) {
            list($total_assignments, $open_assignments) = ProjectProgress::getQuickProgress($project['id']);
          } else {
            $total_assignments = $open_assignments = 1; // trick for archived projects, they are 100% completed anyway
          } // if

          $result[] = array(
            'id'                    => $project['id'],
            'name'                  => $project['name'],
            'is_completed'          => (integer) !is_null($project['completed_on']),
            'category_id'           => $project['category_id'],
            'label_id'              => $project['label_id'],
            'company_id'            => $project['company_id'],
            'icon'                  => get_project_icon_url($project['id'], '16x16'),
            'permalink'             => str_replace('--PROJECTSLUG--', $project['slug'], $project_url_brief),
            'goto_url'              => str_replace('--PROJECTSLUG--', $project['slug'], $project_url),
            'is_favorite'           => Favorites::isFavorite(array('Project', $project['id']), $user),
            'total_assignments'     => $total_assignments,
            'open_assignments'      => $open_assignments,
            'label'                 => $project['label_id'] ? (isset($labels[$project['label_id']]) ? $labels[$project['label_id']] : null) : null,
          	'is_archived'           => $project['state'] == STATE_ARCHIVED ? 1 : 0
          );

          if(count($custom_fields)) {
            $last_record = count($result) - 1;

            foreach($custom_fields as $custom_field) {
              $result[$last_record][$custom_field] = $project[$custom_field] ? $project[$custom_field] : null;
            } // foreach
          } // if
        } // foreach
      } // if

      return $result;
    } // findForObjectsList
    
    /**
     * Find all projects and prepare them for quick tracking
     * 
     * @param User $user
     * @return array
     */
    static function findForQuickTracking(User $user) {
      $projects_table = TABLE_PREFIX . 'projects';
      $project_users_table = TABLE_PREFIX . 'project_users';
          
      $result = array();
      if($user->isProjectManager()) {
        $projects = Projects::findBySQL("SELECT * FROM $projects_table WHERE state >= ? AND $projects_table.completed_on IS NULL ORDER BY name", STATE_VISIBLE);
      } else {
        $projects = Projects::findBySQL("SELECT $projects_table.id, $projects_table.slug, $projects_table.name FROM $projects_table, $project_users_table WHERE $project_users_table.user_id = ? AND $project_users_table.project_id = $projects_table.id AND $projects_table.state >= ? AND $projects_table.completed_on IS NULL ORDER BY $projects_table.name", $user->getId(), STATE_VISIBLE);
      } // if

      if($projects instanceof DBResult) {
        foreach($projects as $project) {
          if($project->tracking()->canAdd($user)) {
            $result[] = array(
              'id' => $project->getId(),
              'slug' => $project->getSlug(),
              'name' => $project->getName(),
              'icon' => $project->avatar()->getUrl(IProjectAvatarImplementation::SIZE_BIG)
            );
          } // if
        } // foreach
      } // if

      return $result;
    } // findForQuickTracking

    /**
     * Find projects for phone view
     *
     * @param User $user
     * @param boolean $all_for_admins_and_pms
     * @param mixed $additional_conditions
     * @param string $order_by
     * @return array
     */
    static function findForPhone(User $user, $all_for_admins_and_pms = false, $additional_conditions = null, $order_by = null) {
      $projects = Projects::findByUser($user, $all_for_admins_and_pms, $additional_conditions, $order_by);

      if(is_foreachable($projects)) {
        $favorite = array();
        $not_favorite = array();

        foreach($projects as $project) {
          if(Favorites::isFavorite($project, $user)) {
            $favorite[] = $project;
          } else {
            $not_favorite[] = $project;
          } // if
        } // foreach

        return array($favorite, $not_favorite);
      } else {
        return null;
      } // if
    } // findForPhone
    
    /**
     * Finds projects for quick jump for $user
     * 
     * @param User $user
     * @param Project $project
     * @return array
     */
    static function findForPhoneProjectUsers(User $user, Project $project) {
    	$return = array();
    	
    	$project_users = $project->users()->describe($user, true, true);
    	if(is_foreachable($project_users)) {
    		foreach($project_users as $project_user) {
    			$return[$project_user['user']['company_id']][] = $project_user;
    		} // foreach
    	} // if
    	
    	return $return;
    } // findForPhoneProjectUsers
    
    /**
     * Find projects for printing by grouping and filtering criteria
     * 
     * @param User $user
     * @param integer $min_state
     * @param string $group_by
     * @param array $filter_by
     * @return DBResult
     */
    public static function findForPrint(User $user, $min_state = STATE_VISIBLE, $group_by = null, $filter_by = null) {

      $visible_project_ids = Projects::findIdsByUser($user, $user->isProjectManager());

      // no point to do anything else if there are no visible projects
      if (!$visible_project_ids) {
        return null;
      } // if

      // initial condition
      $conditions = array(DB::prepare("id IN (?)", $visible_project_ids));

      // initial condition
      $conditions[] = DB::prepare('(state = ?)', $min_state);
       
      if (!in_array($group_by, array('category_id','company_id','label_id'))) {
        $group_by = null;
      } // if
                
      // filter by completion status
      $filter_is_completed = array_var($filter_by, 'is_completed', null);

      if ($filter_is_completed === '0') {
        $conditions[] = DB::prepare('(completed_on IS NULL)', Project::STATUS_ACTIVE);
      } else if ($filter_is_completed === '1') {
        $conditions[] = DB::prepare('(completed_on IS NOT NULL)', Project::STATUS_COMPLETED);
      } // if

      // do find tasks
      $projects = Projects::find(array(
        'conditions' => implode(' AND ', $conditions),
        'order' => $group_by ? $group_by . ', name' : 'name'
      ));
     
      return $projects;
    } // findForPrint
    
    /**
     * Get trashed map
     * 
     * @param User $user
     * @return array
     */
    static function getTrashedMap($user) {
      return array(
        'project' => DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'projects WHERE state = ? ORDER BY updated_on DESC', STATE_TRASHED)
      );
    } // getTrashedMap
    
    /**
     * Find trashed projects
     * 
     * @param User $user
     * @param array $map
     * @return array
     */
    static function findTrashed(User $user, &$map) {
      $trashed_projects = DB::execute('SELECT id, name, slug FROM ' . TABLE_PREFIX . 'projects WHERE state = ? ORDER BY updated_on DESC', STATE_TRASHED);

      if ($trashed_projects) {
        $view_url = Router::assemble('project', array('project_slug' => '--PROJECT-SLUG--'));

        $items = array();
        foreach ($trashed_projects as $project) {
          $items[] = array(
            'id' => $project['id'],
            'name' => $project['name'],
            'type' => 'Project',
            'permalink' => str_replace('--PROJECT-SLUG--', $project['slug'], $view_url),
            'can_be_parent' => true
          );
        } // foreach

        return $items;
      } else {
        return null;
      } // if
    } // findTrashed

    /**
     * Delete trashed projects
     *
     * @return bool
     */
    static function deleteTrashed() {
      $projects = Projects::find(array(
        'conditions' => array('state = ?', STATE_TRASHED)
      ));

      if ($projects) {
        foreach ($projects as $project) {
          $project->state()->delete();
        } // foreach
      } // if

      return true;
    } // deleteTrashed
    
    /**
     * Return number of visible objects
     *
     * @param User $user
     * @return integer
     */
    static function countActive(User $user) {
      return (integer) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'projects WHERE state = ?', STATE_VISIBLE);
    } // countActive

    // ---------------------------------------------------
    //  DataFilter related
    // ---------------------------------------------------

    // Available project filters
    const PROJECT_FILTER_ANY = 'any';
    const PROJECT_FILTER_ACTIVE = 'active';
    const PROJECT_FILTER_COMPLETED = 'completed';
    const PROJECT_FILTER_CATEGORY = 'category';
    const PROJECT_FILTER_CLIENT = 'client';
    const PROJECT_FILTER_SELECTED = 'selected';

    /**
     * Return project ID-s based on project filter and given user
     *
     * @param DataFilter $filter
     * @param User $user
     * @param integer $min_state
     * @param string|null $additional_conditions
     * @return array
     * @throws InvalidInstanceError
     * @throws DataFilterConditionsError
     */
    static function getProjectIdsByDataFilter(DataFilter $filter, User $user, $min_state = STATE_ARCHIVED, $additional_conditions = null) {
      if($filter instanceof DataFilter && method_exists($filter, 'getProjectFilter') && method_exists($filter, 'getIncludeAllProjects')) {
        $projects_table = TABLE_PREFIX . 'projects';

        $include_all_projects = $filter->getIncludeAllProjects();

        if($additional_conditions) {
          $additional_conditions = DB::prepare("($projects_table.state >= ?) AND ($additional_conditions)", $min_state);
        } else {
          $additional_conditions = DB::prepare("$projects_table.state >= ?", $min_state);
        } // if

        switch($filter->getProjectFilter()) {

          // Go through all projects
          case self::PROJECT_FILTER_ANY:
            $project_ids = Projects::findIdsByUser($user, $include_all_projects, $additional_conditions);

            if(empty($project_ids)) {
              throw new DataFilterConditionsError('project_filter', self::PROJECT_FILTER_ANY, null, 'There are no projects in the database that current user can see');
            } // if

            break;

          // Go only through active projects
          case self::PROJECT_FILTER_ACTIVE:
            $project_ids = Projects::findIdsByUser($user, $include_all_projects, "($projects_table.completed_on IS NULL) AND ($additional_conditions)");

            if(empty($project_ids)) {
              throw new DataFilterConditionsError('project_filter', self::PROJECT_FILTER_ACTIVE, null, 'There are no active projects in the database that current user can see');
            } // if

            break;

          // Go through completed projects
          case self::PROJECT_FILTER_COMPLETED:
            $project_ids = Projects::findIdsByUser($user, $include_all_projects, "($projects_table.completed_on IS NOT NULL) AND ($additional_conditions)");

            if(empty($project_ids)) {
              throw new DataFilterConditionsError('project_filter', self::PROJECT_FILTER_COMPLETED, null, 'There are no completed projects in the database that current user can see');
            } // if

            break;

          // Filter by project client
          case self::PROJECT_FILTER_CLIENT:
            $project_client_id = $filter->getProjectClientId();

            if($project_client_id) {
              $project_ids = Projects::findIdsByUser($user, $include_all_projects, DB::prepare("($projects_table.company_id = ?) AND ($additional_conditions)", $project_client_id));

              if(empty($project_ids)) {
                throw new DataFilterConditionsError('project_filter', self::PROJECT_FILTER_CLIENT, $project_client_id, 'There are no projects for this client or user cant see any of the projects for this client');
              } // if
            } else {
              throw new DataFilterConditionsError('project_filter', self::PROJECT_FILTER_CLIENT, $project_client_id, 'Project client not selected');
            } // if

            break;

          // Filter by selected project category
          case self::PROJECT_FILTER_CATEGORY:
            $project_categories = Categories::getIdNameMap(null, 'ProjectCategory');

            if($project_categories) {
              $project_category_id = $filter->getProjectCategoryId();

              if($project_category_id) {
                $project_ids = Projects::findIdsByUser($user, $include_all_projects, DB::prepare("($projects_table.category_id = ?) AND ($additional_conditions)", $project_category_id));

                if(empty($project_ids)) {
                  throw new DataFilterConditionsError('project_filter', self::PROJECT_FILTER_CATEGORY, $project_category_id, 'Category is empty or user cant see any of the projects in it');
                } // if
              } else {
                throw new DataFilterConditionsError('project_filter', self::PROJECT_FILTER_CATEGORY, $project_category_id, 'Project category not selected');
              } // if
            } else {
              throw new DataFilterConditionsError('project_filter', self::PROJECT_FILTER_CATEGORY, $filter->getProjectCategoryId(), 'There are no project categories defined in the database');
            } // if

            break;

          // Filter by list of selected projects
          case self::PROJECT_FILTER_SELECTED:
            $selected_project_ids = $filter->getProjectIds();

            if(is_array($selected_project_ids) && count($selected_project_ids)) {
              $project_ids = Projects::findIdsByUser($user, $include_all_projects, DB::prepare("($projects_table.id IN (?)) AND ($additional_conditions)", $selected_project_ids));

              if(empty($project_ids)) {
                throw new DataFilterConditionsError('project_filter', self::PROJECT_FILTER_SELECTED, $selected_project_ids, "User can't access any of the selected projects");
              } // if
            } else {
              throw new DataFilterConditionsError('project_filter', self::PROJECT_FILTER_SELECTED, $selected_project_ids, 'Selected project IDs array is empty');
            } // if

            break;

          // Invalid filter value
          default:
            throw new DataFilterConditionsError('project_filter', $filter->getProjectFilter(), 'mixed', 'Unknown project filter');
        } // switch

        return $project_ids;
      } else {
        throw new InvalidInstanceError('filter', $filter, 'DataFilter', '$filter is required to be DataFilter instance with getProjectFilter() and getIncludeAllProjects() methods defined');
      } // if
    } // getMatchingProjectIds
    
  }