<?php

  // Build on top of API controller
  AngieApplication::useController('api', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Trackig module API controller
   * 
   * @package activeCollab.modules.tracking
   * @subpackage controllers
   */
  class TrackingApiController extends ApiController {

    /**
     * Return list of available job types
     */
    function job_types() {
      $this->response->respondWithData(JobTypes::findAvailableTo($this->logged_user), array(
        'as' => 'job_types'
      ));
    } // job_types

    /**
     * Return list of available expense categories
     */
    function expense_categories() {
      $this->response->respondWithData(ExpenseCategories::find(), array(
        'as' => 'expense_categories',
      ));
    } // expense_categories

  }