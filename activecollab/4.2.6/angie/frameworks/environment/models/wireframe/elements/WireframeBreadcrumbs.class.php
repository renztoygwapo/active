<?php

  /**
   * Basic breadcrumbs implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage models
   */
  class WireframeBreadcrumbs extends NamedList implements IWireframeElement {
    
    /**
     * All data only to be appended to the list
     *
     * @var boolean
     */
    protected $append_only = true;
    
    /**
     * Add bread crumb to the list
     * 
     * @param string $name
     * @param string $text
     * @param string $url
     * @return array
     */
    function add($name, $text, $url = null) {
      return parent::add($name, array(
        'text' => $text, 
      	'url' => $url, 
      ));
    } // add
    
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