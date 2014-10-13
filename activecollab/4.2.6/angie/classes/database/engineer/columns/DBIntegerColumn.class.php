<?php

  /**
   * Class that represents INT database columns
   *
   * @package angie.library.database
   * @subpackage engineer
   */
  class DBIntegerColumn extends DBNumericColumn {
    
    /**
     * Is this field auto - increment
     *
     * @var boolean
     */
    private $auto_increment = false;
    
    /**
     * Construct numeric column
     *
     * @param string $name
     * @param integer $lenght
     * @param mixed $default
     */
    function __construct($name, $lenght = DBColumn::NORMAL, $default = null) {
      if($default !== null) {
        $default = (integer) $default;
      } // if
      
      parent::__construct($name, $lenght, $default);
    } // __construct
    
    /**
     * Create new integer column instance
     *
     * @param string $name
     * @param integer $lenght
     * @param mixed $default
     * @return DBIntegerColumn
     */
    static public function create($name, $lenght = 5, $default = null) {
      return new DBIntegerColumn($name, $lenght, $default);
    } // create
    
    /**
     * Load column details from row
     *
     * @param array $row
     */
    function loadFromRow($row) {
      parent::loadFromRow($row);
      $this->auto_increment = isset($row['Extra']) && $row['Extra'] == 'auto_increment';
    } // loadFromRow
    
    /**
     * Prepare definition
     *
     * @return string
     */
    function prepareDefinition() {
    	return $this->auto_increment ? parent::prepareDefinition() . ' auto_increment' : parent::prepareDefinition();
    } // prepareDefinition
    
    /**
     * Prepare type defintiion
     *
     * @return string
     */
    function prepareTypeDefinition() {
      $result =  $this->length ? "int($this->length)" : 'int';
      if($this->unsigned) {
        $result .= ' unsigned';
      } // if
      return $this->size == DBColumn::NORMAL ? $result : $this->size . $result;
    } // prepareTypeDefinition
    
    /**
     * Prepare null
     *
     * @return string
     */
    function prepareNull() {
      return $this->auto_increment || $this->default !== null ? 'NOT NULL' : 'NULL';
    } // prepareNull
    
    /**
     * Prepare default value
     *
     * @return string
     */
    function prepareDefault() {
      if($this->auto_increment) {
        return ''; // no default for auto increment columns
      } else {
        return parent::prepareDefault();
      } // if
    } // prepareDefault
    
    /**
     * Return model definition code for this column
     *
     * @return string
     */
    function prepareModelDefinition() {
      if($this->getName() == 'id') {
        $length = '';
        
        switch($this->getSize()) {
          case DBColumn::TINY:
            $length .= 'DBColumn::TINY';
            break;
          case DBColumn::SMALL:
            $length .= 'DBColumn::SMALL';
            break;
          case DBColumn::MEDIUM:
            $length .= 'DBColumn::MEDIUM';
            break;
          case DBColumn::BIG:
            $length .= 'DBColumn::BIG';
            break;
        } // if
        
        return "DBIdColumn::create($length)";
      } else {
        $default = $this->getDefault() === null ? '' : ', ' . var_export($this->getDefault(), true);
        
        $result = "DBIntegerColumn::create('" . $this->getName() ."', " . $this->getLength() . "$default)";
      
        if($this->unsigned) {
          $result .= '->setUnsigned(true)';
        } // if
        
        if($this->auto_increment) {
          $result .= '->setAutoIncrement(true)';
        } // if
        
        return $result;
      } // if
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
      return 'integer';
    } // getPhpType

    /**
     * Return PHP bit that will cast raw value to proper value
     *
     * @param string $var
     * @return string
     */
    function getCastingCode() {
      return '(integer) $value';
    } // getCastingCode
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Return auto_increment
     *
     * @return boolean
     */
    function getAutoIncrement() {
    	return $this->auto_increment;
    } // getAutoIncrement
    
    /**
     * Set auto increment flag value
     *
     * @param boolean $value
     * @return DBIntegerColumn
     */
    function &setAutoIncrement($value) {
      $this->auto_increment = (boolean) $value;
      
      return $this;
    } // setAutoIncrement
    
  }