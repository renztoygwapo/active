<?php

  /**
   * Custom field inspector property defintiion class
   *
   * @package angie.frameworks.custom_fields
   * @subpackage models
   */
  class CustomFieldInspectorProperty extends InspectorElement {
    
    /**
     * Function which will render the property
     * 
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) { App.Inspector.Properties.CustomField.apply(field, [object, client_interface]) })';
    } // render

  }