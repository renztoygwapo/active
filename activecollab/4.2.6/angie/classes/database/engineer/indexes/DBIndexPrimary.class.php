<?php

  /**
   * Primary index definition
   *
   * @package angie.library.database
   * @subpackage engineer
   */
  class DBIndexPrimary extends DBIndex {
    
    /**
     * Construct primary key
     *
     * @param array $columns
     */
    function __construct($columns) {
    	parent::__construct('PRIMARY', DBIndex::PRIMARY, $columns);
    } // __construct
    
    /**
     * Construct and return primary key instance
     *
     * @param array $columns
     * @return DBIndexPrimary
     */
    static public function create($columns) {
      return new DBIndexPrimary($columns);
    } // create
    
  }

?>