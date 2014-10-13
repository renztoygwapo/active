<?php

  /**
   * Labels framework model definition
   *
   * @package angie.frameworks.labels
   * @subpackage resources
   */
  class LabelsFrameworkModel extends AngieFrameworkModel {
    
    /**
     * Construct labels framework model definition
     *
     * @param LabelsFramework $parent
     */
    function __construct(LabelsFramework $parent) {
      parent::__construct($parent);
      
      $this->addModel(DB::createTable('labels')->addColumns(array(
        DBIdColumn::create(), 
        DBTypeColumn::create('Label'), 
        DBNameColumn::create(10, true, 'type'), 
        DBBoolColumn::create('is_default', false), 
        DBAdditionalPropertiesColumn::create(), 
      )))->setTypeFromField('type')->setObjectIsAbstract(true);
    } // __construct
    
  }