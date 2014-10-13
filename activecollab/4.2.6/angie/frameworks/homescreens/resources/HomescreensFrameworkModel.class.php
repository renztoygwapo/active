<?php

  /**
   * Homescreens framework model
   * 
   * @package angie.frameworks.homescreens
   * @subpackage resources
   */
  class HomescreensFrameworkModel extends AngieFrameworkModel {
  
    /**
     * Construct homescreens framework model definition
     *
     * @param HomescreensFramework $parent
     */
    function __construct(HomescreensFramework $parent) {
      parent::__construct($parent);
      
      $this->addModel(DB::createTable('homescreen_tabs')->addColumns(array(
        DBIdColumn::create(), 
        DBTypeColumn::create('HomescreenTab'), 
        DBIntegerColumn::create('user_id', 10, 0)->setUnsigned(true),
        DBNameColumn::create(50), 
        DBIntegerColumn::create('position', 5, 0)->setUnsigned(true), 
        DBAdditionalPropertiesColumn::create(), 
      ))->addIndices(array(
        DBIndex::create('user_id'),
        DBIndex::create('position'),
      )))->setObjectIsAbstract(true)->setTypeFromField('type')->setOrderBy('position');
      
      $this->addModel(DB::createTable('homescreen_widgets')->addColumns(array(
        DBIdColumn::create(), 
        DBTypeColumn::create('HomescreenWidget'), 
        DBIntegerColumn::create('homescreen_tab_id', 5, 0)->setUnsigned(true), 
        DBIntegerColumn::create('column_id', 3, 1)->setUnsigned(true), 
        DBIntegerColumn::create('position', 5, 0)->setUnsigned(true), 
        DBAdditionalPropertiesColumn::create(), 
      ))->addIndices(array(
        DBIndex::create('homescreen_tab_id'),
        DBIndex::create('position'),
      )))->setObjectIsAbstract(true)->setTypeFromField('type')->setOrderBy('position');
    } // __construct

    /**
     * Load initial framework data
     *
     * @param string $environment
     */
    function loadInitialData($environment = null) {
      parent::loadInitialData($environment);

      $this->addConfigOption('default_homescreen_tab_id');
    } // loadInitialData
    
  }