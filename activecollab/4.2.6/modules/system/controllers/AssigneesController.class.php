<?php

  // Build on top of fw_assignees controller
  AngieApplication::useController('fw_assignees', ASSIGNEES_FRAMEWORK);

  /**
   * Assignees controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class AssigneesController extends FwAssigneesController {
  
  }