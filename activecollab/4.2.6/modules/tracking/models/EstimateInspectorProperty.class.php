<?php

  /**
   * Estimate inspector property defintiion class
   *
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  class EstimateInspectorProperty extends InspectorElement {
    
    /**
     * Function which will render the property
     * 
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) { App.Inspector.Properties.Estimate.apply(field, [object, client_interface]) })';
    } // render

  }