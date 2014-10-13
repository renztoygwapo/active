<?php

  /**
   * on_custom_field_disabled even handler implementation
   *
   * @package activeCollab.modules.tasks
   * @subpackage handlers
   */

  /**
   * Handle on_custom_field_disabled event
   *
   * @param string $type
   * @param string $field_name
   */
  function tasks_handle_on_custom_field_disabled($type, $field_name) {
    if($type == 'Task' && in_array($field_name, array('custom_field_1', 'custom_field_2', 'custom_field_3'))) {
      DB::execute('UPDATE ' . TABLE_PREFIX . "project_objects SET $field_name = NULL WHERE type = 'Task'");
    } // if
  } // tasks_handle_on_custom_field_disabled