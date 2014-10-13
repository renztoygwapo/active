<?php

  /**
   * Delegated tasks homescreen widget
   * 
   * @package activeCollab.modules.tasks
   * @subpackage models
   */
  class DelegatedTasksHomescreenWidget extends TasksFilterHomescreenWidget {
  
    /**
     * Return widget name
     * 
     * @return string
     */
    function getName() {
      return lang('Delegated Tasks');
    } // getName
    
    /**
     * Prepare filter
     * 
     * @return AssignmentFilter
     */
    function getFilter() {
      $filter = parent::getFilter();
      
      list($delegated_by_filter, $delegated_by_user_id) = $this->getDelegatedByFilter();
      
      if($delegated_by_filter == self::SELECTED_USER) {
        $filter->filterDelegatedByUsers(array($delegated_by_user_id));
      } else {
        $filter->setDelegatedByFilter(AssignmentFilter::USER_FILTER_LOGGED_USER);
      } // if
      
      return $filter;
    } // getFilter
    
    /**
     * Return options view path
     * 
     * @return string
     */
    protected function getOptionsViewPath() {
      return get_view_path('delegated_tasks_options', 'homescreen_widgets', TASKS_MODULE, AngieApplication::INTERFACE_DEFAULT);
    } // getOptionsViewPath
    
    /**
     * Return options view widget data
     * 
     * @return array
     */
    protected function getOptionsViewWidgetData() {
      list($delegated_by_filter, $delegated_by_user_id) = $this->getDelegatedByFilter();
      
      $widget_data = parent::getOptionsViewWidgetData();
      
      $widget_data['delegated_by_filter'] = $delegated_by_filter;
      $widget_data['delegated_by_user_id'] = $delegated_by_user_id;

      return $widget_data;
    } // getOptionsViewWidgetData
    
    /**
     * Bulk set widget attributes
     * 
     * @param array $attributes
     */
    function setAttributes($attributes) {
      if(array_key_exists('delegated_by_filter', $attributes)) {
        if($attributes['delegated_by_filter'] == self::SELECTED_USER) {
          $this->setDelegatedByFilter(self::SELECTED_USER, (isset($attributes['delegated_by_user_id']) && $attributes['delegated_by_user_id'] ? (integer) $attributes['delegated_by_user_id'] : null));
        } else {
          $this->setDelegatedByFilter(self::LOGGED_USER);
        } // if
      } // if
      
      parent::setAttributes($attributes);
    } // function
    
    /**
     * Return delegated by filter
     * 
     * @return array
     */
    function getDelegatedByFilter() {
      return array($this->getAdditionalProperty('delegated_by_filter', self::LOGGED_USER), $this->getAdditionalProperty('delegated_by_user_id'));
    } // getDelegatedByFilter
    
    /**
     * Set delegated by filter
     * 
     * @param string $filter
     * @param integer $user_id
     */
    function setDelegatedByFilter($filter, $user_id = null) {
      if($filter == self::SELECTED_USER) {
        $this->setAdditionalProperty('delegated_by_filter', $filter);
        $this->setAdditionalProperty('delegated_by_user_id', $user_id);
      } else {
        $this->setAdditionalProperty('delegated_by_filter', self::LOGGED_USER);
        $this->setAdditionalProperty('delegated_by_user_id', null);
      } // if
    } // setDelegatedByFilter
    
  }