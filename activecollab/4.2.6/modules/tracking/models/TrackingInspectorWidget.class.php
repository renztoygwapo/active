<?php

  /**
   * Tracking widget defintion class
   *
   * @package modules.system.tracking
   * @subpackage models
   */
  class TrackingInspectorWidget extends InspectorWidget {

    /**
     * Parent object
     *
     * @var IInspector
     */
    protected $object;

    /**
     * Constructor
     *
     * @param FwApplicationObject $object
     */
    function __construct($object) {
      $this->object = $object;
    } // __construct
    
    /**
     * Function that will render the widget
     * 
     * @return string
     */
    function render() {
      $currency = null;
      if ($this->object instanceof ProjectObject) {
        $project = $this->object->getProject();
        if ($project instanceof Project) {
          $currency = $project->getCurrency();
        } // if
      } // if

      if($this->object instanceof ITracking) {
        $default_billable_status = $this->object->tracking()->getDefaultBillableStatus();
      } else {
        $default_billable_status = ConfigOptions::getValue('default_billable_status') ? 1 : 0;
      } // if

      return '(function (field, object, client_interface) { App.Inspector.Widgets.Tracking.apply(field, [object, ' . JSON::encode($currency) . ' ,client_interface,' . $default_billable_status . ']) })';
    } // render    
    
  }