<?php

  /**
   * Assignees interface implementation
   *
   * @package angie.frameworks.assignees
   * @subpackage models
   */
  class IAssigneesImplementation {
    
    /**
     * Parent object
     *
     * @var IAssignees
     */
    protected $object;
    
    /**
     * Construct assignees interface implementation instance
     *
     * @param IAssignees $object
     */
    function __construct(IAssignees &$object) {
      $this->object = $object;
    } // __construct
    
    /**
     * Returns true if $user is assigned to this object
     *
     * @param User $user
     * @return boolean
     */
    function isAssignee($user) {
      return Assignments::isAssignee($user, $this->object);
    } // isAssignee
    
    /**
     * Returns true if $user is set as responsible assignee for parent object
     *
     * @param User $user
     * @return boolean
     */
    function isResponsible(User $user) {
      return Assignments::isResponsible($user, $this->object);
    } // isResponsible

    /**
     * Switch whether this implementation supports other assignees or not
     *
     * @var bool
     */
    protected $support_multiple_assignees = true;

    /**
     * @return bool
     */
    function getSupportsMultipleAssignees() {
      return $this->support_multiple_assignees;
    } // getSupportsMultipleAssignees
    
    /**
     * Returns true if this object has assignee set
     *
     * @return boolean
     */
    function hasAssignee() {
      return $this->getAssignee() instanceof User;
    } // hasAssignee

    /**
     * Cached assignee
     *
     * @var User|bool|null
     */
    private $assignee = false;

    /**
     * Return assignee instance
     *
     * @return User|null
     */
    function getAssignee() {
      if($this->assignee === false) {
        $assignee = DataObjectPool::get('User', $this->object->getAssigneeId());

        if($assignee instanceof IUser && $assignee->getState() >= STATE_ARCHIVED) {
          $this->assignee = $assignee;
        } else {
          $this->assignee = null;
        } // if
      } // if

      return $this->assignee;
    } // getAssignee
    
    /**
     * Set assignee
     *
     * @param User|null $assignee
     * @param mixed $delegated_by
     * @param boolean $save
     */
    function setAssignee($assignee, $delegated_by = null, $save = true) {
      if($assignee instanceof User) {
        $this->assignee = false; // reset cache

        $this->object->setAssigneeId($assignee->getId());
        $this->setDelegatedBy($delegated_by);

        if($this->support_multiple_assignees) {
          DB::execute('DELETE FROM ' . TABLE_PREFIX . 'assignments WHERE parent_type = ? AND parent_id = ? AND user_id = ?', get_class($this->object), $this->object->getId(), $assignee->getId());
        } // if
      } elseif($assignee === null) {
        $this->object->setAssigneeId(null);
        $this->setDelegatedBy(null);
      } // if

      $this->assignee = $assignee;
      
      if($save) {
        $this->object->save();
      } // if
    } // setAssignee
    
    /**
     * All assignees
     *
     * @var array
     */
    private $all_assignees = false;

    /**
     * Return all object assignees
     *
     * @return IUser[]
     */
    function getAllAssignees() {
      if($this->all_assignees === false) {
        $this->all_assignees = array();

        if($this->object->getAssigneeId()) {
          if($this->support_multiple_assignees) {
            $assignments_table = TABLE_PREFIX . 'assignments';
            $users_table = TABLE_PREFIX . 'users';

            $this->all_assignees = Users::findBySQL("SELECT DISTINCT $users_table.* FROM $users_table JOIN $assignments_table ON $users_table.id = $assignments_table.user_id WHERE  $users_table.id = ? AND $users_table.state >= ? OR ($assignments_table.parent_type = ? AND $assignments_table.parent_id = ? AND $users_table.id = $assignments_table.user_id AND $users_table.state >= ?)", $this->object->getAssigneeId(), STATE_ARCHIVED, get_class($this->object), $this->object->getId(), STATE_ARCHIVED);
          } else {
            $assignee = $this->getAssignee();
            if($assignee instanceof User && $assignee->getState() >= STATE_ARCHIVED) {
              $this->all_assignees = array($assignee);
            } // if
          } // if
        } // if
      } // if

      return $this->all_assignees;
    } // getAllAssignees

    /**
     * Return all assignee ID-s
     *
     * @param boolean $use_cache
     * @return array
     */
    function getAllAssigneeIds($use_cache = true) {
      $assignee_id = $this->object->getAssigneeId();
      if($assignee_id) {
        $all_assignee_ids = array($assignee_id);

        $other_assignee_ids = $this->support_multiple_assignees ? $this->getOtherAssigneeIds($use_cache) : null;
        if($other_assignee_ids) {
          $all_assignee_ids = array_merge($all_assignee_ids, $other_assignee_ids);
        } // if
      } else {
        $all_assignee_ids = null;
      } // if

      return $all_assignee_ids;
    } // getAllAssigneeIds
    
    /**
     * Cached array of other assignees
     *
     * @var User[]
     */
    private $other_assignees = false;

    /**
     * Return other assignees
     *
     * @return User[]
     * @throws NotImplementedError
     */
    function getOtherAssignees() {
      if(empty($this->support_multiple_assignees)) {
        throw new NotImplementedError(__METHOD__);
      } // if

      if($this->other_assignees === false) {
        $users_table = TABLE_PREFIX . 'users';
        $assignments_table = TABLE_PREFIX . 'assignments';

        $this->other_assignees = Users::findBySQL("SELECT $users_table.* FROM $users_table JOIN $assignments_table ON $users_table.id = $assignments_table.user_id WHERE $assignments_table.parent_type = ? AND $assignments_table.parent_id = ? AND $users_table.state >= ? ORDER BY CONCAT($users_table.first_name, $users_table.last_name, $users_table.email)", get_class($this->object), $this->object->getId(), STATE_VISIBLE);
      } // if

      return $this->other_assignees;
    } // getOtherAssignees

    /**
     * Set other assignees
     *
     * @param mixed $other_assignees
     * @throws NotImplementedError
     * @throws InvalidParamError
     * @throws Exception
     */
    function setOtherAssignees($other_assignees) {
      if(empty($this->support_multiple_assignees)) {
        throw new NotImplementedError(__METHOD__);
      } // if

      if($this->getAssignee() instanceof User || !is_foreachable($other_assignees)) {
        //if empty array is passed then it is the delete of the current other assignees and it does not matter if responsible user is set or not
        try {
          $assignments_table = TABLE_PREFIX . 'assignments';
          $parent_type = get_class($this->object);
          $parent_id = $this->object->getId();
          
          DB::beginWork('Setting other assignees @ ' . __CLASS__);
          
          DB::execute("DELETE FROM $assignments_table WHERE parent_type = ? AND parent_id = ?", $parent_type, $parent_id);
          
          if(is_foreachable($other_assignees)) {
            
            $responsible_id = $this->getAssignee()->getId();
            
            foreach($other_assignees as $other_assignee) {
              $assignee_id = $other_assignee instanceof User ? $other_assignee->getId() : (integer) $other_assignee;
              
              if($assignee_id == $responsible_id) {
                continue;
              }//if
              
              if($assignee_id) {
                DB::execute("INSERT INTO $assignments_table (parent_type, parent_id, user_id) VALUES (?, ?, ?)", $parent_type, $parent_id, $assignee_id);
              } // if
            } // foreach
          } // if
          
          DB::commit('Other assignees set @ ' . __CLASS__);
          
          $this->clearOtherAssigneeCaches(); 
        } catch(Exception $e) {
          DB::rollback('Failed to set other assignees @ ' . __CLASS__);
          throw $e;
        } // try
        
      } else {
        throw new InvalidParamError('$this->getAssignee()', $this->getAssignee(), 'Set responsible user before setting other assignees');
      } // if
    } // setOtherAssignees
    
    /**
     * Return IDs of other assignees
     *
     * @var array
     */
    private $other_assignee_ids = false;

    /**
     * Return other assignee IDs
     *
     * @var boolean $use_cache
     *
     * @return array
     */
    function getOtherAssigneeIds($use_cache = true) {
      if ($this->support_multiple_assignees) {
        if(empty($use_cache) || $this->other_assignee_ids === false) {
          $users_table = TABLE_PREFIX . 'users';
          $assignments_table = TABLE_PREFIX . 'assignments';

          $this->other_assignee_ids = DB::executeFirstColumn("SELECT $users_table.id FROM $users_table, $assignments_table WHERE $users_table.id = $assignments_table.user_id AND $assignments_table.parent_type = ? AND $assignments_table.parent_id = ? AND $users_table.state >= ? ORDER BY CONCAT($users_table.first_name, $users_table.last_name, $users_table.email)", get_class($this->object), $this->object->getId(), STATE_ARCHIVED);
        } // if
      } else {
        $this->other_assignee_ids = array();
      } // if


      return $this->other_assignee_ids;
    } // getOtherAssigneeIds
    
    /**
     * Clear caches on other assignees change
     */
    protected function clearOtherAssigneeCaches() {
      $this->other_assignees = false;
      $this->other_assignee_ids = false;
      $this->all_assignees = false;
    } // clearOtherAssigneeCaches
    
    /**
     * Cached instance of user who delegated this assignment to assignees
     *
     * @var User
     */
    private $delegated_by = false;
    
    /**
     * Return user who delegated this assignment to assignees
     * 
     * @return User
     */
    function getDelegatedBy() {
      if($this->delegated_by === false) {
        $this->delegated_by = null;
        if ($this->object->getDelegatedById()) {
          $delegated_by = Users::findById($this->object->getDelegatedById());
          $this->delegated_by = $delegated_by instanceof IUser && $delegated_by->getState() >= STATE_ARCHIVED ? $delegated_by : null;
        } // if
      } // if
      
      return $this->delegated_by;
    } // getDelegatedBy
    
    /**
     * Set user who delegated this instance
     * 
     * @param User $user
     * @return User
     */
    function setDelegatedBy($user) {
      if($user instanceof User) {
        $this->object->setDelegatedById($user->getId());
      } elseif($user === null) {
        $this->object->setDelegatedById(null);
      } else {
        throw new InvalidInstanceError('user', $user, 'User', '$user can be User instance, or NULL');
      } // if
      
      $this->delegated_by = $user;
      
      return $this->delegated_by;
    } // setDelegatedBy
    
    /**
     * Return array of available users
     *
     * @param User $user
     * @return array
     */
    function getAvailableUsers(User $user) {
      return Users::find();
    } // getAvailableUsers
    
    /**
     * Return available users for select box
     *
     * @param User $user
     * @param mixed $exclude_ids
     * @param integer $min_state
     * @return array
     */
    function getAvailableUsersForSelect(User $user, $exclude_ids = null, $min_state = STATE_VISIBLE) {
      return Users::getForSelect($user, $exclude_ids, $min_state);
    } // getAvailableUsersForSelect

    /**
     * Clone all assignees from parent object to target object
     *
     * @param IAssignees $to
     * @param bool $check_users
     * @return bool
     * @throws Exception
     */
    function cloneTo(IAssignees $to, $check_users = false) {
      $assignee_id = $this->object->getAssigneeId();
      
      if($assignee_id) {
        $existing_user_ids = array();
        if ($check_users) {
          $model = Inflector::pluralize(get_class($to));
          $existing_user_ids = $to->getProject()->users()->getIds();

          $responsible = Users::findById($assignee_id);
          if (in_array($assignee_id, $existing_user_ids) && $responsible instanceof User && $responsible->getState() > STATE_ARCHIVED) {
            if (class_exists($model) && method_exists($model, "canAccess") && $model::canAccess($responsible, $to->getProject())) { // permissions in target project are ok
              $to->setAssigneeId($assignee_id);
            } else {
              $to->setAssigneeId(null); // responsible user has no permissions for that area, don't clone the assignments
            } // if
          } else {
            $to->setAssigneeId(null); // responsible user doesn't exist in target project, stop cloning assignments
          } // if
        } else {
          $to->setAssigneeId($assignee_id);
        } // if

        $to->save();

        if ($to->getAssigneeId()) {
          $rows = DB::execute('SELECT user_id FROM ' . TABLE_PREFIX . 'assignments WHERE parent_type = ? AND parent_id = ?', get_class($this->object), $this->object->getId());
          if($rows) {
            try {
              $batch = new DBBatchInsert(TABLE_PREFIX . 'assignments', array('parent_type', 'parent_id', 'user_id'));

              $parent_type = get_class($to);
              $parent_id = $to->getId();

              DB::beginWork('Cloning assignments @ ' . __CLASS__);

              foreach($rows as $row) {
                if ($check_users && is_foreachable($existing_user_ids)) {
                  if (!in_array($row['user_id'], $existing_user_ids)) { // not in traget project
                    continue;
                  } else {
                    $user = Users::findById($row['user_id']);
                    if (!$user instanceof User) { // doesn't exist
                      continue;
                    } else {
                      if (class_exists($model) && method_exists($model, "canAccess") && !$model::canAccess($user, $to->getProject()) && $user->getState() > STATE_ARCHIVED) { // no permissions
                        continue;
                      } // if
                    } // if
                  } // if
                } // if

                $batch->insert($parent_type, $parent_id, $row['user_id']);
              } // foreach

              $batch->done();

              DB::commit('Assignments cloned @ ' . __CLASS__);
            } catch(Exception $e) {
              DB::rollback('Failed to clone assignments @ ' . __CLASS__);
              throw $e;
            } // try
          } // if
        } // if
      } // if

      return true;
    } // cloneTo
    
    /**
     * Reset all caches
     */
    protected function resetCache() {
      $this->assignee_ids = false;
      $this->other_assignees = false;
      $this->other_assignee_ids = false;
      $this->pending_assignees = false;
    } // resetCache
    
    /**
     * Get manage assignees url
     * 
     * @return string
     */
    function getManageAssigneesUrl() {
    	return Router::assemble($this->object->getRoutingContext() . '_assignees', $this->object->getRoutingContextParams());
    } // getManageAssigneesUrl
    
    /**
     * Describe assignees related information
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @param array $result
     */
    function describe(IUser $user, $detailed, $for_interface, &$result) {
      if($detailed) {
        $result['assignee'] = $this->getAssignee() instanceof IUser ? $this->getAssignee()->describe($user) : null; // Assignee / Responsible
        $result['delegated_by'] = $this->getDelegatedBy() instanceof IUser ? $this->getDelegatedBy()->describe($user, false, $for_interface) : null;

        if($this->support_multiple_assignees) {
          if($this->getOtherAssignees()) {
            $result['other_assignees'] = array();
            foreach($this->getOtherAssignees() as $assignee) {
              $result['other_assignees'][] = $assignee->describe($user, false, $for_interface);
            } // foreach
          } else {
            $result['other_assignees'] = null;
          } // if
        } // if

        $result['urls']['manage_assignees'] = $this->getManageAssigneesUrl();
      } else {
	      $result['assignee_id'] = $this->object->getAssigneeId();
        $result['delegated_by_id'] = $this->object->getDelegatedById();

        if($this->support_multiple_assignees) {
          $result['other_assignee_ids'] = $result['assignee_id'] ? $this->getOtherAssigneeIds() : null;
        } // if
	      
	      // Delegated by
      } // if
    } // describe

    /**
     * Describe assignees related information
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param array $result
     */
    function describeForApi(IUser $user, $detailed, &$result) {
      $result['assignee_id'] = $this->object->getAssigneeId();
      $result['delegated_by_id'] = $this->object->getDelegatedById();
      if($this->support_multiple_assignees) {
        $result['other_assignee_ids'] = $result['assignee_id'] ? $this->getOtherAssigneeIds() : null;
      } // if

      if($detailed) {
        // @todo remove in future
        $result['assignee'] = $this->getAssignee() instanceof User ? $this->getAssignee()->describeForApi($user) : null;
        $result['delegated_by'] = $this->getDelegatedBy() instanceof IUser ? $this->getDelegatedBy()->describeForApi($user) : null;

        if($this->support_multiple_assignees) {
          if($this->getOtherAssignees()) {
            $result['other_assignees'] = array();
            foreach($this->getOtherAssignees() as $assignee) {
              $result['other_assignees'][] = $assignee->describeForApi($user);
            } // foreach
          } else {
            $result['other_assignees'] = null;
          } // if
        } // if

        $result['urls']['manage_assignees'] = $this->getManageAssigneesUrl();
      } // if
    } // describeForApi

    // ---------------------------------------------------
    //  Notification Related Methods
    // ---------------------------------------------------

    /**
     * Return notification subject prefix, so recipient can sort and filter notifications
     *
     * @return string
     */
    function getNotificationSubjectPrefix() {
      return '';
    } // getNotificationSubjectPrefix

    /**
     * Send email notifications about re-assignment
     *
     * @param User $old_assignee
     * @param User $new_assignee
     * @param User $reassigned_by
     */
    function notifyOnReassignment($old_assignee, $new_assignee, User $reassigned_by) {
      $notify_new_assignee = $notify_old_assignee = false;

      if($old_assignee instanceof User && $new_assignee instanceof User) {
        if($old_assignee->getId() != $new_assignee->getId()) {
          $notify_new_assignee = $notify_old_assignee = true;
        } // if
      } elseif($old_assignee instanceof User) {
        $notify_old_assignee = true;
      } elseif($new_assignee instanceof User) {
        $notify_new_assignee = true;
      } // if

      // Make sure that title phrases end up in lang
      // lang(':type Reassigned')
      // lang(':type Assigned')

      if($notify_new_assignee) {
        AngieApplication::notifications()
          ->notifyAbout(ASSIGNEES_FRAMEWORK_INJECT_INTO . '/notify_new_assignee', $this->object, $reassigned_by)
          ->setIsReassigned($old_assignee instanceof User)
          ->sendToUsers($new_assignee);
      } // if

      if($notify_old_assignee) {
        AngieApplication::notifications()
          ->notifyAbout(ASSIGNEES_FRAMEWORK_INJECT_INTO . '/notify_old_assignee', $this->object, $reassigned_by)
          ->sendToUsers($old_assignee);
      } // if
    } // notifyOnReassignment
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Assignees that are pending to be attached to the parent object
     *
     * @var array
     */
    private $pending_assignees = false;

    /**
     * Return list of pending assignees
     *
     * @return array|bool
     */
    function getPending() {
      return $this->pending_assignees;
    } // getPending
    
    /**
     * Set pending assignment data
     * 
     * $assignees can be:
     * 
     * 1. Flat array of assignees. System will use first assignee in the list as 
     *    reponsible assignee
     * 2. Array where first element is list of all assignees and second is ID of 
     *    reposnible assignee
     * 3. ID of responsible assignee (only assignee)
     * 4. Empty value - all existing assigees will be cleared
     *
     * @param mixed $assignees
     * @return mixed
     */
    function setPending($assignees) {
      $this->pending_assignees = is_array($assignees) ? $assignees : array();
      
      return $this->pending_assignees;
    } // setPendingAssignees
    
    /**
     * Commit pending assignment data
     */
    function commitPending() {
      if (is_array($this->pending_assignees) && $this->support_multiple_assignees) {
        try {

          $assignments_table = TABLE_PREFIX . 'assignments';

          $parent_type = get_class($this->object);
          $parent_id = $this->object->getId();

          DB::beginWork('Commiting pending assignment data @ ' . __CLASS__);

          //Assignments::deleteByParent($this->object);
          DB::execute("DELETE FROM $assignments_table WHERE parent_type = ? AND parent_id = ?", $parent_type, $parent_id);

          if(is_foreachable($this->pending_assignees)) {
            $object_assignee_id = $this->object->getAssigneeId();

            $user_ids = array();
            $to_insert = array();

            foreach($this->pending_assignees as $user_id) {
              $user_id = (integer) $user_id;

              if($user_id) {
                if(in_array($user_id, $user_ids) || $object_assignee_id == $user_id) {
                  continue;
                } // if

                $user_ids[] = $user_id;

                $to_insert[] = DB::prepare('(?, ?, ?)', $parent_type, $parent_id, $user_id);
              } // if
            } // foreach

            if(count($to_insert)) {
              DB::execute("INSERT INTO $assignments_table (parent_type, parent_id, user_id) VALUES " . implode(', ', $to_insert));
            } // if
          } // if

          // Make sure that all assignees are subscribed
          if($this->object instanceof ISubscriptions && $this->pending_assignees) {
            $this->object->subscriptions()->set($this->pending_assignees, false);
          } // if

          DB::commit('Pending assignment data commited @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to commit pending assignment data @ ' . __CLASS__);
          throw $e;
        } // try

        $this->resetCache();
      } // if
    } // commitPending
    
  }