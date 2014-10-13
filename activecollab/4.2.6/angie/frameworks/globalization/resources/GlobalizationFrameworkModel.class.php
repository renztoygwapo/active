<?php

  /**
   * Globalization framework model defintion
   *
   * @package angie.frameworks.globalization
   * @subpackage resources
   */
  class GlobalizationFrameworkModel extends AngieFrameworkModel {
    
    /**
     * Construct globalization framework model defintion
     *
     * @param GlobalizationFramework $parent
     */
    function __construct(GlobalizationFramework $parent) {
      parent::__construct($parent);
      
      $this->addModel(DB::createTable('currencies')->addColumns(array(
        DBIdColumn::create(), 
        DBNameColumn::create(50, true), 
        DBStringColumn::create('code', 3),
        DBIntegerColumn::create('decimal_spaces', 1, 2)->setUnsigned(true),
        DBDecimalColumn::create('decimal_rounding', 4, 3, '0.000')->setUnsigned(true),
        DBBoolColumn::create('is_default', false), 
      ))->addIndices(array(
        DBIndex::create('code', DBIndex::UNIQUE), 
      )))->setOrderBy('name');
      
      $this->addModel(DB::createTable('day_offs')->addColumns(array(
        DBIdColumn::create(), 
        DBNameColumn::create(100), 
        DBDateColumn::create('event_date'), 
        DBBoolColumn::create('repeat_yearly', false), 
      ))->addIndices(array(
        DBIndex::create('day_off_name', DBIndex::UNIQUE, array('name', 'event_date')),
      )))->setOrderBy('event_date');
      
      $this->addModel(DB::createTable('languages')->addColumns(array(
        DBIdColumn::create(), 
        DBNameColumn::create(50, true), 
        DBStringColumn::create('locale', 30, ''),
        DBStringColumn::create('decimal_separator', 1, '.'),
        DBStringColumn::create('thousands_separator', 1, ','),
        DBDateTimeColumn::create('last_updated_on')
      ))->addIndices(array(
        DBIndex::create('locale', DBIndex::UNIQUE), 
      )))->setOrderBy('name');
      
      $this->addTable(DB::createTable('language_phrases')->addColumns(array(
        DBStringColumn::create('hash', 32), 
        DBTextColumn::create('phrase'), 
        DBStringColumn::create('module', 50, ''), 
        DBIntegerColumn::create('is_serverside', 2)->setUnsigned(true), 
        DBIntegerColumn::create('is_clientside', 2)->setUnsigned(true), 
      ))->addIndices(array(
        DBIndex::create('module_phrase', DBIndex::UNIQUE, array('hash', 'module')), 
      )));
      
      $this->addTable(DB::createTable('language_phrase_translations')->addColumns(array(
        DBIntegerColumn::create('language_id', 11, '0')->setUnsigned(true), 
        DBStringColumn::create('phrase_hash', 32), 
        DBTextColumn::create('translation'), 
      ))->addIndices(array(
        DBIndex::create('language_phrase', DBIndex::UNIQUE, array('phrase_hash', 'language_id')), 
        DBIndex::create('language_id'), 
      )));
    } // __construct
    
    /**
     * Load initial module data
     * 
     * @param string $environment
     */
    function loadInitialData($environment = null) {
    	$this->addConfigOption('language', 1);
    	
    	$this->addConfigOption('time_dst', false);
    	$this->addConfigOption('time_first_week_day', 0);
    	$this->addConfigOption('time_timezone', 0);
    	$this->addConfigOption('time_workdays', array(1, 2, 3, 4, 5));
    	
    	$this->addConfigOption('format_date', '%b %e. %Y');
    	$this->addConfigOption('format_time', '%I:%M %p');

      $this->addConfigOption('effective_work_hours', 40);

    	// Currencies
      $this->loadTableData('currencies', array(
        array('name' => 'Euro', 'code' => 'EUR'), 
        array('name' => 'US Dollar', 'code' => 'USD', 'is_default' => true), 
        array('name' => 'British Pound', 'code' => 'GBP'), 
        array('name' => 'Japanese Yen', 'code' => 'JPY'), 
      ));
    	
    	parent::loadInitialData($environment);
    } // loadInitialData
    
  }