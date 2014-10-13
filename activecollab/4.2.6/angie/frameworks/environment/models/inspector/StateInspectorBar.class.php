<?php

  /**
   * state bar class
   *
   * @package angie.frameworks.environment
   * @subpackage models
   */
  class StateInspectorBar extends InspectorProperty {
    
    /**
     * Function which will render the property
     * 
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) { App.Inspector.Bars.State.apply(field, [object, client_interface]) })';
    } // render    
  } // StateInspectorBar