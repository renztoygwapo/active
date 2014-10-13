<?php

  /**
   * Tasks filter homescreen widget
   * 
   * @package activeCollab.modules.tasks
   * @subpackage models
   */
  class TasksFilterHomescreenWidget extends AssignmentsFilterHomescreenWidget {
    
    // Assignee filter
    const ANYBODY = 'anybody';
    const UNASSIGNED = 'unassigned';
    const LOGGED_USER = 'logged_user';
    const SELECTED_USER = 'selected';
    
    // Project filters
    const ACTIVE_PROJECTS = 'active';
    const COMPLETED_PROJECTS = 'completed';
    const SELECTED_PROJECT = 'selected';
  
    /**
     * Return widget name
     * 
     * @return string
     */
    function getName() {
      return lang('Tasks Filter');
    } // getName
    
    /**
     * Return widget description
     * 
     * @return string
     */
    function getDescription() {
      return lang('Display list of tasks based on given criteria');
    } // getDescription
    
    /**
     * Return group name for widgets of this type
     * 
     * @return string
     */
    function getGroupName() {
      return lang('Tasks');
    } // getGroupName
    
    /**
     * Return true if widget should display group headers
     * 
     * @return boolean
     */
    function showGroupHeaders() {
      return $this->getGroupBy() != AssignmentFilter::DONT_GROUP;
    } // showGroupHeaders
    
    /**
     * Prepare and return assignments filter instance
     * 
     * @return AssignmentFilter
     */
    function getFilter() {
      $filter = new AssignmentFilter();
      
      $filter->setIncludeSubtasks($this->getIncludeSubtasks()); // No subtasks!

      // show completed tasks only if tasks are grouped by completion date
      if ($this->getGroupBy() == AssignmentFilter::GROUP_BY_COMPLETED_ON) {
        $filter->setCompletedOnFilter(AssignmentFilter::DATE_FILTER_ANY);
      } else {
        $filter->setCompletedOnFilter(AssignmentFilter::DATE_FILTER_IS_NOT_SET);
      } // if

      $logged_user = Authentication::getLoggedUser();
      $filter->setIncludeAllProjects($logged_user->isProjectManager());
      
      list($assignee_filter, $user_id, $responsible_only) = $this->getAssigneeFilter();
      list($projects_filter, $project_id) = $this->getProjectsFilter();
      
      switch($assignee_filter) {
        case self::UNASSIGNED:
          $filter->setUserFilter(AssignmentFilter::USER_FILTER_NOT_ASSIGNED);
          break;
        case self::LOGGED_USER:
          $filter->setUserFilter(($responsible_only ? AssignmentFilter::USER_FILTER_LOGGED_USER_RESPONSIBLE : AssignmentFilter::USER_FILTER_LOGGED_USER));
          break;
        case self::SELECTED_USER:
          $filter->filterByUsers(array($user_id), $responsible_only);
          break;
        default:
          $filter->setUserFilter(AssignmentFilter::USER_FILTER_ANYBODY);
      } // switch
      
      switch($projects_filter) {
        case self::ACTIVE_PROJECTS:
          $filter->setProjectFilter(Projects::PROJECT_FILTER_ACTIVE);
          break;
        case self::COMPLETED_PROJECTS:
          $filter->setProjectFilter(Projects::PROJECT_FILTER_COMPLETED);
          break;
        case self::SELECTED_PROJECT:
          $filter->filterByProjects(array($project_id));
          break;
      } // switch
      
      if($this->getLabelNames()) {
        $filter->filterByLabelNames($this->getLabelNames());
      } // if
      
      if($this->getCategoryNames()) {
        $filter->filterByCategoryNames($this->getCategoryNames());
      } // if
      
      if($this->getMilestoneNames()) {
        $filter->filterByMilestoneNames($this->getMilestoneNames());
      } // if
      
      $filter->setGroupBy($this->getGroupBy());
      
      return $filter;
    } // getFilter
    
    // ---------------------------------------------------
    //  Options
    // ---------------------------------------------------
    
    /**
     * Returns true if this widget has additional options
     * 
     * @return boolean
     */
    function hasOptions() {
      return true;
    } // hasOptions
    
    /**
     * Render widget options form section
     * 
     * @param IUser $user
     * @return string
     */
    function renderOptions(IUser $user) {
      $view = SmartyForAngie::getInstance()->createTemplate($this->getOptionsViewPath());
      
      $view->assign(array(
        'widget' => $this, 
        'user' => $user, 
        'widget_data' => $this->getOptionsViewWidgetData(),
      ));
      
      return $view->fetch();
    } // renderOptions
    
    /**
     * Return options view path
     * 
     * @return string
     */
    protected function getOptionsViewPath() {
      return get_view_path('tasks_filter_options', 'homescreen_widgets', TASKS_MODULE, AngieApplication::INTERFACE_DEFAULT);
    } // getOptionsViewPath
    
    /**
     * Return options view widget data
     * 
     * @return array
     */
    protected function getOptionsViewWidgetData() {
      list($assignee_filter, $user_id, $responsible_only) = $this->getAssigneeFilter();
      list($projects_filter, $project_id) = $this->getProjectsFilter();
      
      return array(
        'caption' => $this->getCaption(),
        'include_subtasks' => $this->getIncludeSubtasks(),
        'assignee_filter' => $assignee_filter, 
        'user_id' => $user_id, 
        'responsible_only' => $responsible_only, 
        'projects_filter' => $projects_filter, 
        'project_id' => $project_id,  
        'category_names' => $this->getCategoryNames(), 
        'label_names' => $this->getLabelNames(),
        'milestone_names' => $this->getMilestoneNames(), 
        'group_by' => $this->getGroupBy()
      );
    } // getOptionsViewWidgetData

    // ---------------------------------------------------
    //  Attributes
    // ---------------------------------------------------

    /**
     * Tasks filter widget has caption
     *
     * @return bool
     */
    function hasCaption() {
      return true;
    } // hasCaption
    
    /**
     * Bulk set widget attributes
     * 
     * @param array $attributes
     */
    function setAttributes($attributes) {
      if(array_key_exists('include_subtasks', $attributes)) {
        $this->setIncludeSubtasks($attributes['include_subtasks']);
      } // if

      // Assignee filter
      if(array_key_exists('assignee_filter', $attributes)) {
        $assignee_filter = $attributes['assignee_filter'];
        
        // Unassigned
        if($assignee_filter == self::UNASSIGNED) {
          $this->setAssigneeFilter($assignee_filter);
          
        // Assigned to logged user
        } else if($assignee_filter == self::LOGGED_USER) {
          $responsible_only = isset($attributes['responsible_only']) && $attributes['responsible_only'] ? (boolean) $attributes['responsible_only'] : null;
          
          $this->setAssigneeFilter($assignee_filter, null, $responsible_only);
          
        // Assigned to selected user
        } else if($assignee_filter == self::SELECTED_USER) {
          $user_id = isset($attributes['user_id']) && $attributes['user_id'] ? (integer) $attributes['user_id'] : null;
          $responsible_only = isset($attributes['responsible_only']) && $attributes['responsible_only'] ? (boolean) $attributes['responsible_only'] : null;
          
          $this->setAssigneeFilter(self::SELECTED_USER, $user_id, $responsible_only);
          
        // Anybody
        } else {
          $this->setAssigneeFilter(self::ANYBODY);
        } // if
      } // if
      
      // Project filter
      if(array_key_exists('projects_filter', $attributes)) {
        $projects_filter = $attributes['projects_filter'];
        
        if($projects_filter == self::ACTIVE_PROJECTS || $projects_filter == self::COMPLETED_PROJECTS) {
          $this->setProjectsFilter($projects_filter);
        } elseif($projects_filter == self::SELECTED_PROJECT) {
          $this->setProjectsFilter(self::SELECTED_PROJECT, (isset($attributes['project_id']) && $attributes['project_id'] ? (integer) $attributes['project_id'] : null));
        } // if
      } // if
      
      // Categories, labels and milestones
      $this->setCategoryNames(empty($attributes['category_names']) ? null : trim($attributes['category_names']));
      $this->setLabelNames(empty($attributes['label_names']) ? null : trim($attributes['label_names']));
      $this->setMilestoneNames(empty($attributes['milestone_names']) ? null : trim($attributes['milestone_names']));
      
      // Group by
      $this->setGroupBy(isset($attributes['group_by']) ? $attributes['group_by'] : AssignmentFilter::DONT_GROUP);
      
      parent::setAttributes($attributes);
    } // setAttributes

    /**
     * Return true if this widget should show subtasks
     *
     * @return bool
     */
    function getIncludeSubtasks() {
      return (boolean) $this->getAdditionalProperty('include_subtasks');
    } // getIncludeSubtasks

    /**
     * Set include subtasks
     *
     * @param boolean $value
     * @return mixed
     */
    function setIncludeSubtasks($value) {
      return $this->setAdditionalProperty('include_subtasks', (boolean) $value);
    } // setIncludeSubtasks
    
    /**
     * Return assignee filter
     * 
     * @return array
     */
    function getAssigneeFilter() {
      return array($this->getAdditionalProperty('assignee_filter', self::UNASSIGNED), $this->getAdditionalProperty('user_id'), $this->getResponsibleOnly());
    } // getAssigneeFilter
    
    /**
     * Set assignee filter
     * 
     * @param string $filter
     * @param integer $user_id
     * @param boolean $responsible_only
     */
    function setAssigneeFilter($filter, $user_id = null, $responsible_only = null) {
      switch($filter) {
        case self::UNASSIGNED:
          $this->setAdditionalProperty('assignee_filter', $filter);
          $this->setAdditionalProperty('user_id', null);
          $this->setAdditionalProperty('responsible_only', null);
          break;
        case self::LOGGED_USER:
          $this->setAdditionalProperty('assignee_filter', $filter);
          $this->setAdditionalProperty('user_id', null);
          $this->setAdditionalProperty('responsible_only', $responsible_only);
          break;
        case self::SELECTED_USER:
          $this->setAdditionalProperty('assignee_filter', $filter);
          $this->setAdditionalProperty('user_id', $user_id);
          $this->setAdditionalProperty('responsible_only', $responsible_only);
          break;
        default:
          $this->setAdditionalProperty('assignee_filter', self::ANYBODY);
          $this->setAdditionalProperty('user_id', null);
          $this->setAdditionalProperty('responsible_only', null);
      } // if
    } // setAssigneeFilter
    
    /**
     * Return value of responsible only flag
     * 
     * @return boolean
     */
    function getResponsibleOnly() {
      return (boolean) $this->getAdditionalProperty('responsible_only');
    } // getResponsibleOnly
    
    /**
     * Set responsible only value
     * 
     * @param boolean $value
     * @return boolean
     */
    function setResponsibleOnly($value) {
      return $this->setAdditionalProperty('responsible_only', (boolean) $value);
    } // setResponsibleOnly
    
    /**
     * Return projects filter
     * 
     * Projects filter is defined with three parameters: filter type and 
     * selected project ID
     * 
     * @return array
     */
    function getProjectsFilter() {
      return array($this->getAdditionalProperty('projects_filter', self::ACTIVE_PROJECTS), $this->getAdditionalProperty('project_id'));
    } // getProjectsFilter
    
    /**
     * Set projects filter value
     * 
     * @param string $filter
     * @param integer $project_id
     * @throws InvalidParamError
     */
    function setProjectsFilter($filter, $project_id = null) {
      if($filter == self::ACTIVE_PROJECTS || $filter == self::COMPLETED_PROJECTS) {
        $this->setAdditionalProperty('projects_filter', $filter);
        $this->setAdditionalProperty('project_id', null);
      } elseif($filter == self::SELECTED_PROJECT) {
        $this->setAdditionalProperty('projects_filter', $filter);
        $this->setAdditionalProperty('project_id', $project_id);
      } else {
        throw new InvalidParamError('filter', $filter);
      } // if
    } // setProjectsFilter
    
    /**
     * Return category name filter
     * 
     * @return string
     */
    function getCategoryNames() {
      return $this->getAdditionalProperty('category_names');
    } // getCategoryNames
    
    /**
     * Set category name filter
     * 
     * @param string $value
     * @return string
     */
    function setCategoryNames($value) {
      return $this->setAdditionalProperty('category_names', $value);
    } // setCategoryNames
    
    /**
     * Return label names
     * 
     * @return integer
     */
    function getLabelNames() {
      return $this->getAdditionalProperty('label_names');
    } // getLabelNames
    
    /**
     * Set label names
     * 
     * @param integer $value
     * @return integer
     */
    function setLabelNames($value) {
      return $this->setAdditionalProperty('label_names', $value);
    } // setLabelNames
    
    /**
     * Return milestone names
     * 
     * @return integer
     */
    function getMilestoneNames() {
      return $this->getAdditionalProperty('milestone_names');
    } // getMilestoneNames
    
    /**
     * Set milestone names
     * 
     * @param integer $value
     * @return integer
     */
    function setMilestoneNames($value) {
      return $this->setAdditionalProperty('milestone_names', $value);
    } // setMilestoneNames
    
    /**
     * Return group by parameter
     * 
     * @return string
     */
    function getGroupBy() {
      return $this->getAdditionalProperty('group_by', AssignmentFilter::DONT_GROUP);
    } // getGroupBy
    
    /**
     * Set group by value
     * 
     * @param string $value
     * @return string
     */
    function setGroupBy($value) {
      if(empty($value)) {
        $value = AssignmentFilter::DONT_GROUP;
      } // if
      
      return $this->setAdditionalProperty('group_by', $value);
    } // setGroupBy
    
  }