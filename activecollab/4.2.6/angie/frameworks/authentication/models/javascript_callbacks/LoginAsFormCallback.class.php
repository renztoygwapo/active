<?php

  /**
   * Login as form callback
   * 
   * @package angie.frameworks.authentication
   * @subpackage models
   */
  class LoginAsFormCallback extends JavaScriptCallback {
    
    /**
     * Additional options
     *
     * @var Object
     */
    protected $options;
    
  	/**
  	 * Flyout page action
  	 *
  	 * @param array $options
  	 */
  	function __construct($options) {
  	  $this->options = $options;
  	} // __construct
    
    /**
     * Render callback body
     *
     * @return string
     */
    function render() {
      return '(function () { $(this).loginAsForm(' . JSON::encode($this->options) . '); })';
    } // render
    
  }