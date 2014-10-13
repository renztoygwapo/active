<?php

  /**
   * My tasks home screen widget
   * 
   * @package activeCollab.modules.tasks
   * @subpackage models
   */
  class MyTasksHomescreenWidget extends TasksFilterHomescreenWidget {
    
    /**
     * Return widget name
     * 
     * @return string
     */
    function getName() {
      return lang('My Tasks');
    } // getName

    /**
     * Return empty result set message
     *
     * @return string
     */
    function getEmptyResultMessage() {
      return lang('There are no open tasks that are assigned to you');
    } // getEmptyResultMessage
    
    /**
     * Return options view path
     * 
     * @return string
     */
    protected function getOptionsViewPath() {
      return AngieApplication::getViewPath('my_tasks_options', 'homescreen_widgets', TASKS_MODULE, AngieApplication::INTERFACE_DEFAULT);
    } // getOptionsViewPath
    
    /**
     * Bulk set widget attributes
     * 
     * @param array $attributes
     */
    function setAttributes($attributes) {
      if(isset($attributes['assignee_filter'])) {
        unset($attributes['assignee_filter']);
      } // if
      
      $this->setResponsibleOnly(isset($attributes['responsible_only']) && $attributes['responsible_only']);
      
      parent::setAttributes($attributes);
    } // function
    
    /**
     * Return assignee filter
     * 
     * @return array
     */
    function getAssigneeFilter() {
      return array($this->getAdditionalProperty('assignee_filter', self::LOGGED_USER), null, $this->getResponsibleOnly());
    } // getAssigneeFilter
    
    /**
     * Set assignee filter
     * 
     * @param string $filter
     * @param integer $user_id
     * @param boolean $responsible_only
     * @throws NotImplementedError
     */
    function setAssigneeFilter($filter, $user_id = null, $responsible_only = null) {
      throw new NotImplementedError(__METHOD__);
    } // setAssigneeFilter
  
  }