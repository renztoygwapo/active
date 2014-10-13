<?php

  /**
   * Visibility indicator defintion class
   *
   * @package angie.frameworks.environment
   * @subpackage models
   */
  class VisibilityInspectorIndicator extends InspectorIndicator {
    
    /**
     * Function which will render the property
     * 
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) { App.Inspector.Indicators.Visibility.apply(field, [object, client_interface]) })';
    } // render

  }