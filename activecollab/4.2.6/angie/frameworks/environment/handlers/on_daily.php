<?php

  /**
   * on_daily event handler
   * 
   * @package angie.frameworks.environment
   * @subpackage handlers
   */

  /**
   * Handle on daily task
   */
  function environment_handle_on_daily() {
    Router::cleanUpCache();
    AccessLogs::archive();
    DiskSpace::dailyFreeSpaceCheck();
  } // environment_handle_on_daily