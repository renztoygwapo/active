<?php

  /**
   * Composite user column definition
   *
   * @package angie.library.database
   * @subpackage engineer
   */
  class DBUserColumn extends DBCompositeColumn {
    
    /**
     * Name of the column
     *
     * @var string
     */
    protected $name;
    
    /**
     * Flag if we need to add key on user ID field or not
     *
     * @var boolean
     */
    private $add_key;
    
    /**
     * Construct user column instance
     *
     * @param string $name
     * @param boolean $add_key
     */
    function __construct($name, $add_key = true) {
      $this->add_key = $add_key;
      $this->name = $name;
      
      $this->columns = array(
        DBIntegerColumn::create($name . '_id', 10)->setUnsigned(true), 
        DBStringColumn::create($name . '_name', 100), 
        DBStringColumn::create($name . '_email', 150), 
      );
    } // __construct
    
    /**
     * Construct and return user column
     *
     * @param string $name
     * @param boolean $add_key
     * @return DBUserColumn
     */
    static function create($name, $add_key = true) {
      return new DBUserColumn($name, $add_key);
    } // create
    
    /**
     * Event that table triggers when this column is added to the table
     */
    function addedToTable() {
      if($this->add_key) {
        $this->table->addIndex(new DBIndex($this->name . '_id', DBIndex::KEY, $this->name . '_id'));
      } // if
    } // addedToTable
    
  }