<?php

  // Build on top of framework level implementation
  AngieApplication::useController('fw_api', ENVIRONMENT_FRAMEWORK);

  /**
   * API specific calls controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ApiController extends FwApiController {
    
    /**
     * List all defined job types
     */
    function index() {
      $this->response->respondWithData(JobTypes::findAvailableTo($this->logged_user), array(
        'as' => 'job_types', 
      ));
    } // index
    
  }