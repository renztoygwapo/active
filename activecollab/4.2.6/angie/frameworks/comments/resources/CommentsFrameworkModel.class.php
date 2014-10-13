<?php

  /**
   * Comments framework model definition
   *
   * @package angie.frameworks.comments
   * @subpackage resources
   */
  class CommentsFrameworkModel extends AngieFrameworkModel {
    
    /**
     * Construct comments framework model definition
     *
     * @param CommentsFramework $parent
     */
    function __construct(CommentsFramework $parent) {
      parent::__construct($parent);
      
      $this->addModel(DB::createTable('comments')->addColumns(array(
        DBIdColumn::create(), 
        DBTypeColumn::create('Comment'), 
        DBStringColumn::create('source', 50), 
        DBParentColumn::create(), 
        DBTextColumn::create('body')->setSize(DBColumn::BIG),
        DBIpAddressColumn::create('ip_address'),  
        DBStateColumn::create(), 
        DBActionOnByColumn::create('created', true, true), 
        DBActionOnByColumn::create('updated'), 
      )))->setTypeFromField('type');
    } // __construct
    
  }