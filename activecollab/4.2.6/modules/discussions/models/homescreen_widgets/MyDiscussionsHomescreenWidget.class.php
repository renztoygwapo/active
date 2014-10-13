<?php

  /**
   * My discussions home screen widget implementation
   * 
   * @package activeCollab.modules.discussions
   * @subpackage models
   */
  class MyDiscussionsHomescreenWidget extends HomescreenWidget {
  
    /**
     * Return widget name
     * 
     * @return string
     */
    function getName() {
      return lang('My Discussions');
    } // getName
    
    /**
     * Return widget description
     * 
     * @return string
     */
    function getDescription() {
      return lang('Display discussions started by logged user or discussions that he took part in recently');
    } // getDescription
    
    /**
     * Return widget body
     * 
     * @param IUser $user
     * @param string $widget_id
     * @param string $column_wrapper_class
     * @return string
     */
    function renderBody(IUser $user, $widget_id, $column_wrapper_class = null) {
      $logged_user =& Authentication::getLoggedUser();
      
      if($logged_user instanceof User) {
        $discussions =  Discussions::findByUser($logged_user, $this->getExtended(), $this->getIncludeCompletedProjects(), $this->getNum());
      } else {
        $discussions = null;
      } // if
      
      if($discussions) {
        AngieApplication::useHelper('ago', GLOBALIZATION_FRAMEWORK, 'modifier');
        AngieApplication::useHelper('user_link', AUTHENTICATION_FRAMEWORK);
        AngieApplication::useHelper('object_link', ENVIRONMENT_FRAMEWORK);
        
        $smarty =& SmartyForAngie::getInstance();
        
        $result = '<table class="common" cellspacing="0">
          <thead>
            <tr>
              <th class="name">' . lang('Discussion / Project') . '</th>
              <th class="last_comment_on center">' . lang('Last Comment') . '</th>
              <th class="author right">' . lang('Started By') . '</th>
            </tr>
          <tbody>';
        
        foreach($discussions as $discussion) {
          $last_comment_on = $discussion->getLastCommentOn();

          $result .= '<tr class="name">
            <td class="name">' . smarty_function_object_link(array('object' => &$discussion, 'additional' => array('class' => 'quick_view_item')), $smarty) . ' ' . lang('in') . ' ' . smarty_function_object_link(array('object' => $discussion->getProject(), 'additional' => array('class' => 'quick_view_item')), $smarty) . '</td>
            <td class="last_comment_on center">' . ($last_comment_on instanceof DateTimeValue ? smarty_modifier_ago($last_comment_on) : '--') . '</td>
            <td class="author right">' . smarty_function_user_link(array('user' => $discussion->getCreatedBy(), 'short' => true), $smarty) . '</td>
          </td>';
        } // foreach
        
        return $result . '</tbody></table>';
      } else {
        return '<p>' . lang('There are no discussions to display') . '</p>';
      } // if
    } // renderBody
    
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
      $view = SmartyForAngie::getInstance()->createTemplate(get_view_path('my_discussions_options', 'homescreen_widgets', DISCUSSIONS_MODULE, AngieApplication::INTERFACE_DEFAULT));
      
      $view->assign(array(
        'widget' => $this, 
        'user' => $user, 
        'widget_data' => array(
          'num' => $this->getNum(), 
          'extended' => $this->getExtended(),
          'include_completed_projects' => $this->getIncludeCompletedProjects(),
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
      if(array_key_exists('num', $attributes)) {
        $this->setNum((integer) array_var($attributes, 'num', 30, true));
      } // if
      
      if(array_key_exists('extended', $attributes)) {
        $this->setExtended((boolean) array_var($attributes, 'extended', false, true));
      } // if

      if(array_key_exists('include_completed_projects', $attributes)) {
        $this->setIncludeCompletedProjects((boolean) array_var($attributes, 'include_completed_projects', false, true));
      } // if
      
      parent::setAttributes($attributes);
    } // setAttributes
    
    /**
     * Return number of discussions that this widget needs to show
     * 
     * @return integer
     */
    function getNum() {
      return $this->getAdditionalProperty('num', 30);
    } // getNum
    
    /**
     * Set number of discussions that are returned
     * 
     * @param integer $value
     * @return integer
     */
    function setNum($value) {
      if($value < 1) {
        $value = 30;
      } // if
      
      if($value > 100) {
        $value = 100;
      } // if
      
      return $this->setAdditionalProperty('num', $value);
    } // setNum
    
    /**
     * Return value of extended property
     * 
     * @return boolean
     */
    function getExtended() {
      return (boolean) $this->getAdditionalProperty('extended', true);
    } // getExtended
    
    /**
     * Set extended propery value
     * 
     * @param boolean $value
     * @return boolean
     */
    function setExtended($value) {
      return $this->setAdditionalProperty('extended', (boolean) $value);
    } // setExtended

    /**
     * Returns true if we should include completed projects
     *
     * @return bool
     */
    function getIncludeCompletedProjects() {
      return (boolean) $this->getAdditionalProperty('include_completed_projects', false);
    } // getIncludeCompletedProjects

    /**
     * Set include completed projects flag
     *
     * @param boolean $value
     * @return bool
     */
    function setIncludeCompletedProjects($value) {
      return $this->setAdditionalProperty('include_completed_projects', (boolean) $value);
    } // setIncludeCompletedProjects
    
  }