<?php

  /**
   * Composite columns are columns that are made of multiple fields
   *
   * @package angie.library.database
   * @subpackage engineer
   */
  abstract class DBCompositeColumn {
    
    /**
     * Parent table
     *
     * @var DBTable
     */
    protected $table;
    
    /**
     * Array of columns that make this composite column
     *
     * @var array
     */
    protected $columns = array();
    
    /**
     * Return array of columns that need to be added to the table
     *
     * @return array
     */
    function getColumns() {
      return $this->columns;
    } // getColumn
    
    /**
     * Event that table triggers when this column is added to the table
     */
    function addedToTable() {
      
    } // addedToTable
    
    /**
     * Return parent table instance
     *
     * @return DBTable
     */
    function &getTable() {
      return $this->table;
    } // getTable
    
    /**
     * Set parent table
     *
     * @param DBTable $table
     * @return DBCompositeColumn
     */
    function &setTable(DBTable &$table) {
      $this->table = $table;
      
      return $this;
    } // setTable
    
  }