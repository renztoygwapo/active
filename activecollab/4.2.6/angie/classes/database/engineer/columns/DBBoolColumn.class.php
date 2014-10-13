<?php

  /**
   * Class that represents BOOL database columns
   *
   * @package angie.library.database
   * @subpackage engineer
   */
  class DBBoolColumn extends DBColumn {
    
    /**
     * Create new column instance
     *
     * @param string $name
     * @param boolean $default
     * @return DBBoolColumn
     */
    static public function create($name, $default = false) {
      return new DBBoolColumn($name, $default);
    } // create
    
    /**
     * Return type definition
     *
     * @return string
     */
    function prepareTypeDefinition() {
      return 'tinyint(1) unsigned';
    } // prepareTypeDefinition
    
    /**
     * Prepare NULL part of type definition
     * 
     * @return string
     */
    function prepareNull() {
      return 'NOT NULL';
    } // prepareNull
    
    /**
     * Prepare default value definition
     * 
     * @return string
     */
    function prepareDefault() {
      return $this->default ? "'1'" : "'0'";
    } // prepareDefault
    
    /**
     * Return model definition code for this column
     *
     * @return string
     */
    function prepareModelDefinition() {
      if($this->getDefault() === null) {
        $default = '';
      } else {
        $default = $this->getDefault() ? ', true' : ', false';
      } // if
      
      return "DBBoolColumn::create('" . $this->getName() ."'$default)";
    } // prepareModelDefinition

    // ---------------------------------------------------
    //  Model generator
    // ---------------------------------------------------

    /**
     * Return verbose PHP type
     *
     * @return string
     */
    function getPhpType() {
      return 'boolean';
    } // getPhpType

    /**
     * Return PHP bit that will cast raw value to proper value
     *
     * @param string $var
     * @return string
     */
    function getCastingCode() {
      return '(boolean) $value';
    } // getCastingCode
    
  }