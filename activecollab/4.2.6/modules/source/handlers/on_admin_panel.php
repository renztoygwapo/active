<?php

  /**
   * on_admin_panel event handler
   * 
   * @package activeCollab.modules.source
   * @subpackage handlers
   */

  /**
   * Handle on_admin_panel event
   * 
   * @param AdminPanel $admin_panel
   */
  function source_handle_on_admin_panel(AdminPanel &$admin_panel) {
    $admin_panel->addToProjects('source', lang('Source Settings'), Router::assemble('admin_source'), AngieApplication::getImageUrl('admin_panel/source-settings.png', SOURCE_MODULE));
  } // source_handle_on_admin_panel