<?php

  /**
   * Basic page tabs implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage models
   */
  class WireframeTabs extends NamedList implements IWireframeElement {
    
    /**
     * Current page tab
     *
     * @var string
     */
    protected $current_tab;
    
    /**
     * Return curent page tab
     * 
     * @return string
     */
    function getCurrentTab() {
      return $this->current_tab;
    } // getCurrentTab
    
    /**
     * Set current tab to a given value
     * 
     * @param string $value
     * @throws InvalidParamError
     */
    function setCurrentTab($value) {
      if(isset($this->data[$value])) {
        $this->current_tab = $value;
      } else {
        throw new InvalidParamError('value', $value, "'$value' tab does not exist");
      } // if
    } // setCurrentTab
    
    /**
     * Separator counter
     *
     * @var integer
     */
    protected $next_separator_num = 1;
    
    /**
     * Add tab separator
     * 
     * @return array
     */
    function addSeparator() {
      $counter = $this->next_separator_num++;
      
      return parent::add("separator-{$counter}", array(
        'text' => '-', 
      ));
    } // addSeparator
    
    /**
     * Add new tab
     * 
     * @param string $name
     * @param string $text
     * @param string $url
     * @param string $icon
     * @param boolean $current
     * @return array
     */
    function add($name, $text, $url, $icon = null, $current = false) {
      $tab = parent::add($name, $this->preparePageTab($text, $url, $icon));
      
      if($current) {
        $this->setCurrentTab($name);
      } // if
      
      return $tab;
    } // add
    
    /**
     * Add new icon tab
     * 
     * @param string $name
     * @param string $text
     * @param string $url
     * @param string $icon
     * @return array
     */
    function addIcon($name, $text, $url, $icon = null) {
      return parent::add("icon-$name", $this->preparePageTab($text, $url, $icon));
    } // addIcon
    
    /**
     * Begin the list with given item
     * 
     * @param string $name
     * @param string $text
     * @param string $url
     * @param array $additional
     */
    function beginWith($name, $text, $url) {
      return parent::beginWith($name, $this->preparePageTab($text, $url));
    } // beginWith
    
    /**
     * Add tab before specific tab
     * 
     * @param string $name
     * @param string $text
     * @param string $url
     * @param string $before
     * @param array $additional
     */
    function addBefore($name, $text, $url, $before) {
      return parent::add($name, $this->preparePageTab($text, $url), $before);
    } // addBefore
    
    /**
     * Add tab after specific tab
     * 
     * @param string $name
     * @param string $text
     * @param string $url
     * @param string $after
     * @param array $additional
     */
    function addAfter($name, $text, $url, $after) {
      return parent::addAfter($name, $this->preparePageTab($text, $url), $after);
    } // addAfter
    
    /**
     * Prepare single page tab
     * @param string $text
     * @param string $url
     * @param string $icon
     * @return array
     */
    protected function preparePageTab($text, $url, $icon) {
      return array(
        'text' => $text, 
      	'url' => $url,
      	'icon' => $icon
      );
    } // preparePageTab
    
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