<?php

  // Include application specific model base
  require_once APPLICATION_PATH . '/resources/ActiveCollabModuleModel.class.php';

  /**
   * Tracking module model definition
   *
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  class TrackingModuleModel extends ActiveCollabModuleModel {
    
    /**
     * Construct tracking module model definition
     *
     * @param TrackingModule $parent
     */
    function __construct(TrackingModule $parent) {
      parent::__construct($parent);
      
      $this->addModel(DB::createTable('time_records')->addColumns(array(
        DBIdColumn::create(), 
        DBParentColumn::create(), 
        DBIntegerColumn::create('job_type_id', 5, 0)->setUnsigned(true), 
        DBStateColumn::create(), 
        DBDateColumn::create('record_date'), 
        DBDecimalColumn::create('value', 12, 2), 
        DBUserColumn::create('user'),
        DBTextColumn::create('summary'), 
        DBIntegerColumn::create('billable_status', 3, 0)->setUnsigned(true), 
        DBActionOnByColumn::create('created'),
        DBActionOnByColumn::create('updated'),
      ))->addIndices(array(
        DBIndex::create('job_type_id'), 
        DBIndex::create('record_date'), 
      )))->setBaseObjectExtends('TrackingObject');
      
      $this->addModel(DB::createTable('job_types')->addColumns(array(
        DBIdColumn::create(), 
        DBNameColumn::create(100), 
        DBMoneyColumn::create('default_hourly_rate', 0), 
        DBBoolColumn::create('is_default', false),
        DBBoolColumn::create('is_active', false),
      ))->addIndices(array(
        DBIndex::create('name', DBIndex::UNIQUE, 'name'), 
      )))->setOrderBy('name');
      
      $this->addTable(DB::createTable('project_hourly_rates')->addColumns(array(
        DBIntegerColumn::create('project_id', 10)->setUnsigned(true), 
        DBIntegerColumn::create('job_type_id', 5)->setUnsigned(true), 
        DBMoneyColumn::create('hourly_rate', 0)->setUnsigned(true), 
      ))->addIndices(array(
        DBIndexPrimary::create(array('project_id', 'job_type_id')), 
      )));
      
      $this->addModel(DB::createTable('expenses')->addColumns(array(
        DBIdColumn::create(), 
        DBParentColumn::create(),
        DBIntegerColumn::create('category_id', 5, 0)->setUnsigned(true), 
        DBStateColumn::create(), 
        DBDateColumn::create('record_date'), 
        DBMoneyColumn::create('value', 0)->setUnsigned(true), 
        DBUserColumn::create('user'), 
        DBTextColumn::create('summary'), 
        DBIntegerColumn::create('billable_status', 3, '0')->setUnsigned(true), 
        DBActionOnByColumn::create('created'),
        DBActionOnByColumn::create('updated'),
      ))->addIndices(array(
        DBIndex::create('category_id'), 
        DBIndex::create('record_date'), 
      )))->setBaseObjectExtends('TrackingObject');
      
      $this->addModel(DB::createTable('expense_categories')->addColumns(array(
        DBIdColumn::create(), 
        DBNameColumn::create(100), 
        DBBoolColumn::create('is_default', false), 
      ))->addIndices(array(
        DBIndex::create('name', DBIndex::UNIQUE, 'name'), 
      )))->setOrderBy('name');
      
      $this->addModel(DB::createTable('estimates')->addColumns(array(
        DBIdColumn::create(), 
        DBParentColumn::create(), 
        DBIntegerColumn::create('job_type_id', 5, 0)->setUnsigned(true), 
        DBDecimalCOlumn::create('value', 12, 2)->setUnsigned(true),
        DBTextColumn::create('comment'),  
        DBActionOnByColumn::create('created', true), 
      )))->setOrderBy('created_on DESC');
    } // __construct
    
    /**
     * Load initial framework data
     *
     * @param string $environment
     */
    function loadInitialData($environment = null) {
      $this->addConfigOption('default_billable_status', 1);

      $this->loadTableData('job_types', array(
        array(
          'name' => 'General',
          'default_hourly_rate' => 100,
          'is_default' => true,
          'is_active' => true,
        )
      ));
      
      $this->loadTableData('expense_categories', array(
        array(
          'name' => 'General',  
          'is_default' => true, 
        )
      ));

      $project_tabs = $this->getConfigOptionValue('project_tabs');

      if(!in_array('time', $project_tabs)) {
        $project_tabs[] = 'time';
        $this->setConfigOptionValue('project_tabs', $project_tabs);
      } // if
      
      parent::loadInitialData($environment);
    } // loadInitialData
    
  }