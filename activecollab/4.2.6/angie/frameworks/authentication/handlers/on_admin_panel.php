<?php

  /**
   * on_admin_panel event handler
   * 
   * @package angie.frameworks.authentication
   * @subpackage handlers
   */

  /**
   * Handle on_admin_panel event
   * 
   * @param AdminPanel $admin_panel
   */
  function authentication_handle_on_admin_panel(AdminPanel &$admin_panel) {
    $admin_panel->addToGeneral('roles', lang('Roles and Permissions'), Router::assemble('admin_roles'), AngieApplication::getImageUrl('admin_panel/roles.png', AUTHENTICATION_FRAMEWORK));
  } // authentication_handle_on_admin_panel