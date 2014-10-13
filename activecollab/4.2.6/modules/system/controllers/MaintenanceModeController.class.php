<?php

  // Build on top of framework level module
  AngieApplication::useController('fw_maintenance_mode', 'authentication');

  /**
   * Maintenance mode controller implementation
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class MaintenanceModeController extends FwMaintenanceModeController {
    
  }