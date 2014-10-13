<?php

  /**
   * Async link callback
   * 
   * @package angie.framework.environment
   * @subpackage models
   */
  class AsyncLinkCallback extends JavaScriptCallback {
    
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
  	function __construct($options = null) {
  	  $this->options = $options;
  	} // __construct
    
    /**
     * Render callback body
     *
     * @return string
     */
    function render() {
      return '(function () { $(this).asyncLink(' . JSON::encode($this->options) . '); })';
    } // render
    
  }