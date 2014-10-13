<?php

/**
 * Permalink inspector titlebar widget defintiion class
 *
 * @package angie.frameworks.environment
 * @subpackage models
 */
class PermalinkInspectorTitlebarWidget extends InspectorElement {

  /**
   * Function which will render the property
   *
   * @return string
   */
  function render() {
    return '(function (field, object, client_interface) { App.Inspector.TitlebarWidgets.Permalink.apply(field, [object, client_interface]) })';
  } // render
} // PermalinkInspectorTitlebarWidget