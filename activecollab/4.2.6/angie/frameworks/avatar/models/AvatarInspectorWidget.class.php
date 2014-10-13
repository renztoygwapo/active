<?php

  /**
   * Avatar widget defintion class
   *
   * @package modules.system.avatar
   * @subpackage models
   */
  class AvatarInspectorWidget extends InspectorWidget {
  	
  	/**
  	 * Avatar size
  	 * 
  	 * @var string
  	 */
  	protected $size = 'large';
  	
    /**
     * Constructor
     * 
     * @param FwApplicationObject $object
     * @param string $content_field
     * @param array $additional
     */
    function __construct($object, $size = 'large') {
    	$this->size = $size;
    } // __construct
    
    /**
     * Function that will render the widget
     * 
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) { App.Inspector.Widgets.Avatar.apply(field, [object, client_interface, ' . JSON::encode($this->size) . ']) })';
    } // render    
    
  } // AvatarInspectorWidget