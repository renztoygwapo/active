<?php

  /**
   * Favorites framework model
   * 
   * @package angie.frameworks.favorites
   * @subpackage resources
   */
  class FavoritesFrameworkModel extends AngieFrameworkModel {
    
    /**
     * Construct user flags model definition
     *
     * @param FavoritesFramework $parent
     */
    function __construct(FavoritesFramework $parent) {
      parent::__construct($parent);
      
      $this->addTable(DB::createTable('favorites')->addColumns(array(
        DBParentColumn::create(false), 
        DBIntegerColumn::create('user_id', 10)->setUnsigned(true), 
      ))->addIndices(array(
        DBIndex::create('favorite_object', DBIndex::UNIQUE, array('parent_type', 'parent_id', 'user_id')), 
        DBIndex::create('user_id'), 
      )));
    } // __construct
    
  }