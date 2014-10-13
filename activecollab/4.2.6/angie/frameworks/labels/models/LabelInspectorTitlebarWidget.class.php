<?php

  /**
   * Label inspector titlebar widget defintiion class
   *
   * @package angie.frameworks.labels
   * @subpackage models
   */
  class LabelInspectorTitlebarWidget extends InspectorElement {
    
    /**
     * Function which will render the property
     * 
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) { App.Inspector.TitlebarWidgets.Label.apply(field, [object, client_interface]) })';
    } // render    
  } // LabelInspectorTitlebarWidget