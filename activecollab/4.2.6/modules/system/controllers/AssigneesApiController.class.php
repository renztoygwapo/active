<?php

  // Build on top of framework level controller
  AngieApplication::useController('fw_assignees_api', ASSIGNEES_FRAMEWORK);

  /**
   * Assignees API controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class AssigneesApiController extends FwAssigneesApiController {
  
  }