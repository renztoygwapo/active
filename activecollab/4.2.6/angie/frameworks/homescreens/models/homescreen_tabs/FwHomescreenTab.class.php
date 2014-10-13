<?php

  /**
   * Framework level homescreen tab implementation
   * 
   * @package angie.frameworks.homescreens
   * @subpackage models
   */
  abstract class FwHomescreenTab extends BaseHomescreenTab implements IRoutingContext {
    
    // Column classes
    const WIDE_COLUMN = 'wide';
    const NARROW_COLUMN = 'narrow';
  
    /**
     * This home screen does not accept widgets
     *
     * @var boolean
     */
    protected $accept_widgets = false;
    
    /**
     * Return tab description
     * 
     * @return string
     */
    abstract function getDescription();

    /**
     * Return parent user
     *
     * @return User
     */
    function getUser() {
      return DataObjectPool::get('User', $this->getUserId());
    } // getUser

    /**
     * Set user instance
     *
     * @param User $user
     * @param bool $save
     */
    function setUser(User $user, $save = true) {
      $this->setUserId($user->getId());

      if($save) {
        $this->save();
      } // if
    } // setUser
    
    // ---------------------------------------------------
    //  Describe
    // ---------------------------------------------------
    
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
      $result = parent::describe($user);
      
      $result['description'] = $this->getDescription();
      
      if($for_interface) {
        $result['manager'] = $this->renderManager($user);
      } // if
      
      return $result;
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
    //  Renderer
    // ---------------------------------------------------
    
    /**
     * Render tab content
     * 
     * @param IUser $user
     * @return string
     */
    abstract function render(IUser $user);
    
    /**
     * Render management widget
     * 
     * @param IUser $user
     * @return string
     */
    function renderManager(IUser $user) {
      if($this->hasOptions()) {
        $view = SmartyForAngie::getInstance()->createTemplate(AngieApplication::getViewPath('_homescreen_tab_options', null, HOMESCREENS_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT));
        
        $view->assign(array(
          'homescreen_tab' => &$this, 
          'options' => $this->renderOptions($user), 
        ));
        
        return $view->fetch();
      } else {
        return '<div class="homescreen_tab_manager without_options"><p class="homescreen_tab_name">' . clean($this->getName()) . '</p><p class="homescreen_tab_desc">' . lang('No additional options available') . '</p></div>';
      } // if
    } // renderManager
    
    // ---------------------------------------------------
    //  Options
    // ---------------------------------------------------
    
    /**
     * Returns true if this widget has additional options
     * 
     * @return boolean
     */
    protected function hasOptions() {
      return false;
    } // hasOptions
    
    /**
     * Render widget options form section
     * 
     * @param IUser $user
     * @return string
     */
    protected function renderOptions(IUser $user) {
      return '';
    } // renderOptions
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------

    /**
     * Return URL of page that will render this home screen tab
     *
     * @return string
     */
    function getHomescreenTabUrl() {
      return Router::assemble('custom_tab', array('homescreen_tab_id' => $this->getId()));
    } // getHomescreenTabUrl
    
    // ---------------------------------------------------
    //  Interface implementation
    // ---------------------------------------------------
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return $this->getUser()->getRoutingContext() . '_homescreen_tab';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array_merge($this->getUser()->getRoutingContextParams(), array('homescreen_tab_id' => $this->getId()));
    } // getRoutingContextParams
    
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
      if($user instanceof User) {
        return $user->isAdministrator() || $user->getId() == $this->getUserId();
      } else {
        return false;
      } // if
    } // canEdit
    
    /**
     * Return true if $user can remove this destkop
     * 
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      if($user instanceof User) {
        return $user->isAdministrator() || $user->getId() == $this->getUserId();
      } else {
        return false;
      } // if
    } // canDelete
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Copy this tab and all of its settings and widgets to a home screen
     * 
     * @param User $user
     * @return HomescreenTab
     */
    function copyTo(User $user) {
      $class_name = get_class($this);
      
      // Create a home screen tab of the same class as this tab
      $homescreen_tab = new $class_name();
      
      $homescreen_tab->setUserId($user->getId());
      $homescreen_tab->setName($this->getName());
      $homescreen_tab->setPosition($this->getPosition());
      
      foreach($this->getAdditionalProperties() as $k => $v) {
        $homescreen_tab->setAdditionalProperty($k, $v);
      } // foreach
      
      $homescreen_tab->save();
      
      return $homescreen_tab;
    } // copyTo
    
    /**
     * Validate before save
     * 
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if(!$this->validatePresenceOf('type')) {
        $errors->addError(lang('Home screen tab type is required'), 'type');
      } // if
      
      if(!$this->validatePresenceOf('user_id')) {
        $errors->addError(lang('User is required'), 'user_id');
      } // if
      
      if(!$this->validatePresenceOf('name')) {
        $errors->addError(lang('Home screen tab name is required'), 'name');
      } // if
    } // validate
    
  }