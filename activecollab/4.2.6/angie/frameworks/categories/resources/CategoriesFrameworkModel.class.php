<?php

  /**
   * Categories framework model definition
   *
   * @package angie.frameworks.categories
   * @subpackage resources
   */
  class CategoriesFrameworkModel extends AngieFrameworkModel {
    
    /**
     * Construct categories framework model definition
     *
     * @param CategoriesFramework $parent
     */
    function __construct(CategoriesFramework $parent) {
      parent::__construct($parent);
      
      $this->addModel(DB::createTable('categories')->addColumns(array(
        DBIdColumn::create(), 
        DBTypeColumn::create('Category'), 
        DBParentColumn::create(), 
        DBNameColumn::create(100), 
        DBActionOnByColumn::create('created'), 
      ))->addIndices(array(
        DBIndex::create('name', DBIndex::UNIQUE, array('parent_type', 'parent_id', 'type', 'name')),
      )))->setTypeFromField('type')->setObjectIsAbstract(true);
    } // __construct
    
  }