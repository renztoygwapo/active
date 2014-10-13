<?php

  // Build on top of data filters controller
  AngieApplication::useController('data_filters', REPORTS_FRAMEWORK_INJECT_INTO);

  /**
   * Assignment filters controler
   *
   * @package activeCollab.modules.resources
   * @subpackage controllers
   */
  class AssignmentFiltersController extends DataFiltersController {

    /**
     * This report should be available to users with non-managerial permissions
     *
     * @var bool
     */
    protected $check_reports_access_permissions = false;

    /**
     * Return filter class managed by this controller
     *
     * @return string
     */
    function getFilterType() {
      return 'AssignmentFilter';
    } // getFilterType

    /**
     * Return filter ID variable name
     *
     * @return mixed
     */
    function getFilterIdVariableName() {
      return 'assignment_filter_id';
    } // getFilterIdVariableName
    
  }