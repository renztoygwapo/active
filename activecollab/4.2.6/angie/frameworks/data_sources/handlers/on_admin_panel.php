<?php

  /**
   * on_admin_panel event handler
   *
   * @package angie.frameworks.data_sources
   * @subpackage handlers
   */

  /**
   * Handle on_admin_panel event
   *
   * @param AdminPanel $admin_panel
   */
  function data_sources_handle_on_admin_panel(AdminPanel &$admin_panel) {
    $admin_panel->addToTools('data_sources', lang('Data Sources'), Router::assemble('data_sources'), AngieApplication::getImageUrl('admin_panel/importer.png', DATA_SOURCES_FRAMEWORK));
  } // data_sources_handle_on_admin_panel