<?php

  /**
   * Framework level homescreen widget implementation
   * 
   * @package angie.frameworks.homescreens
   * @subpackage models
   */
  abstract class FwHomescreenWidget extends BaseHomescreenWidget implements IRoutingContext {
    
    /**
     * Return widget name
     * 
     * @return string
     */
    function getName() {
      return get_class($this);
    } // getName
    
    /**
     * Return widget description
     * 
     * @return string
     */
    function getDescription() {
      return '';
    } // getDescription
    
    /**
     * Return name of the group where this widget needs to be added to
     * 
     * @return string
     */
    function getGroupName() {
      return null;
    } // getGroupName
    
    /**
     * Cached parent home screen tab instance
     *
     * @var HomescreenTab
     */
    private $homescreen_tab = false;
    
    /**
     * Return parent home screen tab
     * 
     * @return HomescreenTab
     */
    function getHomescreenTab() {
      if($this->homescreen_tab === false) {
        $this->homescreen_tab = $this->getHomescreenTabId() ? HomescreenTabs::findById($this->getHomescreenTabId()) : null;
      } // if
      
      return $this->homescreen_tab;
    } // getHomescreenTab
    
    /**
     * Copy this homescreen widget to a given homescreen tab
     * 
     * @param HomescreenTab $homescreen_tab
     * @return HomescreenWidget
     */
    function copyTo(HomescreenTab $homescreen_tab) {
      $class_name = get_class($this);
      
      // Create a home screen tab of the same class as this home screen
      $widget = new $class_name();
      
      $widget->setHomescreenTabId($homescreen_tab->getId());
      $widget->setColumnId($this->getColumnId());
      $widget->setPosition($this->getPosition());
      
      foreach($this->getAdditionalProperties() as $k => $v) {
        $widget->setAdditionalProperty($k, $v);
      } // foreach
      
      $widget->save();
      
      return $widget;
    } // copyTo
    
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
      return array(
        'id' => $this->getId(), 
        'name' => $this->getName(),
        'caption' => $this->hasCaption() ? $this->getCaption() : null,
      	'description' => $this->getDescription() ? $this->getDescription() : null,
      	'column_id' => $this->getColumnId(), 
        'position' => $this->getPosition(), 
        'has_options' => $this->hasOptions(), 
        'permissions' => array(
          'can_edit' => $this->canEdit($user), 
          'can_delete' => $this->canDelete($user), 
        ), 
        'urls' => array(
          'edit' => $this->getEditUrl(), 
          'delete' => $this->getDeleteUrl(), 
        )
      );
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     * @throws NotImplementedError
     */
    function describeForApi(IUser $user, $detailed = false) {
      throw new NotImplementedError(__METHOD__);
    } // describeForApi
    
    // ---------------------------------------------------
    //  Renderers
    // ---------------------------------------------------
    
    /**
     * Return widget title
     * 
     * @param IUser $user
     * @param string $widget_id
     * @param string $column_wrapper_class
     * @return string
     */
    function renderTitle(IUser $user, $widget_id, $column_wrapper_class = null) {
      return $this->hasCaption() && $this->getCaption() ? clean($this->getCaption()) : clean($this->getName());
    } // renderTitle
    
    /**
     * Return widget body
     * 
     * @param IUser $user
     * @param string $widget_id
     * @param string $column_wrapper_class
     * @return string
     */
    abstract function renderBody(IUser $user, $widget_id, $column_wrapper_class = null);
    
    /**
     * Return widget footer
     * 
     * Since widget footer is rendered after widget is fully displayed, this 
     * function is useful for return widget related JavaScript etc
     * 
     * @param IUser $user
     * @param string $widget_id
     * @param string $column_wrapper_class
     * @return string
     */
    function renderFooter(IUser $user, $widget_id, $column_wrapper_class = null) {
      return '';
    } // renderFooter
    
    // ---------------------------------------------------
    //  Options
    // ---------------------------------------------------
    
    /**
     * Returns true if this widget has additional options
     * 
     * @return boolean
     */
    function hasOptions() {
      return false;
    } // hasOptions
    
    /**
     * Render widget options form section
     * 
     * @param IUser $user
     * @return string
     */
    function renderOptions(IUser $user) {
      return '';
    } // renderOptions
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can edit this destkop
     * 
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return $this->getHomescreenTab()->canEdit($user);
    } // canEdit
    
    /**
     * Return true if $user can remove this destkop
     * 
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return $this->getHomescreenTab()->canEdit($user);
    } // canDelete
    
    // ---------------------------------------------------
    //  Interface implementation
    // ---------------------------------------------------
    
    /**
     * Routing context name
     *
     * @var string
     */
    private $routing_context = false;
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      if($this->routing_context === false) {
        $this->routing_context = $this->getHomescreenTab()->getRoutingContext() . '_widget';
      } // if
      
      return $this->routing_context;
    } // getRoutingContext
    
    /**
     * Routing context parameters
     *
     * @var array
     */
    private $routing_context_params = false;
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      if($this->routing_context_params === false) {
        $this->routing_context_params = $this->getHomescreenTab()->getRoutingContextParams() ? array_merge($this->getHomescreenTab()->getRoutingContextParams(), array('homescreen_widget_id' => $this->getId())) : array('homescreen_widget_id' => $this->getId());
      } // if
      
      return $this->routing_context_params;
    } // getRoutingContextParams

    /**
     * Returns the url for rendering the widget
     */
    function getRenderUrl() {
      return Router::assemble('homescreen_widget_render', array('widget_id' => $this->getId()));
    } // getRenderUrl

    // ---------------------------------------------------
    //  Caption
    // ---------------------------------------------------

    /**
     * Return true if this widget uses caption field
     *
     * @return bool
     */
    function hasCaption() {
      return false;
    } // hasCaption

    /**
     * Bulk set widget attributes
     *
     * @param array $attributes
     */
    function setAttributes($attributes) {
      if($this->hasCaption()) {
        $this->setCaption(isset($attributes['caption']) ? $attributes['caption'] : '');
      } // if

      parent::setAttributes($attributes);
    } // setAttributes

    /**
     * Return widget caption
     *
     * @return string
     */
    function getCaption() {
      return $this->getAdditionalProperty('caption');
    } // getCaption

    /**
     * Set caption value
     *
     * @param string $value
     * @return string
     */
    function setCaption($value) {
      if(empty($value) || trim($value) == '') {
        return $this->setAdditionalProperty('caption', null);
      } else {
        return $this->setAdditionalProperty('caption', $value);
      } // if
    } // setCaption
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Validate before save
     * 
     * @param array $errors
     */
    function validate(ValidationErrors &$errors) {
      if(!$this->validatePresenceOf('type')) {
        $errors->addError(lang('Home screen widget type is required'), 'type');
      } // if
      
      if(!$this->validatePresenceOf('homescreen_tab_id')) {
        $errors->addError(lang('Home screen tab is required'), 'homescreen_tab_id');
      } // if
      
      if(!$this->validatePresenceOf('column_id')) {
        $errors->addError(lang('Home screen tab column is required'), 'column_id');
      } // if
    } // validate
  
  }