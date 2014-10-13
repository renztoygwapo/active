<?php

  /**
   * Class that represents VARCHAR database columns
   *
   * @package angie.library.database
   * @subpackage engineer
   */
  class DBStringColumn extends DBColumn {
    
    /**
     * Field length (max is 255)
     *
     * @var integer
     */
    protected $length = 255;
    
    /**
     * Construct string column
     *
     * @param string $name
     * @param integer $lenght
     * @param mixed $default
     */
    function __construct($name, $lenght = 255, $default = null) {
      parent::__construct($name, $default);
      
      $this->length = (integer) $lenght;
    } // __construct
    
    /**
     * Create new integer column instance
     *
     * @param string $name
     * @param integer $lenght
     * @param mixed $default
     * @return DBStringColumn
     */
    static public function create($name, $lenght = 255, $default = null) {
      return new DBStringColumn($name, $lenght, $default);
    } // create
    
    /**
     * Process additional field properties
     *
     * @param array $additional
     */
    function processAdditional($additional) {
      parent::processAdditional($additional);
      
      if(is_array($additional) && isset($additional[0]) && $additional[0]) {
    	  $this->length = (integer) $additional[0];
    	} // if
    } // processAdditional
    
    /**
     * Return type definition
     *
     * @return string
     */
    function prepareTypeDefinition() {
      return "varchar($this->length)";
    } // prepareTypeDefinition
    
    /**
     * Return model definition code for this column
     *
     * @return string
     */
    function prepareModelDefinition() {
      if($this->getName() == 'name') {
        return 'DBNameColumn::create(' . $this->getLength() . ')';
      } elseif($this->getName() == 'type') {
        if($this->getDefault() === null) {
          return 'DBTypeColumn::create()';
        } else {
          return 'DBTypeColumn::create(' . var_export($this->getDefault(), true) . ')';
        } // if
      } else {
        $default = $this->getDefault() === null ? '' : ', ' . var_export($this->getDefault(), true);
        
        return "DBStringColumn::create('" . $this->getName() ."', " . $this->getLength() . "$default)";
      } // if
    } // prepareModelDefinition
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Return length
     *
     * @return integer
     */
    function getLength() {
    	return $this->length;
    } // getName
    
    /**
     * Set string field lenght
     *
     * @param unknown_type $value
     * @return DBStringColumn
     */
    function &setLength($value) {
      $this->length = (integer) $value;
      
      return $this;
    } // setLength
    
  }