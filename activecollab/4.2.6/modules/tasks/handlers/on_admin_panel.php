<?php

  /**
   * on_admin_panel event handler
   * 
   * @package activeCollab.modules.tasks
   * @subpackage handlers
   */

  /**
   * Handle on_admin_panel event
   * 
   * @param AdminPanel $admin_panel
   */
  function tasks_handle_on_admin_panel(AdminPanel &$admin_panel) {
    $admin_panel->addToProjects('tasks_admin', lang('Task Settings'), Router::assemble('tasks_admin'), AngieApplication::getImageUrl('admin_panel/tasks.png', TASKS_MODULE));
  } // tasks_handle_on_admin_panel