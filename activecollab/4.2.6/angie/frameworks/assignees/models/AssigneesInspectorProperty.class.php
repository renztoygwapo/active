<?php

  /**
   * Assignees property defintion class
   *
   * @package angie.frameworks.assignees
   * @subpackage models
   */
  class AssigneesInspectorProperty extends InspectorProperty {
    
    /**
     * Function that will render the property
     * 
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) { App.Inspector.Properties.Assignees.apply(field, [object, client_interface]) })';
    } // render    
    
  } // AssigneesInspectorProperty