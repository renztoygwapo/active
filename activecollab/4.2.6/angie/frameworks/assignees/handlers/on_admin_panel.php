<?php

  /**
   * on_admin_panel event handler implementation
   * 
   * @package angie.frameworks.assignees
   * @subpackage handlers
   */

  /**
   * Handle on_admin_panel event
   * 
   * @param AdminPanel $admin_panel
   */
  function assignees_handle_on_admin_panel(AdminPanel &$admin_panel) {
    $admin_panel->addToGeneral('assignment_labels_admin', lang('Assignment Labels'), Router::assemble('assignments_admin_labels'), AngieApplication::getImageUrl('admin_panel/assignment-labels.png', ASSIGNEES_FRAMEWORK));
  } // assignees_handle_on_admin_panel