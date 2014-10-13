<?php

  /**
   * Simple permalink property defintiion class
   *
   * @package angie.frameworks.environment
   * @subpackage models
   */
  class SimplePermalinkInspectorProperty extends InspectorProperty {
  	
  	/**
  	 * Url variable
  	 * 
  	 * @var string
  	 */
  	var $url;
  	
  	/**
  	 * Label variable
  	 * 
  	 * @var string
  	 */
  	var $label;

    /**
     * URL attributes
     *
     * @var array
     */
    var $attributes;
  	
  	/**
  	 * Constructor
  	 * 
  	 * @param FwApplicationObject $object
  	 * @param string $url
  	 * @param string $label
  	 */
  	function __construct($object, $url, $label, $attributes = array()) {
  		$this->url = $url;
  		$this->label = $label;
      $this->attributes = is_foreachable($attributes) ? $attributes : array();
  	} // __construct
    
    /**
     * Function which will render the property
     * 
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) {' .
      	'App.Inspector.Properties.SimplePermalink.apply(field, [object, client_interface, ' . JSON::encode($this->url) . ', ' . JSON::encode($this->label) . ', ' . JSON::encode($this->attributes) . '])' .
      '})';
    } // render

  } // SimplePermalinkProperty