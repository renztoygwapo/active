<?php

  /**
   * Project budget property defintiion class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ProjectBudgetInspectorProperty extends InspectorProperty {    
    /**
     * Additional properties
     * 
     * @var array
     */
    var $additional;
    
    /**
     * Constructor
     * 
     * @param FwApplicationObject $object
     * @param string $content_field
     * @param array $additional
     */
    function __construct($object, $additional = null) {
      $this->additional = $additional;
    } // __construct
    
    /**
     * Function which will render the property
     * 
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) { App.Inspector.Properties.ProjectBudget.apply(field, [object, client_interface, ' . JSON::encode($this->additional) . ']) })';
    } // render

  } // ProjectBudgetInspectorProperty