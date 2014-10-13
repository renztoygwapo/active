<?php

  /**
   * ProjectRequestClient property defintion class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ProjectRequestClientInspectorProperty extends InspectorProperty {
    
    /**
     * Function which will render the property
     * 
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) { App.Inspector.Properties.ProjectRequestClient.apply(field, [object, client_interface]) })';
    } // render    
  }