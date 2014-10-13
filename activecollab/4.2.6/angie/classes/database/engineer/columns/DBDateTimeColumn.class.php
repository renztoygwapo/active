<?php

  /**
   * Class that represents DATETIME database columns
   *
   * @package angie.library.database
   * @subpackage engineer
   */
  class DBDateTimeColumn extends DBColumn {
    
    /**
     * Create new column instance
     *
     * @param string $name
     * @param mixed $default
     * @return DBDateTimeColumn
     */
    static public function create($name, $default = null) {
      return new DBDateTimeColumn($name, $default);
    } // create
    
    /**
     * Prepare type definition
     *
     * @return string
     */
    function prepareTypeDefinition() {
      return 'datetime';
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
        return is_int($this->default) ? "'" . date(DATETIME_MYSQL, $this->default) . "'" : "'" . date(DATETIME_MYSQL, strtotime($this->default)) . "'";
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
      return 'DateTimeValue';
    } // getPhpType

    /**
     * Return PHP bit that will cast raw value to proper value
     *
     * @param string $var
     * @return string
     */
    function getCastingCode() {
      return 'datetimeval($value)';
    } // getCastingCode
    
  }