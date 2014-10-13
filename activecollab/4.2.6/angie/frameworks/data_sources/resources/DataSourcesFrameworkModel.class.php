<?php

  /**
   * Data Sources module model definition
   *
   * @package angie.frameworks.data_sources
   * @subpackage models
   */
  class DataSourcesFrameworkModel extends AngieFrameworkModel {
    
    /**
     * Construct data sources module model definition
     *
     * @param DataSourcesFramework $parent
     */
    function __construct(DataSourcesFramework $parent) {
      parent::__construct($parent);
      
      $this->addModel(DB::createTable('data_sources')->addColumns(array(
        DBIdColumn::create(),
        DBTypeColumn::create(),
        DBNameColumn::create(50),
        DBActionOnByColumn::create('created', false, true),
        DBBoolColumn::create('is_private', false),
        DBAdditionalPropertiesColumn::create(),
      )))->setTypeFromField('type')->setObjectIsAbstract(true);

      $this->addModel(DB::createTable('data_source_mappings')->addColumns(array(
        DBIdColumn::create(),
        DBIntegerColumn::create('project_id', 11),
        DBStringColumn::create('source_type', 50, ''),
        DBIntegerColumn::create('source_id', 11),
        DBIntegerColumn::create('parent_id', 11),
        DBStringColumn::create('parent_type', 50, ''),
        DBIntegerColumn::create('external_id', 11),
        DBStringColumn::create('external_type', 50, ''),
        DBActionOnByColumn::create('created')
      )));
    } // __construct
    
  }