<?php

  /**
   * Name column
   *
   * @package angie.library.database
   * @subpackage engineer
   */
  class DBNameColumn extends DBStringColumn {
    
    /**
     * Flag whether this name field should be kept unique
     *
     * @var boolean
     */
    private $unique = false;
    
    /**
     * Additional fields that are used to validate uniqueness of the name
     *
     * @var array
     */
    private $unique_context = null;
    
    /**
     * Construct name column instance
     *
     * @param integer $length
     * @param boolean $unique
     * @param array $unique_context
     */
    function __construct($length = 255, $unique = false, $unique_context = null) {
      parent::__construct('name', $length);
      
      $this->unique = (boolean) $unique;
      
      if($unique_context) {
        $this->unique_context = (array) $unique_context;
      } // if
    } // __construct
    
    /**
     * Create and return instance of name column
     *
     * @param integer $length
     * @param boolean $unique
     * @param string $unique_context
     * @return DBNameColumn
     */
    static public function create($length = 255, $unique = false, $unique_context = null) {
      return new DBNameColumn($length, $unique, $unique_context);
    } // create
    
    /**
     * Event that table triggers when this column is added to the table
     */
    function addedToTable() {
      if($this->unique) {
        $context = array('name');
        
        if(is_array($this->unique_context)) {
          $context = array_merge($context, $this->unique_context);
        } // if
        
        $this->table->addIndex(new DBIndex('name', DBIndex::UNIQUE, $context));
      } // if
    } // addedToTable
    
  }