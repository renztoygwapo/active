<?php

  /**
   * Related object composite column
   *
   * @package angie.library.database
   * @subpackage engineer
   */
  class DBRelatedObjectColumn extends DBCompositeColumn {

    /**
     * Name of the relation
     *
     * @var string
     */
    private $relation_name;
    
    /**
     * Flag if we need to add key on related object fields or not
     *
     * @var boolean
     */
    private $add_key;
    
    /**
     * Construct related object column instance
     *
     * @param string $relation_name
     * @param boolean $add_key
     */
    function __construct($relation_name, $add_key = true) {
      $this->relation_name = $relation_name;
      $this->add_key = $add_key;
      
      $this->columns = array(
        DBStringColumn::create("{$relation_name}_type", 50),
        DBIntegerColumn::create("{$relation_name}_id", DBColumn::NORMAL)->setUnsigned(true),
      );
    } // __construct
    
    /**
     * Construct and return related object column
     *
     * @param string $relation_name
     * @param boolean $add_key
     * @return DBRelatedObjectColumn
     */
    static function create($relation_name, $add_key = true) {
      return new DBRelatedObjectColumn($relation_name, $add_key);
    } // create
    
    /**
     * Event that table triggers when this column is added to the table
     */
    function addedToTable() {
      if($this->add_key) {
        $this->table->addIndex(new DBIndex($this->relation_name, DBIndex::KEY, array("{$this->relation_name}_type", "{$this->relation_name}_id")));
      } // if
    } // addedToTable
    
  }