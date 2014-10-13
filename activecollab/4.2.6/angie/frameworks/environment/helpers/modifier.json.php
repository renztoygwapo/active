<?php

  /**
   * JSON modifier implementation
   */

  /**
   * Encode data to JSON
   *
   * @param mixed $data
   * @param IUser $user
   * @param boolean $detailed
   * @param boolean $for_interface
   * @return string
   */
  function smarty_modifier_json($data, $user = null, $detailed = false, $for_interface = false) {
    return JSON::encode($data, $user, $detailed, $for_interface);
  } // smarty_modifier_json