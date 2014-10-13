<?php

  /**
   * JavaScript callback defintiion class
   *
   * @package angie.classes.json
   */
  abstract class JavaScriptCallback implements IJavaScriptCallback, IJSON {
    
    /**
     * Convert current object to JSON
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return string
     */
    function toJSON(IUser $user, $detailed = false, $for_interface = false) {
      return JSON::encode($this->render());
    } // toJSON
    
  }