<?php

  /**
   * SourceCommit commited by property defintiion class
   *
   * @package angie.frameworks.environment
   * @subpackage models
   */
  class SourceCommitCommitedByInspectorProperty extends InspectorProperty {
  	
  	/**
  	 * Field Name
  	 * 
  	 * @var string
  	 */
  	var $field_name;
  	
  	/**
  	 * Constructor
  	 * 
  	 * @param SourceCommit $object
  	 * @param string $field_name
  	 */
  	function __construct(SourceCommit $object, $field_name) {
  		$this->field_name = $field_name;
  	}
    
    /**
     * Function which will render the property
     * 
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) { App.Inspector.Properties.SourceCommitCommitedBy.apply(field, [object, client_interface, "' . $this->field_name . '"]) })';
    } // render    
  } // SourceCommitCommitedByInspectorProperty