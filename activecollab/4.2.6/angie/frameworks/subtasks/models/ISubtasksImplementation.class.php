<?php

  /**
   * Subtasks implementation that can be attached to any object
   *
   * @package angie.frameworks.subtasks
   * @subpackage models
   */
  abstract class ISubtasksImplementation {
    
    // Constants used by finders and counters, mostly internally
    const ANY = 'any';
    const COMPLETED = 'completed';
    const OPEN = 'open';
    
    /**
     * Parent object instance
     *
     * @var ApplicationObject|ISubtasks|IRoutingContext|IState
     */
    protected $object;
    
    /**
     * Construct subtasks implementation and set parent object
     *
     * @param ISubtasks $object
     */
    function __construct(ISubtasks $object) {
      $this->object = $object;
    } // __construct

    /**
     * Create a new subtask instance
     *
     * @return Subtask
     */
    abstract function newSubtask();

    /**
     * Return notification subject prefix, so recipient can sort and filter notifications
     *
     * @return string
     */
    function getNotificationSubjectPrefix() {
      return '';
    } // getNotificationSubjectPrefix
    
    /**
     * Cached array of tasks attached to parent object
     *
     * @var boolean
     */
    private $tasks = array();
    
    /**
     * Return all tasks that belong to this object
     *
     * @param IUser $user
     * @param string $completed
     * @return array
     */
    function get(IUser $user, $completed = ISubtasksImplementation::ANY) {
      if($completed != ISubtasksImplementation::COMPLETED && $completed != ISubtasksImplementation::OPEN) {
        $completed = ISubtasksImplementation::ANY;
      } // if
      
      $cache_id = $user instanceof User ? $user->getId() . '_' . $completed : $user->getEmail() . '_' . $completed;
      
      if(!array_key_exists($cache_id, $this->tasks)) {
        switch($completed) {
          case ISubtasksImplementation::COMPLETED:
            $this->tasks[$cache_id] = Subtasks::findCompletedByParent($this->object);
            break;
          case ISubtasksImplementation::OPEN:
            $this->tasks[$cache_id] = Subtasks::findOpenByParent($this->object);
            break;
          default:
            $this->tasks[$cache_id] = Subtasks::findByParent($this->object);
            break;
        } // switch
      } // if
      
      return $this->tasks[$cache_id];
    } // get
    
    /**
     * Return total number of tasks in this object
     *
     * @param IUser $user
     * @param string $completed
     * @param boolean $use_cache
     * @return integer
     */
    function count(IUser $user, $completed = ISubtasksImplementation::ANY, $use_cache = true) {
      $value = Subtasks::countByParent($this->object, $user, $use_cache);
      
      if(is_array($value) && count($value) == 2) {
        list($total_subtasks, $open_subtasks) = $value;
        
        switch($completed) {
          case ISubtasksImplementation::COMPLETED:
            return $total_subtasks - $open_subtasks;
          case ISubtasksImplementation::OPEN:
            return $open_subtasks;
          default:
            return $total_subtasks;
        } // switch
      } else {
        return 0;
      } // if
    } // count
    
    /**
     * Return array of open object tasks
     *
     * @param IUser $user
     * @return array
     */
    function getOpen(IUser $user) {
      return $this->get($user, ISubtasksImplementation::OPEN);
    } // getOpen
    
    /**
     * Return number of open tasks in this object
     *
     * @param IUser $user
     * @return integer
     */
    function countOpen(IUser $user) {
      return $this->count($user, ISubtasksImplementation::OPEN);
    } // countOpen
    
    /**
     * Return array of completed object tasks (if there is no limit, it will cache tasks)
     *
     * @param IUser $user
     * @param integer $limit
     * @return array
     */
    function getCompleted(IUser $user, $limit = null) {
      if($limit === null) {
        return $this->get($user, ISubtasksImplementation::COMPLETED);
      } else {
        return Subtasks::findCompletedByParent($this->object, $this->object->getState(), $user->getMinVisibility(), $limit);
      } // if
    } // getCompleted
    
    /**
     * Return number of completed tasks in this object
     *
     * @param IUser $user
     * @return integer
     */
    function countCompleted(IUser $user) {
      return $this->count($user, ISubtasksImplementation::COMPLETED);
    } // countCompleted

    /**
     * Describe subtask related information
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param string $for_interface
     * @param array $result
     */
    function describe(IUser $user, $detailed, $for_interface, &$result) {
      $result['subtasks_url'] = $this->getUrl();

      $result['total_subtasks'] = $this->count($user);
			if($result['total_subtasks']) {
      	$result['open_subtasks'] = $this->count($user, ISubtasksImplementation::OPEN);
        $result['completed_subtasks'] = $result['total_subtasks'] - $result['open_subtasks'];
      } else {
      	$result['open_subtasks'] = 0;
        $result['completed_subtasks'] = 0;
			} // if
    } // describe

    /**
     * Describe subtask related information
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param array $result
     */
    function describeForApi(IUser $user, $detailed, &$result) {
      $result['subtasks_url'] = $this->getUrl();

      $result['total_subtasks'] = $this->count($user);
      if($result['total_subtasks']) {
        $result['open_subtasks'] = $this->count($user, ISubtasksImplementation::OPEN);
        $result['completed_subtasks'] = $result['total_subtasks'] - $result['open_subtasks'];
      } else {
        $result['open_subtasks'] = 0;
        $result['completed_subtasks'] = 0;
      } // if
    } // describeForApi
    
    // ---------------------------------------------------
    //  Utility methods
    // ---------------------------------------------------
    
    /**
     * Advance all subtask due dates for a given number of seconds ($advance 
     * value can be negative, to move them back)
     * 
     * @param integer $advance
     */
    function advanceDueDates($advance) {
      Subtasks::advanceByParent($this->object, $advance);
    } // advanceDueDates
    
    /**
     * Complete all open tasks
     *
     * @param IUser $by
     */
    function completeOpenSubtasks(IUser $by) {
      DB::execute('UPDATE ' . TABLE_PREFIX . 'subtasks SET completed_on = UTC_TIMESTAMP(), completed_by_id = ?, completed_by_name = ?, completed_by_email = ? WHERE parent_type = ? AND parent_id = ?', $by->getId(), $by->getName(), $by->getEmail(), get_class($this->object), $this->object->getId());

      AngieApplication::cache()->removeByModel('subtasks');
      AngieApplication::cache()->removeByObject($this->object, 'subtasks_count');
    } // completeOpenSubtasks
    
    /**
     * Clone subtasks to a $to object
     * 
     * @param ISubtasks $to
     * @throws Exception
     */
    function cloneTo(ISubtasks $to) {
      $subtasks_table = TABLE_PREFIX . 'subtasks';
      $subscriptions_table = TABLE_PREFIX . 'subscriptions';
      
      $rows = DB::execute("SELECT id, type, label_id, assignee_id, priority, body, due_on, state, original_state, created_on, created_by_id, created_by_name, created_by_email, position FROM $subtasks_table WHERE parent_type = ? AND parent_id = ? AND state >= ?", get_class($this->object), $this->object->getId(), ($this->object instanceof IState ? $this->object->getState() : STATE_VISIBLE));
      if($rows) {
        $parent_type = get_class($to);
        $parent_id = $to->getId();
        
        try {
          DB::beginWork('Moving subtasks @ ' . __CLASS__);
          
          $subtasks_batch = new DBBatchInsert($subtasks_table, array('parent_type', 'parent_id', 'type', 'label_id', 'assignee_id', 'priority', 'body', 'due_on', 'state', 'original_state', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email', 'position'));
          $subscriptions_batch = new DBBatchInsert($subscriptions_table, array('parent_type', 'parent_id', 'user_id', 'subscribed_on', 'code'));
          
          $now = DateTimeValue::now()->toMySQL();
          
          foreach($rows as $row) {
            $subscriber_ids = DB::executeFirstColumn("SELECT user_id FROM $subscriptions_table WHERE parent_type = ? AND parent_id = ?", $row['type'], $row['id']);
            
            // If we have subscribers, we'll need new subtask ID so we need to do the insert now
            if($subscriber_ids) {
              DB::execute("INSERT INTO $subtasks_table (parent_type, parent_id, type, label_id, assignee_id, priority, body, due_on, state, original_state, created_on, created_by_id, created_by_name, created_by_email, position) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 
                $parent_type, $parent_id, $row['type'], $row['label_id'], $row['assignee_id'], $row['priority'], $row['body'], $row['due_on'], $row['state'], $row['original_state'], $row['created_on'], $row['created_by_id'], $row['created_by_name'], $row['created_by_email'], $row['position']);
                
              $new_subtask_id = DB::lastInsertId();
              foreach($subscriber_ids as $subscriber_id) {
                $subscriptions_batch->insert($row['type'], $new_subtask_id, $subscriber_id, $now, make_string(10));
              } // foreach
              
            // No subscribers? Add subtask to batch
            } else {
              $subtasks_batch->insert($parent_type, $parent_id, $row['type'], $row['label_id'], $row['assignee_id'], $row['priority'], $row['body'], $row['due_on'], $row['state'], $row['original_state'], $row['created_on'], $row['created_by_id'], $row['created_by_name'], $row['created_by_email'], $row['position']);
            } // if
          } // foreach
          
          $subtasks_batch->done();
          $subscriptions_batch->done();
          
          DB::commit('Subtasks moved @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to move subtasks @ ' . __CLASS__);
          throw $e;
        } // try
      } // if
    } // cloneTo
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can create subtasks for parent object
     *
     * @param IUser $user
     * @return boolean
     */
    function canAdd(IUser $user) {
      return $this->object->canEdit($user);
    } // canAdd
    
    /**
     * Returns true if $user can manage all subtasks attached to parent object
     *
     * @param IUser $user
     * @return boolean
     */
    function canManage(IUser $user) {
      return $this->object->canEdit($user);
    } // canManage
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return subtasks section URL
     * 
     * @return string
     */
    function getUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_subtasks', $this->object->getRoutingContextParams());
    } // getUrl
    
    /**
     * Return subtasks archive URL
     *
     * @return string
     */
    function getArchiveUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_subtasks_archive', $this->object->getRoutingContextParams());
    } // getArchiveUrl
    
    /**
     * Return post task URL
     *
     * @return string
     */
    function getAddUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_subtasks_add', $this->object->getRoutingContextParams());
    } // getAddUrl
    
    /**
     * Return URL for tasks reordering
     *
     * @return string
     */
    function getReorderUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_subtasks_reorder', $this->object->getRoutingContextParams());
    } // getReorderUrl
    
  }