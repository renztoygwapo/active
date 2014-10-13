<?php

  /**
   * on_homescreen_widget_types event handler
   * 
   * @package angie.frameworks.tasks
   * @subpackage handlers
   */

  /**
   * Handle on_homescreen_widget_types event
   * 
   * @param array $types
   * @param IUser $user
   */
  function tasks_handle_on_homescreen_widget_types(&$types, IUser &$user) {
    $types[] = new TasksFilterHomescreenWidget();
    $types[] = new MyTasksHomescreenWidget();
    $types[] = new DelegatedTasksHomescreenWidget();
    $types[] = new UnassignedTasksHomescreenWidget();
  } // tasks_handle_on_homescreen_widget_types