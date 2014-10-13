<?php

  /**
   * Notebook inspector property
   *
   * @package activeCollab.modules.notebooks
   * @subpackage models
   */
  class NotebookInspectorProperty extends InspectorProperty {

    /**
     * Function which will render the property
     *
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) { App.Inspector.Properties.Notebook.apply(field, [object, client_interface]) })';
    } // render

  }