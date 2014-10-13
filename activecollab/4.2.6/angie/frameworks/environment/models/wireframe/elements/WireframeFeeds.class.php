<?php

  /**
   * List of registered RSS feeds for the current page
   * 
   * @package angie.frameworks.environment
   * @subpackage models
   */
  class WireframeFeeds extends NamedList implements IWireframeElement {
    
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
     * @param string $label
     * @param string $url
     * @param string $type
     * @return array
     */
    function add($name, $label, $url, $type = 'application/rss+xml') {
      return parent::add($name, array(
        'label' => $label, 
      	'url' => $url, 
      	'type' => $type, 
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