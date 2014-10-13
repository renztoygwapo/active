<?php

  /**
   * Flyout form callback
   * 
   * @package angie.frameworks.environment
   * @subpackage models
   */
  class FlyoutFormCallback extends JavaScriptCallback {
    
    /**
     * Additional options
     *
     * @var Object
     */
    protected $options;
    
  	/**
  	 * Flyout page action
  	 *
  	 * @param mixed $p1
  	 * @param mixed $p2
  	 */
  	function __construct($p1 = null, $p2 = null) {
  	  if(is_string($p1)) {
  	    $this->options = array('success_event' => $p1);
  	    
  	    if(is_array($p2)) {
  	      $this->options = array_merge($this->options, $p2);
  	    } // if
  	  } elseif(is_array($p1)) {
  	    $this->options = $p1;
  	  } else {
  	    $this->options = array();
  	  } // if
  	} // __construct
    
    /**
     * Render callback body
     *
     * @return string
     */
    function render() {
      return '(function () { $(this).flyoutForm(' . JSON::encode($this->options) . '); })';
    } // render
    
  }