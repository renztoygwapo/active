<?php

  /**
   * Open link in new window callback
   * 
   * @package angie.frameworks.enviornment
   * @subpackage models
   */
  class TargetBlankCallback extends JavaScriptCallback {
    
    /**
     * Render callback code
     * 
     * @return string
     */
    function render() {
      return '(function() { $(this).targetBlank(); })';
    } // render
    
  }