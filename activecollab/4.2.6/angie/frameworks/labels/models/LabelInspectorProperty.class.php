<?php

  /**
   * Label inspector property defintiion class
   *
   * @package angie.frameworks.labels
   * @subpackage models
   */
  class LabelInspectorProperty extends InspectorElement {
    
    /**
     * Function which will render the property
     * 
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) { App.Inspector.Properties.Label.apply(field, [object, client_interface]) })';
    } // render    
  } // LabelInspectorProperty