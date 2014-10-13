<?php
  /**
   * Project inspector property
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ProjectInspectorProperty extends InspectorProperty {

    /**
     * Function which will render the property
     *
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) { App.Inspector.Properties.Project.apply(field, [object, client_interface]) })';
    } // render

  }