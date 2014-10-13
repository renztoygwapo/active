<?php

  /**
   * Priority inspector titlebar widget defintiion class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class PriorityInspectorTitlebarWidget extends InspectorElement {
    
    /**
     * Function which will render the property
     * 
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) { App.Inspector.TitlebarWidgets.Priority.apply(field, [object, client_interface]) })';
    } // render    
  } // PriorityInspectorTitlebarWidget