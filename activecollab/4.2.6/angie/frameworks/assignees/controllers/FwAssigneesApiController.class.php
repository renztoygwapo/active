<?php

  // Build on top of API controller
  AngieApplication::useController('api', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Assignees API controller
   * 
   * @package angie.frameworks.assignees
   * @subpackage controllers
   */
  abstract class FwAssigneesApiController extends ApiController {
  
    /**
     * Show assignment labels
     */
    function labels() {
      $this->response->respondWithData(Labels::findByType('AssignmentLabel'), array(
        'as' => 'labels', 
      ));
    } // labels
    
  }