<?php

  /**
   * actionOnby property defintiion class
   *
   * @package angie.frameworks.environment
   * @subpackage models
   */
  class ActionOnByInspectorProperty extends InspectorProperty {
  	
  	/**
  	 * Prefix for fields
  	 * 
  	 * @var string
  	 */
  	protected $field_prefix;
  	
  	/**
  	 * Localize time flag
  	 * 
  	 * @var boolean
  	 */
  	protected $localize_time;
  	
  	/**
  	 * Constructor
  	 * 
  	 * If $localize_time is true, system will use user's time zone. For false, GMT will be used
  	 * 
  	 * @param FwApplicationObject $object
  	 * @param string $field_prefix
  	 * @param boolean $localize_time
  	 */
  	function __construct($object, $field_prefix = null, $localize_time = true) {
  		$this->field_prefix = $field_prefix ? $field_prefix : 'created';
  		$this->localize_time = $localize_time;
  	} // __construct
  	
  	/**
  	 * Function which will render the property
  	 * 
  	 * @return string
  	 */
  	function render() {
  		return '(function (field, object, client_interface) { App.Inspector.Properties.ActionOnBy.apply(field, [object, client_interface, \'' . $this->field_prefix . '\', ' . $this->localize_time . ']) })';
  	} // render    
  } // ActionOnByInspectorProperty