<?php

  // Build on top of framework level implementation
  AngieApplication::useController('fw_disk_space_admin', ENVIRONMENT_FRAMEWORK);

  /**
   * Application level disk space admin controller implementation
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class DiskSpaceAdminController extends FwDiskSpaceAdminController {

  }