<?php

  /**
   * Action on by composite column definition
   *
   * @package angie.library.database
   * @subpackage engineer
   */
  class DBActionOnByColumn extends DBCompositeColumn {
    
    /**
     * Action name
     *
     * @var string
     */
    protected $action;
    
    /**
     * Set key on date column
     *
     * @var boolean
     */
    protected $key_on_date;
    
    /**
     * Set key on user ID column
     *
     * @var boolean
     */
    protected $key_on_by;
    
    /**
     * Construct action on by composite column
     *
     * @param string $action
     * @param boolean $key_on_date
     * @param boolean $key_on_by
     */
    function __construct($action, $key_on_date = false, $key_on_by = false) {
      $this->action = $action;
      $this->key_on_date = $key_on_date;
      $this->key_on_by = $key_on_by;
      
      $this->columns = array(
        DBDateTimeColumn::create($this->action . '_on'), 
        DBIntegerColumn::create($this->action . '_by_id', DBColumn::NORMAL)->setUnsigned(true), 
        DBStringColumn::create($this->action . '_by_name', 100), 
        DBStringColumn::create($this->action . '_by_email', 150), 
      );
    } // __construct
    
    /**
     * Create and return instance of action on by composite column
     *
     * @param string $action
     * @param boolean $key_on_date
     * @param boolean $key_on_by
     * @return DBActionOnByColumn
     */
    static public function create($action, $key_on_date = false, $key_on_by = false) {
      return new DBActionOnByColumn($action, $key_on_date, $key_on_by);
    } // create
    
    /**
     * Event that table triggers when this column is added to the table
     */
    function addedToTable() {
      if($this->key_on_date) {
        $this->table->addIndex(new DBIndex($this->action . '_on', DBIndex::KEY, $this->action . '_on'));
      } // if
      
      if($this->key_on_by) {
        $this->table->addIndex(new DBIndex($this->action . '_by_id', DBIndex::KEY, $this->action . '_by_id'));
      } // if
    } // addedToTable
    
  }