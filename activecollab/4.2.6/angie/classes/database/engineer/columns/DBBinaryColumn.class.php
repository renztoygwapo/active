<?php

  /**
   * Class that represents BLOB/BINARY database columns
   *
   * @package angie.library.database
   * @subpackage engineer
   */
  class DBBinaryColumn extends DBColumn {
    
    /**
     * Binary fields have size
     *
     * @var boolean
     */
    protected $has_size = true;
    
    /**
     * Binary columns can't have default value
     * 
     * @var boolean
     */
    protected $has_default = false;
    
    /**
     * Construct without default value
     *
     * @param string $name
     */
    function __construct($name) {
      parent::__construct($name);
    } // __construct
    
    /**
     * Create new binary field instance
     *
     * @param string $name
     * @return DBBinaryColumn
     */
    static public function create($name) {
      return new DBBinaryColumn($name);
    } // create
    
    /**
     * Return model definition code for this column
     *
     * @return string
     */
    function prepareModelDefinition() {
      $result = "DBBinaryColumn::create('" . $this->getName() ."')";
      
      if($this->getSize() != DBColumn::NORMAL) {
        switch($this->getSize()) {
          case DBColumn::TINY:
            $result .= '->setSize(DBColumn::TINY)';
            break;
          case DBColumn::SMALL:
            $result .= '->setSize(DBColumn::SMALL)';
            break;
          case DBColumn::MEDIUM:
            $result .= '->setSize(DBColumn::MEDIUM)';
            break;
          case DBColumn::BIG:
            $result .= '->setSize(DBColumn::BIG)';
            break;
        } // if
      } // if
      
      return $result;
    } // prepareModelDefinition
    
    /**
     * Prepare type definition
     *
     * @return string
     */
    function prepareTypeDefinition() {
      switch($this->size) {
        case self::BIG:
          return 'longblob';
        case self::SMALL:
        case self::NORMAL:
          return 'blob';
        default:
          return $this->size . 'blob';
      } // if
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
      return 'mixed';
    } // getPhpType

    /**
     * Return PHP bit that will cast raw value to proper value
     *
     * @param string $var
     * @return string
     */
    function getCastingCode() {
      return '$value';
    } // getCastingCode
    
  }