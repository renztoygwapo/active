<?php

  /**
   * Assignment filters home screen tab
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class AssignmentFiltersHomescreenTab extends HomescreenTab {
    
    /**
     * Return tab description
     * 
     * @return string
     */
    function getDescription() {
      return lang('Assignment Filters');
    } // getDescription
    
    /**
     * Render tab
     * 
     * @param IUser $user
     * @return string
     */
    function render(IUser $user) {
      AngieApplication::useHelper('assignment_filters', SYSTEM_MODULE);
      
      $assignment_filter = $this->getAssignmentFilterId() ? DataFilters::findById($this->getAssignmentFilterId()) : null;
      
      return smarty_function_assignment_filters(array(
        'user' => &$user, 
        'filter' => $assignment_filter, 
      ), SmartyForAngie::getInstance());
    } // render
    
    // ---------------------------------------------------
    //  Options
    // ---------------------------------------------------
    
    /**
     * Returns true if this widget has additional options
     * 
     * @return boolean
     */
    protected function hasOptions() {
      return true;
    } // hasOptions
    
    /**
     * Render widget options form section
     * 
     * @param IUser $user
     * @return string
     */
    protected function renderOptions(IUser $user) {
      $view = SmartyForAngie::getInstance()->createTemplate(AngieApplication::getViewPath('assignment_filters_options', 'homescreen_tabs', SYSTEM_MODULE, AngieApplication::INTERFACE_DEFAULT));
      
      $view->assign(array(
        'homescreen_tab' => $this, 
        'user' => $user, 
        'homescreen_tab_data' => array(
          'assignment_filter_id' => $this->getAssignmentFilterId(),  
        ), 
      ));
      
      return $view->fetch();
    } // renderOptions
    
    /**
     * Bulk set widget attributes
     * 
     * @param array $attributes
     */
    function setAttributes($attributes) {
      $this->setAssignmentFilterId(isset($attributes['assignment_filter_id']) ? $attributes['assignment_filter_id'] : null);
      
      parent::setAttributes($attributes);
    } // setAttributes
    
    /**
     * Return welcome message
     * 
     * @return string
     */
    function getAssignmentFilterId() {
      return $this->getAdditionalProperty('assignment_filter_id');
    } // getAssignmentFilterId
    
    /**
     * Set welcome message
     * 
     * @param string $value
     * @return string
     */
    function setAssignmentFilterId($value) {
      return $this->setAdditionalProperty('assignment_filter_id', $value);
    } // setAssignmentFilterId
  
  }