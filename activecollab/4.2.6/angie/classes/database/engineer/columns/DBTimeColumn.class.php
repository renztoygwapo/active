<?php

  /**
   * Class that represents TIME database columns
   *
   * @package angie.library.database
   * @subpackage engineer
   */
  class DBTimeColumn extends DBColumn {
    
    /**
     * Create and return tme column
     *
     * @param string $name
     * @param mixed $default
     * @return DBTimeColumn
     */
    static public function create($name, $default = null) {
      return new DBTimeColumn($name, $default);
    } // create
    
    /**
     * Return type definition
     *
     * @return string
     */
    function prepareTypeDefinition() {
      return 'time';
    } // prepareTypeDefinition

    // ---------------------------------------------------
    //  Model generator
    // ---------------------------------------------------

    /**
     * Return verbose PHP type
     *
     * @return string
     */
    function getPhpType() {
      return 'DateTimeValue';
    } // getPhpType

    /**
     * Return PHP bit that will cast raw value to proper value
     *
     * @param string $var
     * @return string
     */
    function getCastingCode() {
      return 'timeval($value)';
    } // getCastingCode
    
  }