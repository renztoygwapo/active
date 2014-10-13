<?php

  /**
   * IPv6 friendly IP address column
   * 
   * @package angie.library.database
   * @subpackage engineer
   */
  class DBIpAddressColumn extends DBStringColumn {
    
    /**
     * Construct string column
     *
     * @param string $name
     * @param mixed $default
     */
    function __construct($name, $default = null) {
      parent::__construct($name, $default);
      
      $this->length = 45;
    } // __construct
    
    /**
     * Create new integer column instance
     *
     * @param string $name
     * @param mixed $default
     * @return DBIpAddressColumn
     */
    static public function create($name, $default = null) {
      return new DBIpAddressColumn($name, $default);
    } // create
    
  }