<?php

  /**
   * Category property defintion class
   *
   * @package angie.frameworks.categories
   * @subpackage models
   */
  class CategoryInspectorProperty extends InspectorProperty {
    
    /**
     * Function which will render the property
     * 
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) { App.Inspector.Properties.Category.apply(field, [object, client_interface]) })';
    } // render

  }