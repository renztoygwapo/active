<?php

  /**
   * Simple boolean property defintiion class
   *
   * @package angie.frameworks.environment
   * @subpackage models
   */
  class SimpleBooleanInspectorProperty extends InspectorProperty {
  	
  	/**
  	 * Field which will be checked for boolean expression
  	 * 
  	 * @var string
  	 */
  	var $field;
  	
  	/**
  	 * Label which will be written if field is true
  	 * 
  	 * @var string
  	 */
  	var $true_label;
  	
  	/**
  	 * Label which will be written if field is false
  	 * 
  	 * @var string
  	 */
  	var $false_label;
    
    /**
     * Constructor
     * 
     * @param FwApplicationObject $object
     * @param string $url
     * @param string $label
     */
    function __construct($object, $field, $true_label, $false_label) {
      $this->field = $field;
      $this->true_label = $true_label;
      $this->false_label = $false_label;
    } // __construct
    
    /**
     * Function which will render the property
     * 
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) { App.Inspector.Properties.SimpleBoolean.apply(field, [object, client_interface, "' . $this->field . '", "' . $this->true_label . '", "' . $this->false_label . '"]) })';
    } // render

  } // SimpleBooleanInspectorProperty