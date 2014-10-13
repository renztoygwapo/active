<?php

  // Build on top of framework controller
  AngieApplication::useController('fw_download', DOWNLOAD_FRAMEWORK);

  /**
   * Application level download controller implementation
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class DownloadController extends FwDownloadController {
    
  }