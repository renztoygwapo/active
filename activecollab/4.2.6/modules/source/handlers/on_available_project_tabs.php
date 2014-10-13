<?php

  /**
   * Source module on_available_project_tabs event handler
   * 
   * @package activeCollab.modules.source
   * @subpackage handlers
   */

  /**
   * Populate list of available project tabs
   * 
   * @param array $tabs
   */
  function source_handle_on_available_project_tabs(&$tabs) {
    $tabs['source'] = lang('Source');
  } // source_handle_on_available_project_tabs