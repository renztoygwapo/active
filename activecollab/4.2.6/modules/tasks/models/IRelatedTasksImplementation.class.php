<?php

  /**
   * Related tasks implementation
   *
   * @package activeCollab.modules.tasks
   * @subpackage models
   */
  class IRelatedTasksImplementation {

    /**
     * Parent task instance
     *
     * @var Task
     */
    protected $object;

    /**
     * Construct related tasks helper instance
     *
     * @param Task $object
     */
    function __construct(Task $object) {
      $this->object = $object;
    } // __construct

    /**
     * Returns true if parent task has related tasks that $user can see
     *
     * @return boolean
     */
    function has(IUser $user) {
      $type_filter = $user->projects()->getVisibleTypesFilter(Project::STATUS_ANY, array('Task'));

      if($type_filter) {
        $project_objects_table = TABLE_PREFIX . 'project_objects';
        $related_tasks_table = TABLE_PREFIX . 'related_tasks';

        return (boolean) DB::executeFirstCell("SELECT COUNT($project_objects_table.id) FROM $project_objects_table, $related_tasks_table WHERE (($project_objects_table.id = $related_tasks_table.related_task_id AND $related_tasks_table.parent_task_id = ?) OR ($project_objects_table.id = $related_tasks_table.parent_task_id AND $related_tasks_table.related_task_id = ?)) AND $type_filter AND $project_objects_table.state >= ? AND $project_objects_table.visibility >= ?", $this->object->getId(), $this->object->getId(), STATE_ARCHIVED, $user->getMinVisibility());
      } else {
        return false;
      } // if
    } // has

    /**
     * Return all related tasks
     *
     * @param IUser $user
     * @return DBResult
     */
    function get(IUser $user) {
      $type_filter = $user->projects()->getVisibleTypesFilter(Project::STATUS_ANY, array('Task'));

      if($type_filter) {
        $project_objects_table = TABLE_PREFIX . 'project_objects';
        $related_tasks_table = TABLE_PREFIX . 'related_tasks';

        return Tasks::findBySql("SELECT DISTINCT $project_objects_table.* FROM $project_objects_table, $related_tasks_table WHERE (($project_objects_table.id = $related_tasks_table.related_task_id AND $related_tasks_table.parent_task_id = ?) OR ($project_objects_table.id = $related_tasks_table.parent_task_id AND $related_tasks_table.related_task_id = ?)) AND $type_filter AND $project_objects_table.state >= ? AND $project_objects_table.visibility >= ? ORDER BY $related_tasks_table.created_on", $this->object->getId(), $this->object->getId(), STATE_ARCHIVED, $user->getMinVisibility());
      } else {
        return null;
      } // if
    } // get

    /**
     * Check if given task is related
     *
     * @param Task $task
     * @return boolean
     */
    function isRelated(Task $task) {
      $parent_task_id = $this->object->getId();
      $related_task_id = $task->getId();

      return (boolean) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'related_tasks WHERE (parent_task_id = ? AND related_task_id = ?) OR (parent_task_id = ? AND related_task_id = ?)', $parent_task_id, $related_task_id, $related_task_id, $parent_task_id);
    } // isRelated

    /**
     * Add task to the list of related tasks
     *
     * @param Task $task
     * @param string $note
     * @param IUser $by
     * @throws InvalidParamError
     */
    function addTask(Task $task, $note, IUser $by) {
      if($this->object->getId() == $task->getId()) {
        throw new InvalidParamError('task', $task, "Can't add self");
      } // if

      $note = trim($note) ? trim($note) : null;

      if(!$this->isRelated($task)) {
        DB::execute('INSERT INTO ' . TABLE_PREFIX . 'related_tasks (parent_task_id, related_task_id, note, created_on, created_by_id, created_by_name, created_by_email) VALUES (?, ?, ?, UTC_TIMESTAMP(), ?, ?, ?)', $this->object->getId(), $task->getId(), $note, $by->getId(), $by->getDisplayName(), $by->getEmail());
      } // if
    } // addTask

    /**
     * Remove specific task from list of related tasks
     *
     * @param Task $task
     */
    function removeTask(Task $task) {
      if($this->isRelated($task)) {
        $parent_task_id = $this->object->getId();
        $related_task_id = $task->getId();

        return (boolean) DB::executeFirstCell('DELETE FROM ' . TABLE_PREFIX . 'related_tasks WHERE (parent_task_id = ? AND related_task_id = ?) OR (parent_task_id = ? AND related_task_id = ?)', $parent_task_id, $related_task_id, $related_task_id, $parent_task_id);
      } // if
    } // removeTask

    /**
     * Clear all related tasks
     */
    function clear() {
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'related_tasks WHERE parent_task_id = ?', $this->object->getId());
    } // clear

    /**
     * Describe related tasks information
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @param array $result
     */
    function describe(IUser $user, $detailed, $for_interface, &$result) {
      $result['urls']['related_tasks'] = $this->getUrl();

      if($detailed) {
        $type_filter = $user->projects()->getVisibleTypesFilter(Project::STATUS_ANY, array('Task'));

        if($type_filter) {
          $project_objects_table = TABLE_PREFIX . 'project_objects';
          $related_tasks_table = TABLE_PREFIX . 'related_tasks';

          $rows = DB::execute("SELECT DISTINCT $project_objects_table.id, $project_objects_table.project_id, $project_objects_table.label_id, $project_objects_table.name, $project_objects_table.completed_on, $project_objects_table.integer_field_1 AS 'task_id', $related_tasks_table.note AS 'note' FROM $project_objects_table, $related_tasks_table WHERE (($project_objects_table.id = $related_tasks_table.related_task_id AND $related_tasks_table.parent_task_id = ?) OR ($project_objects_table.id = $related_tasks_table.parent_task_id AND $related_tasks_table.related_task_id = ?)) AND $type_filter AND $project_objects_table.state >= ? AND $project_objects_table.visibility >= ? ORDER BY $related_tasks_table.created_on", $this->object->getId(), $this->object->getId(), STATE_ARCHIVED, $user->getMinVisibility());
        } else {
          $rows = null;
        } // if

        if($rows) {
          $rows->setCasting(array(
            'id' => DBResult::CAST_INT,
            'project_id' => DBResult::CAST_INT,
            'label_id' => DBResult::CAST_INT,
            'task_id' => DBResult::CAST_INT,
          ));

          $result['has_related_tasks'] = true;
          $result['related_tasks'] = array();

          $project_ids = array();

          foreach($rows as $row) {
            $project_ids[] = $row['project_id'];
          } // foreach

          $projects = count($project_ids) ? Projects::getIdDetailsMap(array('name', 'slug'), $project_ids) : array();

          foreach($rows as $row) {
            $project_id = $row['project_id'];

            if(isset($projects[$project_id])) {
              $project_name = $projects[$project_id]['name'];
              $project_slug = $projects[$project_id]['slug'];
            } else {
              $project_name = lang('Unknown');
              $project_slug = $project_id;
            } // if

            $result['related_tasks'][] = array(
              'id' => $row['id'],
              'project_id' => $project_id,
              'project_name' => $project_name,
              'name' => $row['name'],
              'is_completed' => (boolean) $row['completed_on'],
              'url' => Router::assemble('project_task', array(
                'project_slug' => $project_slug,
                'task_id' => $row['task_id'],
              )),
              'task_id' => $row['task_id'],
              'note' => $row['note'],
            );
          } // froeach
        } else {
          $result['has_related_tasks'] = false;
        } // if
      } else {
        $result['has_related_tasks'] = $this->has($user);
      } // if
    } // describe

    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------

    /**
     * Returns true if $user can manage related tasks for parent task
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
     * Return related task URL
     *
     * @return string
     */
    function getUrl() {
      return Router::assemble('project_task_related_tasks', $this->object->getRoutingContextParams());
    } // getUrl

    /**
     * Return add related task URL
     *
     * @return mixed
     */
    function getAddTaskUrl() {
      return Router::assemble('project_task_related_tasks_add', $this->object->getRoutingContextParams());
    } // getAddTaskUrl

    /**
     * Return remove related task URL
     *
     * @param Task $task
     * @return string
     */
    function getRemoveTaskUrl($task) {
      return Router::assemble('project_task_related_tasks_remove', array_merge($this->object->getRoutingContextParams(), array(
        'related_task_id' => $task instanceof Task ? $task->getId() : $task,
      )));
    } // getRemoveTaskUrl

  }