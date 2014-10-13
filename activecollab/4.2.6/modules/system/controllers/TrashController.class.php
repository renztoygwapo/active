<?php

  // Build on top of framework level controller
  AngieApplication::useController('fw_trash', ENVIRONMENT_FRAMEWORK);

  /**
   * Trash controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class TrashController extends FwTrashController {
  
  }