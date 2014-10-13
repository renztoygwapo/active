<?php

  /**
   * Sharing indicator defintion class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class SharingInspectorIndicator extends InspectorIndicator {
    
    /**
     * Function which will render the property
     * 
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) { App.Inspector.Indicators.Sharing.apply(field, [object, client_interface]) })';
    } // render
  } // SharingInspectorIndicator