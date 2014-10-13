<?php

  /**
   * Related tasks inspector property
   *
   * @package activeCollab.modules.tasks
   * @subpackage models
   */
  class RelatedTasksInspectorProperty extends InspectorProperty {

    /**
     * Function which will render the property
     *
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) { App.Inspector.Properties.RelatedTasks.apply(field, [object, client_interface]) })';
    } // render

  }