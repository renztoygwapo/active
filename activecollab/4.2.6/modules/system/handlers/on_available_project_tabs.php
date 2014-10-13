<?php

  /**
   * System module on_available_project_tabs event handler
   * 
   * @package activeCollab.modules.system
   * @subpackage handlers
   */

  /**
   * Populate list of available project tabs
   * 
   * @param array $tabs
   */
  function system_handle_on_available_project_tabs(&$tabs) {
    $tabs['outline'] = lang('Outline');
    $tabs['milestones'] = lang('Milestones');
  } // system_handle_on_available_project_tabs