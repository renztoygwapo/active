<?php

  /**
   * Page actions optimized for phone interface
   * 
   * @package angie.modules.environment
   * @subpackage models
   */
  class PhoneWireframeActions extends WireframeActions {
  
    /**
     * Primary action
     *
     * @var WireframeAction
     */
    protected $primary;
    
    /**
     * Return primary action
     * 
     * @return WireframeAction
     */
    function getPrimary() {
      return $this->primary;
    } // getPrimary
    
    /**
     * Set primary action
     * 
     * @param WireframeAction $action
     * @return WireframeAction
     */
    function setPrimary(WireframeAction $action) {
      if($action instanceof WireframeAction || $action === null) {
        $this->primary = $action;
        
        return $this->primary;
      } else {
        throw new InvalidInstanceError('action', $action, 'WireframeAction');
      } // if
    } // setPrimary
    
    /**
     * Do add item to the list
     * 
     * @param string $name
     * @param mixed $data
     * @param mixed $options
     * @return mixed
     * @throws InvalidInstanceError
     */
    protected function doAdd($name, $data, $options = null) {
      if($data instanceof WireframeAction) {
        return $data->getAdditionalProperty('primary') ? $this->setPrimary($data) : parent::doAdd($name, $data, $options);
      } else {
        throw new InvalidInstanceError('data', $data, 'WireframeAction');
      } // if
    } // doAdd
    
    // ---------------------------------------------------
    //  Interface Implementation
    // ---------------------------------------------------
    
    /**
     * Event that is triggered when page object is set in wireframe
     * 
     * @param ApplicationObject $object
     * @param IUser $user
     */
    function onPageObjectSet($object, IUser $user) {
      parent::onPageObjectSet($object, $user);
      
      list($primary_option_name, $primary_option) = $object->getPrimaryOption($user, AngieApplication::INTERFACE_PHONE);
      
      if($primary_option_name && $primary_option instanceof WireframeAction) {
        $this->setPrimary($primary_option);
      } // if
      
      $options = $object->getOptions($user, AngieApplication::INTERFACE_PHONE);
      
      if($options instanceof NamedList) {
        foreach($options as $k => $v) {
          if($k == $primary_option_name) {
            continue;
          } // if
          
          $text = array_var($v, 'text', null, true);
          $url = array_var($v, 'url', null, true);
          
          $this->add($k, $text, $url, $v);
        } // if
      } // if
    } // onProjectObjectSet
    
  }