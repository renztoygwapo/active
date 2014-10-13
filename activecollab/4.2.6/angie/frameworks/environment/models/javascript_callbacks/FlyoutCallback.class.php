<?php

  /**
   * Flyout JavaScript callback implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage models
   */
  class FlyoutCallback extends JavaScriptCallback {
  
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
  	  
  	  if(empty($this->options) || !is_array($this->options)) {
  	  	$this->options = array();
  	  } // if
  	} // __construct
    
    /**
     * Render callback body
     *
     * @return string
     */
    function render() {
      return '(function () { $(this).flyout(' . JSON::encode($this->options) . '); })';
    } // render
    
  }