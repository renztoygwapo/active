<?php

  /**
   * Favorite indicator defintion class
   *
   * @package angie.frameworks.favorite
   * @subpackage models
   */
  class FavoriteInspectorIndicator extends InspectorIndicator {
    
    /**
     * Function which will render the property
     * 
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) { App.Inspector.Indicators.Favorite.apply(field, [object, client_interface]) })';
    } // render
  } // FavoriteInspectorIndicator