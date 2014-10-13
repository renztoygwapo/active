<?php

  /**
   * Subscribe indicator defintion class
   *
   * @package angie.frameworks.favorite
   * @subpackage models
   */
  class SubscribeInspectorIndicator extends InspectorIndicator {
    
    /**
     * Function which will render the indicator
     * 
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) { App.Inspector.Indicators.Subscribe.apply(field, [object, client_interface]) })';
    } // render
  } 