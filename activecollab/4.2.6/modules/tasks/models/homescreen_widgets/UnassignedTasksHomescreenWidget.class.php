<?php

  /**
   * Display list of unassigned objects
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class UnassignedTasksHomescreenWidget extends TasksFilterHomescreenWidget {
    
    /**
     * Return widget name
     * 
     * @return string
     */
    function getName() {
      return lang('Unassigned Tasks');
    } // getName
    
    /**
     * Return widget description
     * 
     * @return string
     */
    function getDescription() {
      return lang('Display a list of tasks that have no assignees');
    } // getDescription
    
    /**
     * Return options view path
     * 
     * @return string
     */
    protected function getOptionsViewPath() {
      return AngieApplication::getViewPath('unassigned_tasks_options', 'homescreen_widgets', TASKS_MODULE, AngieApplication::INTERFACE_DEFAULT);
    } // getOptionsViewPath
    
    /**
     * Return assignee filter
     * 
     * @return array
     */
    function getAssigneeFilter() {
      return array(TasksFilterHomescreenWidget::UNASSIGNED, 0, false);
    } // getAssigneeFilter
    
  }