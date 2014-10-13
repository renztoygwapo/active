<?php

  /**
   * History framework model definition
   *
   * @package angie.frameworks.history
   * @subpackage resources
   */
  class HistoryFrameworkModel extends AngieFrameworkModel {
    
    /**
     * Construct history framework model definition
     *
     * @param HistoryFramework $parent
     */
    function __construct(HistoryFramework $parent) {
      parent::__construct($parent);
      
      $this->addModel(DB::createTable('modification_logs')->addColumns(array(
        DBIdColumn::create(DBColumn::BIG), 
        DBParentColumn::create(), 
        DBActionOnByColumn::create('created', true), 
        DBBoolColumn::create('is_first', false), 
      )))->setOrderBy('created_on');
      
      $this->addTable(DB::createTable('modification_log_values')->addColumns(array(
        DBIntegerColumn::create('modification_id', 20, '0')->setUnsigned(true), 
        DBStringColumn::create('field', 50, ''), 
        DBTextColumn::create('value')->setSize(DBColumn::BIG), 
      ))->addIndices(array(
        DBIndexPrimary::create(array('modification_id', 'field')), 
      )));
    } // __construct
    
  }