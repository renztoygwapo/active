<?php

  /**
   * Discussions module on_available_project_tabs event handler
   * 
   * @package activeCollab.modules.discussions
   * @subpackage handlers
   */

  /**
   * Populate list of available project tabs
   * 
   * @param array $tabs
   */
  function discussions_handle_on_available_project_tabs(&$tabs) {
    $tabs['discussions'] = lang('Discussions');
  } // discussions_handle_on_available_project_tabs