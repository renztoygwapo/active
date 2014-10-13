<?php

  /**
   * Class that represents SET database columns
   *
   * @package angie.library.database
   * @subpackage engineer
   */
  class DBSetColumn extends DBColumn {
    
    /**
     * Enum possibilities
     *
     * @var array
     */
    private $possibilities = array();
    
    /**
     * Construct set field
     *
     * @param string $name
     * @param array $possibilities
     * @param mixed $default
     */
    function __construct($name, $possibilities = array(), $default = null) {
      parent::__construct($name, $default);
    	$this->possibilities = $possibilities;
    } // __construct
    
    /**
     * Construct and return set column instance
     *
     * @param string $name
     * @param array $possibilities
     * @param array $default
     * @return DBSetColumn
     */
    static public function create($name, $possibilities = array(), $default = null) {
      return new DBSetColumn($name, $possibilities, $default);
    } // create
    
    /**
     * Process additional field parameters
     *
     * @param array $additional
     */
    function processAdditional($additional) {
      parent::processAdditional($additional);
      
      if(is_array($additional) && isset($additional[0]) && $additional[0]) {
    	  $this->possibilities = $additional;
    	} // if
    } // processAdditional
    
    /**
     * Returns prepared default value
     *
     * @return string
     */
    function prepareDefault() {
      if(is_array($this->default)) {
        return "'" . implode(',', $this->default) .  "'";
      } elseif($this->default === null) {
        return 'NULL';
      } else {
        return '';
      } // if
    } // prepareDefault
    
    /**
     * Prepare type definition
     *
     * @return string
     */
    function prepareTypeDefinition() {
      $possibilities = array();
      foreach($this->possibilities as $v) {
        $possibilities[] = var_export((string) $v, true);
      } // foreach
      
      return 'set(' . implode(', ', $possibilities) . ')';
    } // prepareTypeDefinition
    
    /**
     * Return model definition code for this column
     *
     * @return string
     */
    function prepareModelDefinition() {
      $possibilities = array();
      
      foreach($this->getPossibilities() as $v) {
        $possibilities[] = var_export($v, true);
      } // foreach
      
      $possibilities = 'array(' . implode(', ', $possibilities) .')';
      
      $default = $this->getDefault() === null ? '' : ', ' . var_export($this->getDefault(), true);
      
      return "DBSetColumn::create('" . $this->getName() ."', $possibilities$default)";
    } // prepareModelDefinition
    
    /**
     * Load from row
     *
     * @param array $row
     */
    function loadFromRow($row) {
      parent::loadFromRow($row);
      
      if(isset($row['Default']) && $row['Default']) {
        $this->setDefault(explode(',', $row['Default']));
      } // if
    } // loadFromRow

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
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Return possibilities
     *
     * @return array
     */
    function getPossibilities() {
    	return $this->possibilities;
    } // getPossibilities
    
    /**
     * Set possibilities value
     *
     * @param array $value
     * @return DBSetColumn
     */
    function &setPossibilities($value) {
      $this->possibilities = $value;
      
      return $this;
    } // setPossibilities
    
  }