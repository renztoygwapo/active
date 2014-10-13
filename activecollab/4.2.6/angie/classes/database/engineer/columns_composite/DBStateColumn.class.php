<?php

  /**
   * State column definition
   *
   * @package angie.library.database
   * @subpackage engineer
   */
  class DBStateColumn extends DBCompositeColumn {
    
    /**
     * Construct state column instance
     */
    function __construct() {
      $this->columns = array(
        DBIntegerColumn::create('state', 3, 0)->setSize(DBColumn::TINY)->setUnsigned(true), 
        DBIntegerColumn::create('original_state', 3)->setSize(DBColumn::TINY)->setUnsigned(true), 
      );
    } // __construct
    
    /**
     * Construct and return state column
     *
     * @return DBStateColumn
     */
    static function create() {
      return new DBStateColumn();
    } // create
    
  }