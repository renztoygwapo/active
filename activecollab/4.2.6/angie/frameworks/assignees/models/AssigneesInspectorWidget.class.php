<?php

  /**
   * Assignees widget defintion class
   *
   * @package angie.frameworks.assignees
   * @subpackage models
   */
  class AssigneesInspectorWidget extends InspectorWidget {
    
    /**
     * Function that will render the widget
     * 
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) { App.Inspector.Widgets.Assignees.apply(field, [object, client_interface]) })';
    } // render    
    
  }