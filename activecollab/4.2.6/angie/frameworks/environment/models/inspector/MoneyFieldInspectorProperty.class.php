<?php

  /**
   * Money field property defintiion class
   *
   * @package angie.frameworks.environment
   * @subpackage models
   */
  class MoneyFieldInspectorProperty extends InspectorProperty {
    
    /**
     * Content field variable
     * 
     * @var string
     */
    var $content_field;
    
    /**
     * Currency field variable
     * 
     * @var unknown_type
     */
    var $currency_field;
    
    /**
     * Additional properties
     * 
     * @var array
     */
    var $additional;
    
    /**
     * Constructor
     * 
     * @param FwApplicationObject $object
     * @param string $content_field
     * @param array $additional
     */
    function __construct($object, $content_field, $currency_field, $additional = null) {
      $this->content_field = $content_field;
      $this->additional = $additional;
      $this->currency_field = $currency_field;
    } // __construct
    
    /**
     * Function which will render the property
     * 
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) { App.Inspector.Properties.MoneyField.apply(field, [object, client_interface, ' . JSON::encode($this->content_field) . ', ' . JSON::encode($this->currency_field) . ', ' . JSON::encode($this->additional) . ']) })';
    } // render

  } // MoneyFieldInspectorProperty