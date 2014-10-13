<?php

  /**
   * Map modifier implementation
   *
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Prepare and return $value as a map instance
   *
   * @param mixed $value
   * @param User $user
   * @param boolean $detailed
   * @param string $for_interface
   * @return string
   */
  function smarty_modifier_map($value, $user = null, $detailed = false, $for_interface = false) {
    return 'new App.Map(' . JSON::map($value, $user, $detailed, $for_interface) . ')';
  } // smarty_modifier_map