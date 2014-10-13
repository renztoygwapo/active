<?php

  /**
   * Invited on inspector property
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class InvitedOnInspectorProperty extends InspectorProperty {

    /**
     * Function which will render the property
     *
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) { App.Inspector.Properties.InvitedOn.apply(field, [object, client_interface]) })';
    } // render

  }