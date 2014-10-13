<?php

  /**
   * Subtasks framework model definition
   *
   * @package angie.frameworks.subtasks
   * @subpackage resources
   */
  class SubtasksFrameworkModel extends AngieFrameworkModel {
    
    /**
     * Construct subtasks framework model definition
     *
     * @param SubtasksFramework $parent
     */
    function __construct(SubtasksFramework $parent) {
      parent::__construct($parent);
      
      $this->addModel(DB::createTable('subtasks')->addColumns(array(
        DBIdColumn::create(), 
        DBTypeColumn::create('Subtask'), 
        DBParentColumn::create(), 
        DBIntegerColumn::create('label_id', 5)->setUnsigned(true), 
        DBIntegerColumn::create('assignee_id', 10)->setUnsigned(true), 
        DBIntegerColumn::create('delegated_by_id', 10)->setUnsigned(true), 
        DBIntegerColumn::create('priority', 4), 
        DBTextColumn::create('body')->setSize(DBColumn::BIG), 
        DBDateColumn::create('due_on'), 
        DBStateColumn::create(),  
        DBActionOnByColumn::create('created', true),
        DBActionOnByColumn::create('updated'),
        DBActionOnByColumn::create('completed', true), 
        DBIntegerColumn::create('position', 10, '0')->setUnsigned(true), 
      ))->addIndices(array(
        DBIndex::create('created_on'), 
        DBIndex::create('position'), 
        DBIndex::create('completed_on'), 
        DBIndex::create('due_on'), 
        DBIndex::create('assignee_id'), 
        DBIndex::create('delegated_by_id'), 
      )))->setObjectIsAbstract(true)->setTypeFromField('type');
    } // __construct
    
  }