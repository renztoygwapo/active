<?php

  /**
   * Visibility column implementation
   *
   * @package angie.library.database
   * @subpackage engineer
   */
  class DBVisibilityColumn extends DBCompositeColumn {
    
    /**
     * Construct visibility column instance
     */
    function __construct() {
      $this->columns = array(
        DBIntegerColumn::create('visibility', 3, 0)->setSize(DBColumn::TINY)->setUnsigned(true), 
        DBIntegerColumn::create('original_visibility', 3)->setSize(DBColumn::TINY)->setUnsigned(true), 
      );
    } // __construct
    
    /**
     * Construct and return visibility column
     *
     * @return DBVisibilityColumn
     */
    static function create() {
      return new DBVisibilityColumn();
    } // create
    
  }