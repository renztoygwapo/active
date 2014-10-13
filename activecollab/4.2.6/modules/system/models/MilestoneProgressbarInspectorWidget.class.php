<?php

  /**
   * Milestone progressbar widget defintion class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class MilestoneProgressbarInspectorWidget extends InspectorWidget {
    
    /**
     * Function that will render the widget
     * 
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) { App.Inspector.Widgets.MilestoneProgressbar.apply(field, [object, client_interface]) })';
    } // render    
    
  } // MilestoneProgressbarInspectorWidget