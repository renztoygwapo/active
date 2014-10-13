<?php

  /**
   * JSON interface
   *
   * @package angie.library.json
   */
  interface IJSON {
    
    /**
     * Convert current object to JSON
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return string
     */
    function toJSON(IUser $user, $detailed = false, $for_interface = false);
    
  }