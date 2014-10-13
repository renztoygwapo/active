<?php

  /**
   * Basic page actions implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage models
   */
  abstract class WireframeActions extends NamedList implements IWireframeElement {
    
    /**
     * Add new action
     */
    function add($name, $text, $url = null, $additional = null) {
      if($text instanceof WireframeAction) {
        return parent::add($name, $text);
      } else {
        return parent::add($name, $this->preparePageAction($name, $text, $url, $additional));
      } // if
    } // add
    
    /**
     * Begin the list with given item
     * 
     * @param string $name
     * @param string $text
     * @param string $url
     * @param array $additional
     */
    function beginWith($name, $text, $url = null, $additional = null) {
      if($text instanceof WireframeAction) {
        return parent::beginWith($name, $text);
      } else {
        return parent::beginWith($name, $this->preparePageAction($name, $text, $url, $additional));
      } // if
    } // beginWith
    
    /**
     * Add action before specific action
     * 
     * @param string $name
     * @param string $text
     * @param string $url
     * @param string $before
     * @param array $additional
     */
    function addBefore($name, $text, $url = null, $before, $additional = null) {
      if($text instanceof WireframeAction) {
        return parent::addBefore($name, $text);
      } else {
        return parent::addBefore($name, $this->preparePageAction($name, $text, $url, $additional), $before);
      } // if
    } // addBefore
    
    /**
     * Add action after specific action
     * 
     * @param string $name
     * @param string $text
     * @param string $url
     * @param string $after
     * @param array $additional
     */
    function addAfter($name, $text, $url = null, $after, $additional = null) {
      if($text instanceof WireframeAction) {
        return parent::addAfter($name, $text);
      } else {
        return parent::addAfter($name, $this->preparePageAction($name, $text, $url, $additional), $after);
      } // if
    } // addAfter
    
    /**
     * Prepare single item
     * 
     * @param string $name
     * @param string $text
     * @param string $url
     * @param array $additional
     * @return array
     */
    protected function preparePageAction($name, $text, $url, $additional = null) {
      if(empty($additional)) {
        $additional = array();
      } // if
      
      if(empty($additional['id'])) {
        $additional['id'] = 'wireframe_action_' . $name;
      } // if
      
      return new WireframeAction($text, $url, $additional);
    } // preparePageAction
    
    // ---------------------------------------------------
    //  Interface implementation
    // ---------------------------------------------------
    
    /**
     * Event that is triggered when page object is set in wireframe
     * 
     * @param ApplicationObject $object
     * @param IUser $user
     */
    function onPageObjectSet($object, IUser $user) {
      
    } // onPageObjectSet
    
    /**
     * On body classes event handler
     * 
     * @param array $classes
     */
    function onBodyClasses(&$classes) {
    
    } // onBodyClasses
    
  }