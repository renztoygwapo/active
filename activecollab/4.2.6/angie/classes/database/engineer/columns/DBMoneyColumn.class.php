<?php

  /**
   * Decimal column tailered for storing money amounts
   * 
   * @package angie.library.database
   * @subpackage engineer
   */
  class DBMoneyColumn extends DBDecimalColumn {
  
    /**
     * Construct decimal column
     *
     * @param string $name
     * @param integer $lenght
     * @param integer $scale
     * @param mixed $default
     */
    function __construct($name, $default = null) {
    	parent::__construct($name, 13, 3, $default);
    } // __construct
    
    /**
     * Create and return money column column
     *
     * @param string $name
     * @param mixed $default
     * @return DBMoneyColumn
     */
    static public function create($name, $default = null) {
      return new DBMoneyColumn($name, $default);
    } // create
    
  }