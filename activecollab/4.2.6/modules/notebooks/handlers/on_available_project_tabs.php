<?php

  /**
   * Notebooks module on_available_project_tabs event handler
   * 
   * @package activeCollab.modules.notebooks
   * @subpackage handlers
   */

  /**
   * Populate list of available project tabs
   * 
   * @param array $tabs
   */
  function notebooks_handle_on_available_project_tabs(&$tabs) {
    $tabs['notebooks'] = lang('Notebooks');
  } // notebooks_handle_on_available_project_tabs