<?php

  /**
   * Visual Editor framework model definition
   *
   * @package angie.frameworks.visual_editor
   * @subpackage resources
   */
  class VisualEditorFrameworkModel extends AngieFrameworkModel {
    
    /**
     * Construct environment framework model definition
     *
     * @param VisualEditorFramework $parent
     */
    function __construct(VisualEditorFramework $parent) {
      parent::__construct($parent);
      
      $this->addModel(DB::createTable('code_snippets')->addColumns(array(
        DBIdColumn::create(), 
        DBParentColumn::create(true),  
        DBStringColumn::create('syntax', 50),  
        DBTextColumn::create('body'), 
        DBActionOnByColumn::create('created', true),  
      )));
    } // __construct
    
    /**
     * Load initial framework data
     *
     * @param string $environment
     */
    function loadInitialData($environment = null) {
      parent::loadInitialData($environment);

      // append list of whitelisted tags to this config option
      $config_option_name = 'whitelisted_tags'; 
      $whitelisted_tags = $this->getConfigOptionValue($config_option_name);
      $whitelisted_tags['visual_editor'] = array(
	      'p' => array('class', 'style'),
	      'img' => array('image-type', 'object-id', 'class'),
	      'strike' => array('class', 'style'),
	      'span' => array('class', 'data-redactor-inlinemethods', 'data-redactor', 'object-id'),
	      'a' => array('class', 'href'),
	      'blockquote' => null,
	      'br' => null,
	      'b' => null, 'strong' => null,
	      'i' => null, 'em' => null,
	      'u' => null
      );
			$this->setConfigOptionValue($config_option_name, $whitelisted_tags);      
    } // loadInitialData
  }