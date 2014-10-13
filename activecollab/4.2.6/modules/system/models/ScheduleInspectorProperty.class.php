<?php

  /**
   * Schedule property defintion class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ScheduleInspectorProperty extends InspectorProperty {
    
    /**
     * Function which will render the property
     * 
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) { App.Inspector.Properties.Schedule.apply(field, [object, client_interface]) })';
    } // render
  } // ScheduleInspectorProperty