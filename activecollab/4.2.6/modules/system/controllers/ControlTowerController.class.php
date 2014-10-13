<?php

  // Build on top of framework level controller
  AngieApplication::useController('fw_control_tower', ENVIRONMENT_FRAMEWORK);

  /**
   * Application level control tower controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ControlTowerController extends FwControlTowerController {

  }