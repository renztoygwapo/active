<?php

  /**
   * ID column
   * 
   * This column is a plan integer column with predefined name, and unsinged and 
   * auto_increment already set to true. Column length is easily configurable 
   * via constructor or create() method parameter
   *
   * @package angie.library.database
   * @subpackage engieer
   */
  class DBIdColumn extends DBIntegerColumn {
    
    /**
     * Create new ID column
     *
     * @param integer|string $length
     */
    function __construct($length = DBColumn::NORMAL) {
      parent::__construct('id', $length, 0);
      
      $this->setUnsigned(true);
      $this->setAutoIncrement(true);
    } // __construct
    
    /**
     * Create new ID column instance
     *
     * @param integer|string $length
     * @return DBIdColumn
     */
    static public function create($length = DBColumn::NORMAL) {
      return new DBIdColumn($length);
    } // create
    
  }