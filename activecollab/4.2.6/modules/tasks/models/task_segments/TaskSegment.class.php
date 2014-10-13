<?php

  /**
   * TaskSegment class
   *
   * @package ActiveCollab.modules.tasks
   * @subpackage models
   */
  class TaskSegment extends BaseTaskSegment implements IRoutingContext {

    const FILTER_ANY = 'any';
    const FILTER_IS = 'is';
    const FILTER_IS_NOT = 'is_not';

    const PROPERTY_MILESTONE = 'milestone';
    const PROPERTY_CATEGORY = 'category';
    const PROPERTY_LABEL = 'label';
    const PROPERTY_PRIORITY = 'priority';

    /**
     * Return task counts
     *
     * Result is an array where first element is total, second is open and third is number of completed tasks
     *
     * @param IUser $user
     * @return array
     */
    function countTasks(IUser $user) {
      $project_objects_table = TABLE_PREFIX . 'project_objects';

      try {
        $conditions = $this->getConditions($user);
      } catch(DataFilterConditionsError $e) {
        return array(0, 0, 0);
      } catch(Exception $e) {
        throw $e;
      } // if

      $total_tasks = (integer) DB::executeFirstCell("SELECT COUNT(id) FROM $project_objects_table WHERE $conditions");
      if($total_tasks) {
        $completed_tasks = (integer) DB::executeFirstCell("SELECT COUNT(id) FROM $project_objects_table WHERE ($conditions) AND $project_objects_table.completed_on IS NOT NULL");;
      } else {
        $completed_tasks = 0;
      } // if

      return array($total_tasks, ($total_tasks - $completed_tasks), $completed_tasks);
    } // countTasks

    /**
     * Return conditions
     *
     * Set $state_type_and_project to false in situations where segment is used by a different filter which already
     * sets these values correctly
     *
     * @param IUser $user
     * @param boolean $state_type_and_project
     * @return string
     */
    function getConditions(IUser $user, $state_type_and_project = true) {
      $project_objects_table = TABLE_PREFIX . 'project_objects';

      if($state_type_and_project) {
        $project_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'projects WHERE state >= ?', STATE_ARCHIVED);
        if(!is_foreachable($project_ids)) {
          throw new DataFilterConditionsError('project_filter', 'archived_and_active');
        } // if
      } else {
        $project_ids = null; // not needed
      } // if

      if($state_type_and_project) {
        $conditions = array(DB::prepare("($project_objects_table.project_id IN (?) AND $project_objects_table.state >= ? AND $project_objects_table.type = 'Task')", $project_ids, STATE_ARCHIVED));
      } else {
        $conditions = array();
      } // if

      $this->preparePropertyConditions(self::PROPERTY_MILESTONE, $this->getMilestoneFilter(), $this->getMilestoneNames(), $conditions);
      $this->preparePropertyConditions(self::PROPERTY_CATEGORY, $this->getCategoryFilter(), $this->getCategoryNames(), $conditions);
      $this->preparePropertyConditions(self::PROPERTY_LABEL, $this->getLabelFilter(), $this->getLabelNames(), $conditions);

      if($this->getPriorityFilter() === self::FILTER_IS || $this->getPriorityFilter() === self::FILTER_IS_NOT) {
        $selected_priorities = $this->getSelectedPriorities();

        if(is_foreachable($selected_priorities)) {
          $in = $this->getPriorityFilter() === self::FILTER_IS ? 'IN' : 'NOT IN';

          $conditions[] = DB::prepare('(' . TABLE_PREFIX . "project_objects.priority $in (?))", $selected_priorities);
        } else {
          throw new DataFilterConditionsError("priority_filter", $this->getPriorityFilter(), $this->getSelectedPriorities());
        } // if
      } // if

      return implode(' AND ', $conditions);
    } // getConditions

    /**
     * Prepare property conditions
     *
     * @param string $property
     * @param string $filter
     * @param string $names
     * @param array$conditions
     * @throws DataFilterConditionsError
     * @throws InvalidParamError
     */
    private function preparePropertyConditions($property, $filter, $names, &$conditions) {
      if($filter === self::FILTER_IS || $filter === self::FILTER_IS_NOT) {
        $names = $names ? explode(', ', $names) : null;

        if(is_array($names) && count($names)) {
          foreach($names as $k => $v) {
            $names[$k] = trim($v);
          } // foreach

          switch($property) {
            case self::PROPERTY_MILESTONE:
              $ids = Milestones::getIdsByNames($names);
              break;
            case self::PROPERTY_CATEGORY:
              $ids = Categories::getIdsByNames($names, 'TaskCategory');
              break;
            case self::PROPERTY_LABEL:
              $ids = Labels::getIdsByNames($names, 'AssignmentLabel');
              break;
            default:
              throw new InvalidParamError('property', $property, '$property can be milestone, category or label');
          } // switch

          if(is_foreachable($ids)) {
            $in = $filter === self::FILTER_IS ? 'IN' : 'NOT IN';

            $conditions[] = DB::prepare('(' . TABLE_PREFIX . "project_objects.{$property}_id $in (?))", $ids);
          } else {
            throw new DataFilterConditionsError("{$property}_filter", $filter, $names);
          } // if
        } else {
          throw new DataFilterConditionsError("{$property}_filter", $filter, $names);
        } // if
      } // if
    } // preparePropertyConditions

    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'task_segment';
    } // getRoutingContext

    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('task_segment_id' => $this->getId());
    } // getRoutingContextParams

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

      try {
        list($total, $open, $completed) = $this->countTasks($user);
      } catch(DataFilterConditionsError $e) {
        $total = $open = $completed = 0;
      } catch(Exception $e) {
        throw $e;
      } // try

      $result['total_tasks'] = $total;
      $result['open_tasks'] = $open;
      $result['completed_tasks'] = $completed;

      return $result;
    } // describe

    // ---------------------------------------------------
    //  Getters and Setters
    // ---------------------------------------------------

    /**
     * Set attributes
     *
     * @param array $attributes
     */
    function setAttributes($attributes) {
      if(isset($attributes['milestone_filter'])) {
        $this->setMilestoneFilter($attributes['milestone_filter'], @$attributes['milestone_names']);
      } // if

      if(isset($attributes['category_filter'])) {
        $this->setCategoryFilter($attributes['category_filter'], @$attributes['category_names']);
      } // if

      if(isset($attributes['label_filter'])) {
        $this->setLabelFilter($attributes['label_filter'], @$attributes['label_names']);
      } // if

      if(isset($attributes['priority_filter'])) {
        $lowest = isset($attributes['priority_lowest']) && $attributes['priority_lowest'];
        $low = isset($attributes['priority_low']) && $attributes['priority_low'];
        $normal = isset($attributes['priority_normal']) && $attributes['priority_normal'];
        $high = isset($attributes['priority_high']) && $attributes['priority_high'];
        $highest = isset($attributes['priority_highest']) && $attributes['priority_highest'];

        $this->setPriorityFilter($attributes['priority_filter'], $lowest, $low, $normal, $high, $highest);
      } // if

      parent::setAttributes($attributes);
    } // setAttributes

    /**
     * Return milestone filter
     *
     * @return string
     */
    function getMilestoneFilter() {
      return $this->getAdditionalProperty('milestone_filter', self::FILTER_ANY);
    } // getMilestoneFilter

    /**
     * Set milestone filter
     *
     * @param string $filter
     * @param string $names
     * @return string
     */
    function setMilestoneFilter($filter, $names = null) {
      return $this->setPropertyFilter('milestone', $filter, $names);
    } // setMilestoneFilter

    /**
     * Return milestone names
     *
     * @return string
     */
    function getMilestoneNames() {
      return $this->getAdditionalProperty('milestone_names');
    } // getMilestoneNames

    /**
     * Return category filter
     *
     * @return string
     */
    function getCategoryFilter() {
      return $this->getAdditionalProperty('category_filter', self::FILTER_ANY);
    } // getCategoryFilter

    /**
     * Set category filter
     *
     * @param string $filter
     * @param string $names
     * @return string
     */
    function setCategoryFilter($filter, $names = null) {
      return $this->setPropertyFilter('category', $filter, $names);
    } // setCategoryFilter

    /**
     * Return milestone names
     *
     * @return string
     */
    function getCategoryNames() {
      return $this->getAdditionalProperty('category_names');
    } // getCategoryNames

    /**
     * Return label filter
     *
     * @return string
     */
    function getLabelFilter() {
      return $this->getAdditionalProperty('label_filter', self::FILTER_ANY);
    } // getLabelFilter

    /**
     * Set label filter
     *
     * @param string $filter
     * @param string $names
     * @return string
     */
    function setLabelFilter($filter, $names = null) {
      return $this->setPropertyFilter('label', $filter, $names);
    } // setLabelFilter

    /**
     * Return milestone names
     *
     * @return string
     */
    function getLabelNames() {
      return $this->getAdditionalProperty('label_names');
    } // getLabelNames

    /**
     * Return label filter
     *
     * @return string
     */
    function getPriorityFilter() {
      return $this->getAdditionalProperty('priority_filter', self::FILTER_ANY);
    } // getPriorityFilter

    /**
     * Set label filter
     *
     * @param string $filter
     * @param string $names
     * @return string
     */
    function setPriorityFilter($filter, $lowest = false, $low = false, $normal = false, $high = false, $highest = false) {
      if($filter === self::FILTER_IS || $filter === self::FILTER_IS_NOT) {
        $priorities = array();

        if($lowest) {
          $priorities[] = PRIORITY_LOWEST;
        } // if

        if($low) {
          $priorities[] = PRIORITY_LOW;
        } // if

        if($normal) {
          $priorities[] = PRIORITY_NORMAL;
        } // if

        if($high) {
          $priorities[] = PRIORITY_HIGH;
        } // if

        if($highest) {
          $priorities[] = PRIORITY_HIGHEST;
        } // if

        $this->setAdditionalProperty('priority_filter', $filter);
        $this->setAdditionalProperty('selected_priorities', $priorities);
      } else {
        $this->setAdditionalProperty('priority_filter', self::FILTER_ANY);
        $this->setAdditionalProperty('selected_priorities', null);
      } // if
    } // setPriorityFilter

    /**
     * Return array of selected priorities
     *
     * @return array
     */
    function getSelectedPriorities() {
      return $this->getAdditionalProperty('selected_priorities');
    } // getSelectedPriorities

    /**
     * Set property filter
     *
     * @param string $property
     * @param string $filter
     * @param string $names
     * @return string
     */
    private function setPropertyFilter($property, $filter, $names = null) {
      $names = trim($names);

      if(empty($names)) {
        $names = null;
      } // if

      if($filter === self::FILTER_IS || $filter === self::FILTER_IS_NOT) {
        $this->setAdditionalProperty("{$property}_filter", $filter);
        $this->setAdditionalProperty("{$property}_names", empty($names) ? null : $names);
      } else {
        $this->setAdditionalProperty("{$property}_filter", self::FILTER_ANY);
        $this->setAdditionalProperty("{$property}_names", null);
      } // if

      return $this->getAdditionalProperty("{$property}_filter");
    } // setPropertyFilter

    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------

    /**
     * Returns true if $user can edit this tracking report
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return $user->isProjectManager();
    } // canEdit

    /**
     * Returns true if $user can delete this tracking report
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return $user->isProjectManager();
    } // canDelete
    
  }