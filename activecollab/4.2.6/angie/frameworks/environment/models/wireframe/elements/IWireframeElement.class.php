<?php

  /**
   * Interface that all wireframe elements implement
   * 
   * @package angie.frameworks.environment
   * @subpackage models
   */
  interface IWireframeElement {
  
    /**
     * Event that is triggered when page object is set in wireframe
     * 
     * @param ApplicationObject $object
     * @param IUser $user
     */
    function onPageObjectSet($object, IUser $user);
    
    /**
     * On body classes event
     * 
     * @param array $classes
     */
    function onBodyClasses(&$classes);
    
  }