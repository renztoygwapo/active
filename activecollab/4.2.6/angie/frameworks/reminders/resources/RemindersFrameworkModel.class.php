<?php

  /**
   * Reminders framework model
   * 
   * @package angie.frameworks.reminders
   * @subpackage resources
   */
  class RemindersFrameworkModel extends AngieFrameworkModel {
  
    /**
     * Construct reminders framework model definition
     *
     * @param RemindersFramework $parent
     */
    function __construct(RemindersFramework $parent) {
      parent::__construct($parent);
      
      $this->addModel(DB::createTable('reminders')->addColumns(array(
        DBIdColumn::create(), 
        DBParentColumn::create(),  
        DBStringColumn::create('send_to', 15, 'self'), 
        DBDateTimeColumn::create('send_on'), 
        DBDateTimeColumn::create('sent_on'), 
        DBTextColumn::create('comment'),
        DBIntegerColumn::create('selected_user_id', 10)->setUnsigned(true),
        DBUserColumn::create('created_by'),  
        DBDateTimeColumn::create('created_on'), 
        DBDateTimeColumn::create('dismissed_on'), 
      ))->addIndices(array(
        DBIndex::create('created_on'), 
      )));
      
      $this->addTable(DB::createTable('reminder_users')->addColumns(array(
        DBIntegerColumn::create('reminder_id', 10)->setUnsigned(true), 
        DBUserColumn::create('user', true),  
        DBDateTimeColumn::create('dismissed_on'), 
      ))->addIndices(array(
        DBIndexPrimary::create(array('reminder_id', 'user_email')), 
      )));
    } // __construct
    
  }