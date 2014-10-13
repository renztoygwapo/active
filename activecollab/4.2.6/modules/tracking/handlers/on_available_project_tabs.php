<?php

  /**
   * Tracking module on_available_project_tabs event handler
   * 
   * @package activeCollab.modules.tracking
   * @subpackage handlers
   */

  /**
   * Populate list of available project tabs
   * 
   * @param array $tabs
   */
  function tracking_handle_on_available_project_tabs(&$tabs) {
    $tabs['time'] = lang('Time and Expenses');
  } // tracking_handle_on_available_project_tabs