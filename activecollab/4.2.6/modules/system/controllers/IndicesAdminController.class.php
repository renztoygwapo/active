<?php

  // Build on top of framework level controller
  AngieApplication::useController('fw_indices_admin', ENVIRONMENT_FRAMEWORK);

  /**
   * Application level indices administration controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class IndicesAdminController extends FwIndicesAdminController {
    
  }