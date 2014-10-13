<?php

  /**
   * on_hourly event handler implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage handlers
   */

  /**
   * Handle on_hourly event
   */
  function system_handle_on_hourly() {
    Projects::cleanupSoftDeletedProjects();
  } // system_handle_on_hourly