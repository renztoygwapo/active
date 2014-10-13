<?php

  /**
   * Simple field property defintiion class
   *
   * @package angie.frameworks.environment
   * @subpackage models
   */
  class SimpleFieldInspectorProperty extends InspectorProperty {
    
    /**
     * content field variable
     * 
     * @var string
     */
    var $content_field;
    
    /**
     * Label field variable
     * 
     * @var string
     */
    var $label_field;
    
    /**
     * Prefix
     * 
     * @var String
     */
    var $prefix;
    
    /**
     * Sufix
     *  
     * @var string
     */
    var $sufix;
    
    /**
     * Modifier
     * 
     * @var string
     */
    var $modifier;

    /**
     * No clean
     *
     * @var mixed
     */
    var $no_clean;
    
    /**
     * Constructor
     * 
     * @param FwApplicationObject $object
     * @param string $content_field
     * @param array $additional
     */
    function __construct($object, $content_field, $additional = null) {
      $this->content_field = $content_field;
      
      if (is_foreachable($additional)) {
        $this->prefix = isset($additional['prefix']) ? $additional['prefix'] : null;
        $this->sufix = isset($additional['sufix']) ? $additional['sufix'] : null;
        $this->modifier = isset($additional['modifier']) ? $additional['modifier'] : null;
        $this->label_field = isset($additional['label_field']) ? $additional['label_field'] : null;
        $this->no_clean = isset($additional['no_clean']) ? (boolean) $additional['no_clean'] : false;
      } // if
    } // __construct
    
    /**
     * Function which will render the property
     * 
     * @return string
     */
    function render() {
      $additional = array(
        'prefix' => $this->prefix,
        'sufix' => $this->sufix,
        'modifier' => $this->modifier,
        'label_field' => $this->label_field,
        'no_clean' => $this->no_clean
      );
      
      return '(function (field, object, client_interface) { App.Inspector.Properties.SimpleField.apply(field, [object, client_interface, ' . JSON::encode($this->content_field) . ', ' . JSON::encode($additional) . ']) })';
    } // render

  } // SimpleFieldInspectorProperty