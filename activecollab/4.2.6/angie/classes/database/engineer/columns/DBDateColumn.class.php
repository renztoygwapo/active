<?php

  /**
   * Class that represents DATE database columns
   *
   * @package angie.library.database
   * @subpackage engineer
   */
  class DBDateColumn extends DBColumn {
    
    /**
     * Create new column instance
     *
     * @param string $name
     * @param mixed $default
     * @return DBDateColumn
     */
    static public function create($name, $default = null) {
      return new DBDateColumn($name, $default);
    } // create
    
    /**
     * Return type definition
     *
     * @return string
     */
    function prepareTypeDefinition() {
      return 'date';
    } // prepareTypeDefinition
    
    /**
     * Prepare default value
     *
     * @return string
     */
    function prepareDefault() {
      if($this->default === null) {
        return 'NULL';
      } else {
        return is_int($this->default) ? "'" . date(DATE_MYSQL, $this->default) . "'" : "'" . date(DATE_MYSQL, strtotime($this->default)) . "'";
      } // if
    } // prepareDefault

    // ---------------------------------------------------
    //  Model generator
    // ---------------------------------------------------

    /**
     * Return verbose PHP type
     *
     * @return string
     */
    function getPhpType() {
      return 'DateValue';
    } // getPhpType

    /**
     * Return PHP bit that will cast raw value to proper value
     *
     * @param string $var
     * @return string
     */
    function getCastingCode() {
      return 'dateval($value)';
    } // getCastingCode
    
  }