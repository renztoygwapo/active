<?php

  /**
   * Tasks module on_get_completable_project_object_types events handler
   *
   * @package activeCollab.modules.tasks
   * @subpackage handlers
   */
  
  /**
   * Return completable tasks module types
   *
   * @return string
   */
  function tasks_handle_on_get_completable_project_object_types() {
    return 'Task';
  } // tasks_handle_on_get_completable_project_object_types