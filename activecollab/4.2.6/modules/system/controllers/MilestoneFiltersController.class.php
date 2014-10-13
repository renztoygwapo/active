<?php

  // Build on top of data filters controller
  AngieApplication::useController('data_filters', REPORTS_FRAMEWORK_INJECT_INTO);

  /**
   * Milestone filters controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class MilestoneFiltersController extends DataFiltersController {

    /**
     * Return filter class managed by this controller
     *
     * @return string
     */
    function getFilterType() {
      return 'MilestoneFilter';
    } // getFilterType

    /**
     * Return filter ID variable name
     *
     * @return mixed
     */
    function getFilterIdVariableName() {
      return 'milestone_filter_id';
    } // getFilterIdVariableName

  }