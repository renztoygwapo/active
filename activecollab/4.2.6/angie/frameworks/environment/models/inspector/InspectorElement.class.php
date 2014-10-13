<?php

  /**
   * Inspector element defintion class
   *
   * @package angie.frameworks.environment
   * @subpackage models
   */
  abstract class InspectorElement implements IJSON {
    
    /**
     * Render callback for rendering
     *
     * @return string
     */
    abstract function render();
    
    /**
     * Convert current object to JSON
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return string
     */
    function toJSON(IUser $user, $detailed = false, $for_interface = false) {
      return JSON::encode(array(
        'render' => $this->render(),
      ));
    } // toJSON
    
  }