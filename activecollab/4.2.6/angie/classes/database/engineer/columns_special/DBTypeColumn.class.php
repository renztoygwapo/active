<?php

  /**
   * Type column definition
   * 
   * This is string column designed to store type name value in polymorh tables
   *
   * @package angie.library.database
   * @subpackage engineer
   */
  class DBTypeColumn extends DBStringColumn {
    
    /**
     * Contruct type column instance
     * 
     * @param string $default_type
     * @param integer $length
     */
    function __construct($default_type = 'ApplicationObject', $length = 50) {
      parent::__construct('type', $length, $default_type);
    } // __construct
    
    /**
     * Create and return new type column instance
     *
     * @param string $default_type
     * @param integer $length
     * @return DBTypeColumn
     */
    static public function create($default_type = 'ApplicationObject', $length = 50) {
      return new DBTypeColumn($default_type, $length);
    } // create
    
    /**
     * Event that table triggers when this column is added to the table
     */
    function addedToTable() {
      $this->table->addIndex(new DBIndex('type', DBIndex::KEY, 'type'));
    } // addedToTable
    
  }