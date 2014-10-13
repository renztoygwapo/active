<?php

  /**
   * Parent composite column
   *
   * @package angie.library.database
   * @subpackage engineer
   */
  class DBParentColumn extends DBRelatedObjectColumn {
    
    /**
     * Construct parent column instance
     *
     * @param boolean $add_key
     */
    function __construct($add_key = true) {
      parent::__construct('parent', $add_key);
    } // __construct
    
    /**
     * Construct and return parent column
     *
     * @param boolean $add_key
     * @return DBParentColumn
     */
    static function create($add_key = true) {
      return new DBParentColumn($add_key);
    } // create
    
  }