<?php

  /**
   * on_custom_field_disabled even handler implementation
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */

  /**
   * Handle on_custom_field_disabled event
   *
   * @param string $type
   * @param string $field_name
   */
  function system_handle_on_custom_field_disabled($type, $field_name) {
    if($type == 'Project' && in_array($field_name, array('custom_field_1', 'custom_field_2', 'custom_field_3'))) {
      DB::execute('UPDATE ' . TABLE_PREFIX . "projects SET $field_name = NULL");
    } // if
  } // system_handle_on_custom_field_disabled