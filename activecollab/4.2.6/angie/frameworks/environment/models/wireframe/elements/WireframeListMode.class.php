<?php

  /**
   * List mode toggler
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class WireframeListMode implements IWireframeElement {
    
    /**
     * Enabled indicator
     *
     * @var boolead
     */
    private $is_enabled = false;
    
    /**
     * Enable list mode
     */
    function enable() {
      $this->is_enabled = true;
    } // enable
    
    /**
     * Disable list mode
     */
    function disable() {
      $this->is_enabled = false;
    } // disable
    
    /**
     * Returns true if list mode is enabled, false otherwise
     * 
     * @return boolean
     */
    function isEnabled() {
      return $this->is_enabled;
    } // isEnabled
    
    // ---------------------------------------------------
    //  Events
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
     * On body classes event
     * 
     * @param array $classes
     */
    function onBodyClasses(&$classes) {
      if($this->isEnabled()) {
        $classes[] = 'list_mode';
      } // if
    } // onBodyClasses
    
  }