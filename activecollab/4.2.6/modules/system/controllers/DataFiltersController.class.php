<?php

  // Build on top of framework level controller
  AngieApplication::useController('fw_data_filters', REPORTS_FRAMEWORK);

  /**
   * Application level data filters controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  abstract class DataFiltersController extends FwDataFiltersController {

  }