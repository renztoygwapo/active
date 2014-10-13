<?php

  /**
   * Milestone property defintion class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class MilestoneInspectorProperty extends InspectorProperty {
    
    /**
     * Function which will render the property
     * 
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) { App.Inspector.Properties.Milestone.apply(field, [object, client_interface]) })';
    } // render

  }