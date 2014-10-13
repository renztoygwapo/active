<?php

  /**
   * Project users context implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IProjectUsersContextImplementation extends IUsersContextImplementation {
    
    /**
     * Construct project users helper implementation
     *
     * @param Project $object
     * @throws InvalidInstanceError
     */
    function __construct(Project $object) {
      if($object instanceof Project) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'Project');
      } // if
    } // __construct
    
    /**
     * Cached values for isMember function
     *
     * @var array
     */
    private $project_members = array();
    
    /**
     * Return true if $user is member of this users context
     *
     * @param User $user
     * @param boolean $use_cache
     * @return boolean
     */
    function isMember(User $user, $use_cache = true) {
      $user_id = $user->getId();
      
      if($use_cache && isset($this->project_members[$user_id])) {
        return $this->project_members[$user_id];
      } // if
      
      $this->project_members[$user_id] = (boolean) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'project_users WHERE user_id = ? AND project_id = ?', $user_id, $this->object->getId());
      
      return $this->project_members[$user_id];
    } // isMember
    
    /**
     * Returns true if $user is leader of parent project
     * 
     * @param User $user
     * @return boolean
     */
    function isLeader(User $user) {
      return $user instanceof User && $this->object->getLeaderId() == $user->getId();
    } // isLeader
    
    /**
     * Cached array of users
     *
     * @var array
     */
    private $users = false;

    /**
     * Return users in given context
     *
     * @param User $user
     * @param integer $min_state
     * @return User[]
     */
    function get($user = null, $min_state = STATE_VISIBLE) {
      if($this->users === false) {
        $project = $this->object;
        $implementation = $this;

        $this->users = AngieApplication::cache()->getByObject($project, array('users', $min_state), function() use ($implementation, $project, $min_state) {
          $project_users_table = TABLE_PREFIX . 'project_users';
          $users_table = TABLE_PREFIX . 'users';

          $result = array();

          $project_users = Users::findBySQL("SELECT DISTINCT $users_table.* FROM $users_table JOIN $project_users_table ON $project_users_table.user_id = $users_table.id WHERE $project_users_table.project_id = ? AND $users_table.state >= ? ORDER BY CONCAT($users_table.first_name, $users_table.last_name, $users_table.email)", $project->getId(), $min_state);

          if ($project_users) {
            foreach ($project_users as $project_user) {
              $result[] = $project_user;
            } // foreach
          } // if

          // Correct the problem and add project leader
          if(count($result) < 1) {
            $leader = $project->getLeader();

            if($leader instanceof User) {
              $leader_permissions = array();

              foreach(ProjectRoles::getPermissions() as $k => $v) {
                $leader_permissions[$k] = ProjectRole::PERMISSION_MANAGE;
              } // foreach

              $implementation->add($leader, null, $leader_permissions);

              $result = array($leader);
            } // if
          } // if

          return count($result) ? $result : null;
        });
      } // if

      return $this->users;
    } // get
    
    /**
     * Count users in given context
     * 
     * @param User $user
     * @return integer
     */
    function count(User $user) {
      $project_users_table = TABLE_PREFIX . 'project_users';
      $users_table = TABLE_PREFIX . 'users';
      return (integer) DB::executeFirstCell("SELECT COUNT(user_id) FROM $project_users_table, $users_table WHERE $project_users_table.project_id = ? AND ($project_users_table.user_id = $users_table.id AND $users_table.state >= ?)", $this->object->getId(), STATE_VISIBLE);
    } // count
    
    /**
     * Return users for select box
     *
     * @param User $user
     * @param array $exclude_ids
     * @param integer $min_state
     * @return array
     */
    function getForSelect(User $user, $exclude_ids = null, $min_state = STATE_VISIBLE) {
      $project_users_table = TABLE_PREFIX . 'project_users';
      $users_table = TABLE_PREFIX . 'users';
      
      if($exclude_ids) {
        $conditions = DB::prepare("$users_table.id NOT IN (?) AND $users_table.state >= ? AND $project_users_table.user_id = $users_table.id AND $project_users_table.project_id = ?", $exclude_ids, $min_state, $this->object->getId());
      } else {
        $conditions = DB::prepare("$project_users_table.user_id = $users_table.id AND $project_users_table.project_id = ? AND $users_table.state >= ?", $this->object->getId(), $min_state);
      } // if
      
      return Users::getForSelectByConditions($conditions, $project_users_table);
    } // getForSelect
    
    /**
     * Return user ID-s in this context
     *
     * @param User $user
     * @return array
     */
    function getIds($user = null) {
      return DB::executeFirstColumn('SELECT user_id FROM ' . TABLE_PREFIX . 'project_users WHERE project_id = ?', $this->object->getId());
    } // getIds
    
    /**
     * Return ID name map of users for this project
     * 
     * If $user is present, system will return ID name map of users that $user 
     * can see and hide all the other users
     * 
     * @param User $user
     * @param integer $min_state
     * @return array
     */
    function getIdNameMap($user = null, $min_state = STATE_VISIBLE) {
      $project_id = $this->object->getId();
      $user_id = $user instanceof User ? $user->getId() : (integer) $user;

      return AngieApplication::cache()->getByObject($this->object, array('users', 'id_name_map', $user_id, $min_state), function() use ($project_id, $user, $min_state) {
        $users_table = TABLE_PREFIX . 'users';
        $project_users_table = TABLE_PREFIX . 'project_users';

        if($user instanceof User) {
          $rows = DB::execute("SELECT $users_table.id, $users_table.first_name, $users_table.last_name, $users_table.email FROM $users_table, $project_users_table WHERE $project_users_table.project_id = ? AND $users_table.id = $project_users_table.user_id AND $users_table.id IN (?)", $project_id, $user->visibleUserIds(null, $min_state));
        } else {
          $rows = DB::execute("SELECT $users_table.id, $users_table.first_name, $users_table.last_name, $users_table.email FROM $users_table, $project_users_table WHERE $project_users_table.project_id = ? AND $users_table.id = $project_users_table.user_id AND $users_table.state >= ?", $project_id, $min_state);
        } // if

        $result = array();

        if($rows) {
          foreach($rows as $row) {
            $result[(integer) $row['id']] = Users::getUserDisplayName($row);
          } // foreach
        } // if

        return $result;
      });
    } // getIdNameMap
    
    /**
     * Return users by company
     *
     * @param Company $company
     * @param User $user
     * @param integer $min_state
     * @return DBResult
     */
    function getByCompany(Company $company, User $user, $min_state = STATE_VISIBLE) {
      $users_table = TABLE_PREFIX . 'users';
      $project_users_table = TABLE_PREFIX . 'project_users';
      
      return Users::findBySQL("SELECT $users_table.* FROM $users_table, $project_users_table WHERE $users_table.company_id = ? AND $users_table.state >= ? AND $project_users_table.project_id = ? AND $project_users_table.user_id = $users_table.id", $company->getId(), $min_state, $this->object->getId());
    } // getByCompany
    
    /**
     * Return users by company for select box
     *
     * @param Company $company
     * @param User $user
     * @param mixed $exclude_ids
     * @param integer $min_state
     * @return array
     */
    function getByCompanyForSelect(Company $company, User $user, $exclude_ids = null, $min_state = STATE_VISIBLE) {
      $users_table = TABLE_PREFIX . 'users';
      $project_users_table = TABLE_PREFIX . 'project_users';
      
      if($exclude_ids) {
        $conditions = DB::prepare("$users_table.company_id = ? AND $users_table.state >= ? AND $project_users_table.project_id = ? AND $project_users_table.user_id = $users_table.id AND $users_table.id NOT IN (?)", $company->getId(), $min_state, $this->object->getId(), $exclude_ids);
      } else {
        $conditions = DB::prepare("$users_table.company_id = ? AND $users_table.state >= ? AND $project_users_table.project_id = ? AND $project_users_table.user_id = $users_table.id", $company->getId(), $min_state, $this->object->getId());
      } // if
      
      return Users::getForSelectByConditions($conditions, $project_users_table);
    } // getByCompanyForSelect
    
    /**
     * Add user to this context
     *
     * @param User $user
     * @param ProjectRole $role
     * @param array $permissions
     * @param boolean $bulk
     * @return User
     * @throws Exception
     */
    function add(User $user, $role = null, $permissions = null, $bulk = false) {
      if(!$this->isMember($user, false)) {
        try {
          DB::beginWork('Adding user to project @ ' . __CLASS__);
          
          if($role instanceof ProjectRole) {
            DB::execute('INSERT INTO ' . TABLE_PREFIX . 'project_users (user_id, project_id, role_id, permissions) VALUES (?, ?, ?, ?)', $user->getId(), $this->object->getId(), $role->getId(), serialize(null));
          } else {
            DB::execute('INSERT INTO ' . TABLE_PREFIX . 'project_users (user_id, project_id, role_id, permissions) VALUES (?, ?, ?, ?)', $user->getId(), $this->object->getId(), 0, serialize($permissions));
          } // if

          $this->project_members = array(); // Reset internal isMember cache

          if(empty($bulk)) {
            AngieApplication::cache()->removeByModel('users');
            AngieApplication::cache()->removeByModel('projects');
          } // if

          EventsManager::trigger('on_project_user_added', array($this->object, $user, $role, $permissions));
          
          DB::commit('User added to project @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to add user to project @ ' . __CLASS__);
          throw $e;
        } // try

        $user->projects()->clearProjectDataCache();
      } // if
      
      return $user;
    } // add
    
    /**
     * Update user permissions
     *
     * @param User $user
     * @param ProjectRole $role
     * @param array $permissions
     * @return User|null
     * @throws Exception
     */
    function update(User $user, $role = null, $permissions = null) {
      if($this->isMember($user, false)) {
        try {
          DB::beginWork('Updating user permissions @ ' . __CLASS__);
          
          if($role instanceof ProjectRole) {
            DB::execute('UPDATE ' . TABLE_PREFIX . 'project_users SET role_id = ?, permissions = ? WHERE user_id = ? AND project_id = ?', $role->getId(), serialize(null), $user->getId(), $this->object->getId());
          } else {
            DB::execute('UPDATE ' . TABLE_PREFIX . 'project_users SET role_id = ?, permissions = ? WHERE user_id = ? AND project_id = ?', 0, serialize($permissions), $user->getId(), $this->object->getId());
          } // if

          AngieApplication::cache()->removeByObject($user);

          EventsManager::trigger('on_project_user_updated', array($this->object, $user, $role, $permissions));
          
          DB::commit('User permissions updated @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to update user permissions @ ' . __CLASS__);
          throw $e;
        } // try

        $user->projects()->clearProjectDataCache();

        return $user;
      } else {
        return $this->add($user, $role, $permissions);
      } // if
    } // update
    
    /**
     * Remove user from this context
     *
     * @param User $user
     * @param User $by
     * @throws Exception
     */
    function remove(User $user, User $by) {
      if($this->isMember($user, $this->object)) {
        try {
          DB::beginWork('Removing user from project @ ' . __CLASS__);
          
          DB::execute('DELETE FROM ' . TABLE_PREFIX . 'project_users WHERE user_id = ? AND project_id = ?', $user->getId(), $this->object->getId());

          $this->project_members = array(); // Reset interal is member cache

          AngieApplication::cache()->removeByModel('users');
          AngieApplication::cache()->removeByModel('projects');

          $this->clearAssignmentsByUser($user, $by);
          $this->clearSubscriptionsByUser($user, $by);
          $this->clearRemindersByUser($user, $by);

          EventsManager::trigger('on_project_user_removed', array($this->object, $user));
          
          DB::commit('User removed from project @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to remove user from project @ ' . __CLASS__);
          
          throw $e;
        } // try

        $user->projects()->clearProjectDataCache();
      } // if
    } // remove

    /**
     * Remove all assignments for a given user
     *
     * @param User $user
     * @param User $by
     * @throws Exception
     */
    protected function clearAssignmentsByUser(User $user, User $by) {
      $parent_filter = $this->getParentFilter();

      if($parent_filter) {
        $project_objects_table = TABLE_PREFIX . 'project_objects';
        $assignments_table = TABLE_PREFIX . 'assignments';
        $subtasks_table = TABLE_PREFIX . 'subtasks';

        $user_id = $user->getId();

        try {
          DB::beginWork('Clearing assignments by user @ ' . __CLASS__);

          $log_values_batch = new DBBatchInsert(TABLE_PREFIX . 'modification_log_values', array('modification_id', 'field', 'value'));

          $created_by_id = DB::escape($by->getId());
          $created_by_name = DB::escape($by->getName());
          $created_by_email = DB::escape($by->getEmail());

          // Responsibilities
          $rows = DB::execute("SELECT id, type FROM $project_objects_table WHERE project_id = ? AND assignee_id = ?", $this->object->getId(), $user_id);
          if($rows) {
            $type_id_map = array();

            foreach($rows as $row) {
              $type = $row['type'];
              $id = (integer) $row['id'];

              if(isset($type_id_map[$type])) {
                $type_id_map[$type][] = $id;
              } else {
                $type_id_map[$type] = array($id);
              } // if

              // Lets insert info in moderation log
              DB::execute('INSERT INTO ' . TABLE_PREFIX . "modification_logs (parent_type, parent_id, created_on, created_by_id, created_by_name, created_by_email) VALUES (?, ?, UTC_TIMESTAMP(), $created_by_id, $created_by_name, $created_by_email)", $type, $id);
              $log_values_batch->insert(DB::lastInsertId(), 'assignee_id', null);
            } // foreach

            $responsible_parent_filter = array();

            foreach($type_id_map as $type => $ids) {
              $responsible_parent_filter[] = DB::prepare("(parent_type = ? AND parent_id IN (?))", $type, $ids);
            } // foreach

            $responsible_parent_filter = implode(' OR ', $responsible_parent_filter);

            DB::execute("UPDATE $project_objects_table SET assignee_id = NULL WHERE project_id = ? AND assignee_id = ?", $this->object->getId(), $user_id);

            // Lets get other assignees
            $other_assignees = DB::execute("SELECT parent_type, parent_id, user_id FROM $assignments_table WHERE $responsible_parent_filter");

            if($other_assignees) {
              $other_assignees->setCasting(array(
                'parent_id' => DBResult::CAST_INT,
                'user_id' => DBResult::CAST_INT,
              ));

              DB::execute("DELETE FROM $assignments_table WHERE $responsible_parent_filter");

              $conditions = array();
              foreach($other_assignees as $other_assignee) {
                $conditions[] = DB::prepare("(parent_type = ? AND parent_id = $other_assignee[parent_id] AND user_id = $other_assignee[user_id])", $other_assignee['parent_type']);
              } // foreach

              DB::execute('DELETE FROM ' . TABLE_PREFIX . 'subscriptions WHERE ' . implode(' OR ', $conditions));
              AngieApplication::cache()->removeByModel('users');
            } // if

            AngieApplication::cache()->removeByModel('project_objects');
          } // if

          // Other assignments cleanup
          DB::execute("DELETE FROM $assignments_table WHERE user_id = $user_id AND ($parent_filter)");

          // Remember that we removed assignees from subtasks
          $rows = DB::execute("SELECT id, type FROM $subtasks_table WHERE assignee_id = ? AND ($parent_filter)", $user_id);
          if($rows) {
            foreach($rows as $row) {
              DB::execute('INSERT INTO ' . TABLE_PREFIX . "modification_logs (parent_type, parent_id, created_on, created_by_id, created_by_name, created_by_email) VALUES (?, ?, UTC_TIMESTAMP(), $created_by_id, $created_by_name, $created_by_email)", $row['type'], $row['id']);
              $log_values_batch->insert(DB::lastInsertId(), 'assignee_id', null);
            } // if
          } // if

          // Subtask assignments
          DB::execute("UPDATE $subtasks_table SET assignee_id = NULL WHERE assignee_id = ? AND ($parent_filter)", $user_id);

          AngieApplication::cache()->removeByModel('subtasks');

          $log_values_batch->done();

          DB::commit('Assignments cleared by user @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to clear assignments by user @ ' . __CLASS__);
          throw $e;
        } // try
      } // if
    } // clearAssignmentsByUser

    /**
     * Remove all subscribptions for a given user
     *
     * @param User $user
     * @param User $by
     */
    protected function clearSubscriptionsByUser(User $user, User $by) {
      $parent_filter = $this->getParentFilter();
      if($parent_filter) {
        $user_id = $user->getId();

        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'subscriptions WHERE user_id = ? AND (' . $parent_filter . ')', $user_id);

        AngieApplication::cache()->removeByObject($user, 'subscriptions');
      } // if
    } // clearSubscriptionsByUser


    /**
     * Remove all reminders for specific user
     *
     * @param User $user
     * @param User $by
     */
    function clearRemindersByUser(User $user, User $by) {
      $user_reminders_table = TABLE_PREFIX . "reminder_users";
      $reminders_table = TABLE_PREFIX . "reminders";
      $project_objects_table = TABLE_PREFIX . "project_objects";

      // find specific reminders sent to this user

      $user_reminder_ids = DB::executeFirstColumn("SELECT reminder_id FROM $user_reminders_table WHERE user_id = ? AND dismissed_on IS NULL", $user->getId());
      $clear_user_reminders_table = is_foreachable($user_reminder_ids);
      if ($clear_user_reminders_table) {
        $user_reminder_ids_prepared = DB::prepareConditions(array("id IN (?) OR", $user_reminder_ids));
        $clear_user_reminders_table = true;
      } else {
        $user_reminder_ids_prepared = "";
      } // if

      $reminders = array();

      // find all objects related to reminders found, or 'selected user id' reminders
      $reminders_raw = DB::execute("SELECT parent_type, parent_id FROM $reminders_table WHERE $user_reminder_ids_prepared (selected_user_id = ? AND dismissed_on IS NULL)", $user->getId());
      if ($reminders_raw) {
        foreach ($reminders_raw as $reminder_raw) {
          if (!array_key_exists($reminder_raw['parent_type'], $reminders)) {
            $reminders[$reminder_raw['parent_type']] = array();
          } // if

          $reminders[$reminder_raw['parent_type']][] = $reminder_raw['parent_id'];
        } // foreach

        // filter objects that belong to this project only
        $objects_with_reminders = array();
        foreach ($reminders as $object_type => $object_ids) {
          if (is_foreachable($object_ids)) {
            $objects_with_reminders[$object_type] = DB::executeFirstColumn("SELECT id FROM $project_objects_table WHERE project_id = ? AND type = ? AND id IN (?)", $this->object->getId(), $object_type, array_unique($object_ids));
            // clean up in case result is null (none of the objects are in selected project)
            if (!is_foreachable($objects_with_reminders[$object_type])) {
              unset($objects_with_reminders[$object_type]);
            } // if
          } // if
        } // foreach

        // reuse old array and get only reminder ID's due for deletion
        $reminders = array();
        foreach ($objects_with_reminders as $object_type => $object_ids) {
          // delete "selected user" reminders that may exist
          if (is_foreachable($object_ids)) {
            DB::execute("DELETE FROM $reminders_table WHERE send_to = 'selected' AND selected_user_id = ? AND dismissed_on IS NULL AND parent_type = ? AND parent_id IN (?)", $user->getId(), $object_type, $object_ids);
          } // if

          if ($clear_user_reminders_table) {
            $reminders_for_object_type = DB::executeFirstColumn("SELECT id FROM $reminders_table WHERE dismissed_on IS NULL AND parent_type = ? AND parent_id IN (?)", $object_type, $object_ids);
            if (is_foreachable($reminders_for_object_type)) {
              $reminders = array_merge($reminders, $reminders_for_object_type);
            } // if
          } // if
        } // foreach

        if ($clear_user_reminders_table && is_foreachable($reminders)) {
          DB::execute("DELETE FROM $user_reminders_table WHERE user_id = ? AND reminder_id IN (?) AND dismissed_on IS NULL", $user->getId(), $reminders);
        } // if
      } // if
    } // clearRemindersByUser

    /**
     * Clear relations
     *
     * @param User $user
     */
    function clear(User $user) {
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'project_users WHERE project_id = ?', $this->object->getId());

      AngieApplication::cache()->removeByModel('users');
      AngieApplication::cache()->removeByObject($this->object, 'users');
    } // clear

    /**
     * Replace one user with another user
     *
     * @param User $replace
     * @param User $with
     * @param User $by
     * @throws InvalidParamError
     * @throws Exception
     */
    function replace(User $replace, User $with, User $by) {
      if($replace->getId() == $with->getId()) {
        return; // Can't replace one user with the same user
      } // if

      $row = DB::executeFirstRow('SELECT role_id, permissions FROM ' . TABLE_PREFIX . 'project_users WHERE user_id = ? AND project_id = ?', $replace->getId(), $this->object->getId());
      if($row) {
        try {
          $project_objects_table = TABLE_PREFIX . 'project_objects';
          $assignments_table = TABLE_PREFIX . 'assignments';
          $subtasks_table = TABLE_PREFIX . 'subtasks';

          DB::beginWork('Replacing user @ ' . __CLASS__);
          
          $role = $row['role_id'] ? ProjectRoles::findById($row['role_id']) : null;
          
          if($role instanceof ProjectRole) {
            $permissions = serialize(null);
          } else {
            $permissions = $row['permissions'];
          } // if
          
          $this->add($with, $role, $permissions);

          if($this->object->isLeader($replace)) {
            $this->object->setLeader($with);
            $this->object->save();
          } // if

          // Update subscriptions and other assignees
          $parent_filter = $this->getParentFilter();

          if($parent_filter) {
            $rememebered_subscriptions = $this->rememberUserSubscriptions($replace, $parent_filter);

            $log_values_batch = new DBBatchInsert(TABLE_PREFIX . 'modification_log_values', array('modification_id', 'field', 'value'));
            $created_by_id = DB::escape($by->getId());
            $created_by_name = DB::escape($by->getName());
            $created_by_email = DB::escape($by->getEmail());

            // Get all assignments where original user is responsible
            $rows = DB::execute("SELECT id, type FROM $project_objects_table WHERE project_id = ? AND assignee_id = ?", $this->object->getId(), $replace->getId());
            if($rows) {
              $by_type = array();

              foreach($rows as $row) {
                $type = $row['type'];
                $id = (integer) $row['id'];

                if(isset($by_type[$type])) {
                  $by_type[$type][] = $id;
                } else {
                  $by_type[$type] = array($id);
                } // if

                // Lets insert info in moderation log
                DB::execute('INSERT INTO ' . TABLE_PREFIX . "modification_logs (parent_type, parent_id, created_on, created_by_id, created_by_name, created_by_email) VALUES (?, ?, UTC_TIMESTAMP(), $created_by_id, $created_by_name, $created_by_email)", $type, $id);
                $log_values_batch->insert(DB::lastInsertId(), 'assignee_id', $with->getId());
              } // foreach

              $responsible_conditions = array();
              foreach($by_type as $type => $ids) {
                $responsible_conditions[] = DB::prepare('(parent_type = ? AND parent_id IN (?))', $type, $ids);
              } // foreach

              $responsible_conditions = implode(' OR ', $responsible_conditions);

              // Drop all assignments where replacement is already assigned as other assignee
              DB::execute("DELETE FROM $assignments_table WHERE user_id = ? AND ($responsible_conditions)", $with->getId());
            } // if

            // Get all assignments where new user is already responsible and remove replaced user from assignees list
            $rows = DB::execute("SELECT id, type FROM $project_objects_table WHERE assignee_id = ? AND project_id = ?", $with->getId(), $this->object->getId());
            if($rows) {
              $by_type = array();

              foreach($rows as $row) {
                $type = $row['type'];
                $id = (integer) $row['id'];

                if(isset($by_type[$type])) {
                  $by_type[$type][] = $id;
                } else {
                  $by_type[$type] = array($id);
                } // if
              } // foreach

              $responsible_conditions = array();
              foreach($by_type as $type => $ids) {
                $responsible_conditions[] = DB::prepare('(parent_type = ? AND parent_id IN (?))', $type, $ids);
              } // foreach

              DB::execute("DELETE FROM $assignments_table WHERE user_id = ? AND (" . implode(' OR ', $responsible_conditions) . ")", $replace->getId());
            } // if

            // Get all assignments where new user is already assigned to the parent task and remove duplicates
            $rows = DB::execute("SELECT parent_type, parent_id FROM $assignments_table WHERE user_id = ? AND ($parent_filter)", $with->getId());
            if($rows) {
              $by_type = array();

              foreach($rows as $row) {
                $type = $row['parent_type'];
                $id = (integer) $row['parent_id'];

                if(isset($by_type[$type])) {
                  $by_type[$type][] = $id;
                } else {
                  $by_type[$type] = array($id);
                } // if
              } // foreach

              $assignee_conditions = array();
              foreach($by_type as $type => $ids) {
                $assignee_conditions[] = DB::prepare('(parent_type = ? AND parent_id IN (?))', $type, $ids);
              } // foreach

              DB::execute("DELETE FROM $assignments_table WHERE user_id = ? AND (" . implode(' OR ', $assignee_conditions) . ")", $replace->getId());
            } // if

            // Get all subtasks where original user is assignee and update modification log
            $rows = DB::execute("SELECT id, type FROM $subtasks_table WHERE assignee_id = ? AND ($parent_filter)", $replace->getId());
            if($rows) {
              foreach($rows as $row) {
                DB::execute('INSERT INTO ' . TABLE_PREFIX . "modification_logs (parent_type, parent_id, created_on, created_by_id, created_by_name, created_by_email) VALUES (?, ?, UTC_TIMESTAMP(), $created_by_id, $created_by_name, $created_by_email)", $type, $id);
                $log_values_batch->insert(DB::lastInsertId(), 'assignee_id', $with->getId());
              } // foreach
            } // if

            DB::execute("UPDATE $project_objects_table SET assignee_id = ? WHERE project_id = ? AND assignee_id = ?", $with->getId(), $this->object->getId(), $replace->getId());

            $this->reassignUserSubscriptions($rememebered_subscriptions, $with);

            AngieApplication::cache()->removeByObject($replace, 'subscriptions');
            AngieApplication::cache()->removeByObject($with, 'subscriptions');

            DB::execute("UPDATE $subtasks_table SET assignee_id = ? WHERE assignee_id = ? AND ($parent_filter)", $with->getId(), $replace->getId());
            DB::execute("UPDATE $assignments_table SET user_id = ? WHERE user_id = ? AND ($parent_filter)", $with->getId(), $replace->getId());

            $log_values_batch->done();
          } // if
          
          $this->remove($replace, $by); // Remove old user (including user subscriptions)
          
          EventsManager::trigger('on_project_user_replaced', array($this->object, $replace, $with));

          AngieApplication::cache()->removeByModel('users');
          AngieApplication::cache()->removeByObject($this->object, 'users');
          
          DB::commit('User replaced @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to replace user @ ' . __CLASS__);
          throw $e;
        } // try
      } else {
        throw new InvalidParamError('replace', $replace, '$replace is not member of this project');
      } // if
    } // replace

    /**
     * Remember user subscriptions
     *
     * @param $user
     * @param string $parent_filter
     * @return array
     */
    private function rememberUserSubscriptions($user, $parent_filter) {
      $rows = DB::execute("SELECT parent_type, parent_id FROM " . TABLE_PREFIX . "subscriptions WHERE (user_id = ? OR user_email = ?) AND ($parent_filter)", $user->getId(), $user->getEmail());

      if($rows) {
        $by_type = array();

        foreach($rows as $row) {
          if(isset($by_type[$row['parent_type']])) {
            $by_type[$row['parent_type']][] = (integer) $row['parent_id'];
          } else {
            $by_type[$row['parent_type']] = array((integer) $row['parent_id']);
          } // if
        } // foreach

        return $by_type;
      } // if

      return null;
    } // rememberUserSubscriptions

    /**
     * Reassign remembered subscriptions to another user
     *
     * @param $remembered_subscriptions
     * @param $to_user
     */
    private function reassignUserSubscriptions($remembered_subscriptions, $to_user) {
      if($remembered_subscriptions) {
        $batch = new DBBatchInsert(TABLE_PREFIX . 'subscriptions', array('parent_type', 'parent_id', 'user_id', 'user_name', 'user_email'), 50, DBBatchInsert::REPLACE_RECORDS);

        $escaped_user_id = DB::escape($to_user->getId());
        $escaped_user_name = DB::escape($to_user->getDisplayName());
        $escaped_user_email = DB::escape($to_user->getEmail());

        foreach($remembered_subscriptions as $type => $ids) {
          $escaped_type = DB::escape($type);

          foreach($ids as $id) {
            $batch->insertEscapedArray(array($escaped_type, DB::escape($id), $escaped_user_id, $escaped_user_name, $escaped_user_email));
          } // foreach
        } // foreach

        $batch->done();
      } // if
    } // reassignUserSubscriptions
    
    /**
     * Clone users to a given project
     *
     * @param Project $to
     * @throws Exception
     */
    function cloneToProject(Project $to) {
      try {
        DB::beginWork('Cloning users to project @ ' . __CLASS__);
        
        $project_users_table = TABLE_PREFIX . 'project_users';
      
        $rows = DB::execute("SELECT user_id, role_id, permissions FROM $project_users_table WHERE project_id = ?", $this->object->getId());
        if($rows) {
          $target_project_id = DB::escape($to->getId());
          $to_insert = array();
          
          // Leader is probably already added to project so make sure that we 
          // skip users that are already involved with this project
          $already_added_user_ids = DB::executeFirstColumn("SELECT user_id FROM $project_users_table WHERE project_id = $target_project_id");
          
          foreach($rows as $row) {
            if($already_added_user_ids && in_array($row['user_id'], $already_added_user_ids)) {
              continue;
            } // if
            
            $to_insert[] = DB::prepare("(?, $target_project_id, ?, ?)", $row['user_id'], $row['role_id'], $row['permissions']);
          } // foreach
          
          if(count($to_insert)) {
            DB::execute("INSERT INTO $project_users_table (user_id, project_id, role_id, permissions) VALUES " . implode(', ', $to_insert));
            AngieApplication::cache()->removeByModel('users');
          } // if
        } // if
        
        DB::commit('Users cloned to project @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to clone users to project @ ' . __CLASS__);
        throw $e;
      } // try
    } // cloneToProject

    /**
     * Return number of assignments that given user is responsible for
     *
     * @param IUser $user
     * @param boolean $only_active
     * @return integer
     */
    function countResponsibilities(IUser $user, $only_active = false) {
      $parent_filter = $this->getParentFilter();

      if($only_active) {
        $assignments_count = (integer) DB::executeFirstCell("SELECT COUNT(*) AS total FROM " . TABLE_PREFIX . "project_objects WHERE state >= ? AND project_id = ? AND assignee_id = ?", STATE_VISIBLE, $this->object->getId(), $user->getId());

        if($parent_filter) {
          $assignments_count += (integer) DB::executeFirstCell("SELECT COUNT(*) AS total FROM " . TABLE_PREFIX . "subtasks WHERE state >= ? AND assignee_id = ? AND ({$parent_filter})", STATE_VISIBLE, $user->getId());
        } // if
      } else {
        $assignments_count = (integer) DB::executeFirstCell("SELECT COUNT(*) AS total FROM " . TABLE_PREFIX . "project_objects WHERE state >= ? AND project_id = ? AND assignee_id = ?", STATE_ARCHIVED, $this->object->getId(), $user->getId());

        if($parent_filter) {
          $assignments_count += (integer) DB::executeFirstCell("SELECT COUNT(*) AS total FROM " . TABLE_PREFIX . "subtasks WHERE state >= ? AND assignee_id = ? AND ({$parent_filter})", STATE_ARCHIVED, $user->getId());
        } // if
      } // if

      return $assignments_count;
    } // countResponsibilities

    /**
     * Returns true if $user has responsibilities in this project
     *
     * @param IUser $user
     * @param boolean $only_active
     * @return bool
     */
    function hasResponsibilities(IUser $user, $only_active = false) {
      return (boolean) $this->countResponsibilities($user, $only_active);
    } // hasResponsibilities

    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @param integer $min_state
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false, $min_state = STATE_VISIBLE) {
      $project_users_table = TABLE_PREFIX . 'project_users';
      $users_table = TABLE_PREFIX . 'users';

      $rows = DB::execute("SELECT $project_users_table.* FROM $project_users_table, $users_table WHERE $project_users_table.user_id = $users_table.id AND project_id = ? AND $users_table.state >= ? ORDER BY CONCAT($users_table.first_name, $users_table.last_name, $users_table.email)", $this->object->getId(), $min_state);
      
      if($rows) {
        $project_permissions = ProjectRoles::getPermissions();
        $result = array();
        
        foreach($rows as $row) {
          $role = $row['role_id'] ? ProjectRoles::findById($row['role_id']) : null;

          $project_user = array(
            'user_id' => (integer) $row['user_id'],
            'permissions' => array()
          );
          
          // Project role
          if($role instanceof ProjectRole) {
            $project_user['role_id'] = $role->getId();
            $project_user['role'] = $role->getName();
            
            foreach($project_permissions as $permission => $permission_text) {
              $project_user['permissions'][$permission] = $role->getPermissionValue($permission);
            } // foreach
            
          // Custom permissions
          } else {
            $project_user['role_id'] = 0;
            $project_user['role'] = lang('Custom');
            
            $project_user_permissions = $row['permissions'] ? unserialize($row['permissions']) : null;
            
            if(!is_array($project_user_permissions)) {
              $project_user_permissions = null;
            } // if
            
            foreach($project_permissions as $permission => $permission_text) {
              $project_user['permissions'][$permission] = $project_user_permissions && isset($project_user_permissions[$permission]) ? (integer) $project_user_permissions[$permission] : ProjectRole::PERMISSION_NONE;
            } // foreach
          } // if
          
          $result[] = $project_user;
          
          if($detailed) {
            foreach($result as $k => $v) {
              $project_user = $result[$k]['user_id'] ? Users::findById($result[$k]['user_id']) : null;
              
              if($project_user instanceof User) {
                $result[$k]['user'] = $project_user->describe($user, false, $for_interface);
              } else {
                $result[$k]['user'] = null;
              } // if
            } // foreach
          } // if
        } // foreach
        
        return $result;
      } // if
      
      return null;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param integer $min_state
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false, $min_state = STATE_VISIBLE) {
      $project_users_table = TABLE_PREFIX . 'project_users';
      $users_table = TABLE_PREFIX . 'users';

      $rows = DB::execute("SELECT $project_users_table.* FROM $project_users_table, $users_table WHERE $project_users_table.user_id = $users_table.id AND project_id = ? AND $users_table.state >= ? ORDER BY CONCAT($users_table.first_name, $users_table.last_name, $users_table.email)", $this->object->getId(), $min_state);

      if($rows) {
        $project_permissions = ProjectRoles::getPermissions();
        $result = array();

        foreach($rows as $row) {
          $role = $row['role_id'] ? ProjectRoles::findById($row['role_id']) : null;

          $project_user = array(
            'user_id' => (integer) $row['user_id'],
            'permissions' => array()
          );

          // Project role
          if($role instanceof ProjectRole) {
            $project_user['role_id'] = $role->getId();
            $project_user['role'] = $role->getName();

            foreach($project_permissions as $permission => $permission_text) {
              $project_user['permissions'][$permission] = $role->getPermissionValue($permission);
            } // foreach

            // Custom permissions
          } else {
            $project_user['role_id'] = 0;
            $project_user['role'] = lang('Custom');

            $project_user_permissions = $row['permissions'] ? unserialize($row['permissions']) : null;

            if(!is_array($project_user_permissions)) {
              $project_user_permissions = null;
            } // if

            foreach($project_permissions as $permission => $permission_text) {
              $project_user['permissions'][$permission] = $project_user_permissions && isset($project_user_permissions[$permission]) ? (integer) $project_user_permissions[$permission] : ProjectRole::PERMISSION_NONE;
            } // foreach
          } // if

          $result[] = $project_user;

          if($detailed) {
            foreach($result as $k => $v) {
              $project_user = $result[$k]['user_id'] ? Users::findById($result[$k]['user_id']) : null;

              if($project_user instanceof User) {
                $result[$k]['user'] = $project_user->describeForApi($user);
              } else {
                $result[$k]['user'] = null;
              } // if
            } // foreach
          } // if
        } // foreach

        return $result;
      } // if

      return null;
    } // describeForApi
    
    /**
     * Cached parent filter value
     *
     * @var string
     */
    private $parent_filter = false;
    
    /**
     * Return parent filter for this project
     *
     * @return string
     */
    private function getParentFilter() {
      if($this->parent_filter === false) {
        $project_objects = DB::execute("SELECT id, LOWER(type) AS 'type' FROM " . TABLE_PREFIX . "project_objects WHERE project_id = ?", $this->object->getId());
        if($project_objects) {
          $parents_by_type = array();
          
          foreach($project_objects as $project_object) {
            $type = $project_object['type'];
            
            if(isset($parents_by_type[$type])) {
              $parents_by_type[$type][] = $project_object['id'];
            } else {
              $parents_by_type[$type] = array($project_object['id']);
            } // if
          } // foreach
          
          foreach($parents_by_type as $type => $ids) {
            $parents_by_type[$type] = DB::prepare('(parent_type = ? AND parent_id IN (?))', $type, $ids);
          } // foreach
          
          $subtasks = DB::execute("SELECT id, LOWER(type) AS 'type' FROM " . TABLE_PREFIX . 'subtasks WHERE ' . implode(' OR ', $parents_by_type));
          if($subtasks) {
            foreach($subtasks as $subtask) {
              $type = $subtask['type'];

              if(isset($parents_by_type[$type])) {
                $parents_by_type[$type][] = $subtask['id'];
              } else {
                $parents_by_type[$type] = array($subtask['id']);
              } // if
            } // foreach
            
            foreach($parents_by_type as $type => $ids) {
              if(is_array($ids)) {
                $parents_by_type[$type] = DB::prepare('(parent_type = ? AND parent_id IN (?))', $type, $ids);
              } // if
            } // foreach
          } // if
          
          $this->parent_filter = implode(' OR ', $parents_by_type);
        } else {
          $this->parent_filter = '';
        } // if
      } // if
      
      return $this->parent_filter;
    } // getParentFilter
    
  }