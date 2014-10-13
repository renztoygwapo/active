<?php

  /**
   * Filess module on_available_project_tabs event handler
   * 
   * @package activeCollab.modules.files
   * @subpackage handlers
   */

  /**
   * Populate list of available project tabs
   * 
   * @param array $tabs
   */
  function files_handle_on_available_project_tabs(&$tabs) {
    $tabs['files'] = lang('Files');
  } // files_handle_on_available_project_tabs