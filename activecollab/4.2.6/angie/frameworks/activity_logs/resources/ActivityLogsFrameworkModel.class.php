<?php

  /**
   * Activity logs framework model definition
   *
   * @package angie.frameworks.activity_logs
   * @subpackage resources
   */
  class ActivityLogsFrameworkModel extends AngieFrameworkModel {
    
    /**
     * Construct activity logs framework model
     *
     * @param ActivityLogsFramework $parent
     */
    function __construct(ActivityLogsFramework $parent) {
      parent::__construct($parent);
      
      $this->addModel(DB::createTable('activity_logs')->addColumns(array(
        DBIdColumn::create(), 
        DBStringColumn::create('subject_type', 50, ''), 
        DBIntegerColumn::create('subject_id')->setDefault(0)->setSize(DBColumn::NORMAL)->setUnsigned(true), 
        DBStringColumn::create('subject_context', 255, ''), 
        DBStringColumn::create('action', 100, ''),
        DBStringColumn::create('target_type', 50), 
        DBIntegerColumn::create('target_id')->setSize(DBColumn::NORMAL)->setUnsigned(true),  
        DBActionOnByColumn::create('created', true),
        DBTextColumn::create('comment'),  
      ))->addIndices(array(
        DBIndex::create('subject', DBIndex::KEY, array('subject_type', 'subject_id')), 
        DBIndex::create('subject_context'), 
        DBIndex::create('action'), 
        DBIndex::create('target', DBIndex::KEY, array('target_type', 'target_id')), 
      )));
    } // __construct
    
  }