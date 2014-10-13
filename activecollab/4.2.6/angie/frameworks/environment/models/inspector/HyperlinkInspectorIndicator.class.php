<?php

  /**
   * Hyperlink indicator defintiion class
   *
   * @package angie.frameworks.environment
   * @subpackage models
   */
  class HyperlinkInspectorIndicator extends InspectorProperty {
    
    /**
     * Url variable
     * 
     * @var string
     */
    var $url;
    
    /**
     * Icon variable
     * 
     * @var string
     */
    var $icon;
    
    /**
     * Title variable
     * 
     * @var string
     */
    var $title;
    
    /**
     * Additional variable
     * 
     * @var array
     */
    var $additional;
    
    /**
     * Constructor
     * 
     * @param FwApplicationObject $object
     * @param string $url
     * @param string $icon
     * @param string $title
     * @param mixed $additional
     */
    function __construct($object, $url, $icon, $title, $additional = null) {
    	$this->url = $url;
    	$this->icon = $icon;
    	$this->title = $title;
    	$this->additional = $additional;
    } // __construct
    
    /**
     * Function which will render the indicator
     * 
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) { App.Inspector.Indicators.Hyperlink.apply(field, [object, client_interface, ' . JSON::encode($this->url) . ', ' . JSON::encode($this->icon) . ', ' . JSON::encode($this->title) . ', ' . JSON::encode($this->additional) . ']) })';
    } // render

  }