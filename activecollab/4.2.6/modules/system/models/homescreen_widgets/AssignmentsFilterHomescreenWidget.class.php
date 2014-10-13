<?php

  /**
   * Assignments filter homescreen widget
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  abstract class AssignmentsFilterHomescreenWidget extends HomescreenWidget {
    
    /**
     * Return group name for widgets of this type
     */
    function getGroupName() {
      return lang('Assignments');
    } // getGroupName
    
    /**
     * Prepare and return assignment filter instance
     * 
     * @return AssignmentFilter
     */
    abstract function getFilter();
    
    /**
     * Return true if widget should display group headers
     * 
     * @return boolean
     */
    function showGroupHeaders() {
      return true;
    } // showGroupHeaders
    
    /**
     * Return path to the view file that's used to render result
     * 
     * @return string
     */
    function getResultsViewPath() {
      return AngieApplication::getViewPath('assignments_filter', 'homescreen_widgets', SYSTEM_MODULE, AngieApplication::INTERFACE_DEFAULT);
    } // getResultsViewPath

    /**
     * Return empty result set message
     *
     * @return string
     */
    function getEmptyResultMessage() {
      return lang('There are no tasks that match this filter');
    } // getEmptyResultMessage
    
    /**
     * Return widget body
     * 
     * @param IUser $user
     * @param string $widget_id
     * @param string $column_wrapper_class
     * @return string
     * @throws Exception
     */
    function renderBody(IUser $user, $widget_id, $column_wrapper_class = null) {
      $filter = $this->getFilter();
      
      if($filter instanceof AssignmentFilter) {
        $view = SmartyForAngie::getInstance()->createTemplate($this->getResultsViewPath());

        try {
          $assignments = $filter->run($user);
        } catch(DataFilterConditionsError $e) {
          $assignments = null;
        } catch(Exception $e) {
          throw $e;
        } // try

        $filter->resultToMap($assignments);
      
        $view->assign(array(
          'widget' => $this, 
          'user' => $user, 
          'filter' => $filter, 
          'assignments' =>  $assignments, 
          'project_slugs' => Projects::getIdSlugMap(), 
          'task_url' => AngieApplication::isModuleLoaded('tasks') ? Router::assemble('project_task', array('project_slug' => '--PROJECT_SLUG--', 'task_id' => '--TASK_ID--')) : '', 
          'task_subtask_url' => AngieApplication::isModuleLoaded('tasks') ? Router::assemble('project_task_subtask', array('project_slug' => '--PROJECT_SLUG--', 'task_id' => '--TASK_ID--', 'subtask_id' => '--SUBTASK_ID--')) : '',
          'todo_url' => AngieApplication::isModuleLoaded('todo') ? Router::assemble('project_todo_list', array('project_slug' => '--PROJECT_SLUG--', 'todo_list_id' => '--TODO_LIST_ID--')) : '',
          'todo_subtask_url' => AngieApplication::isModuleLoaded('todo') ? Router::assemble('project_todo_list_subtask', array('project_slug' => '--PROJECT_SLUG--', 'todo_list_id' => '--TODO_LIST_ID--', 'subtask_id' => '--SUBTASK_ID--')) : '',
          'labels' => Labels::getIdDetailsMap('AssignmentLabel'),
          'show_group_headers' => $this->showGroupHeaders(), 
        ));
        
        return $view->fetch();
      } else {
        return '';
      } // if
    } // renderBody
    
  }