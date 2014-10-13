<?php

  /**
   * Class that represents DECIMAL database columns
   * 
   * @package angie.library.database
   * @subpackage engineer
   */
  class DBDecimalColumn extends DBNumericColumn {
  
    /**
     * Column scale
     *
     * @var integer
     */
    private $scale = 2;
    
    /**
     * Construct decimal column
     *
     * @param string $name
     * @param integer $lenght
     * @param integer $scale
     * @param mixed $default
     */
    function __construct($name, $lenght = 12, $scale = 2, $default = null) {
      if($default !== null) {
        $default = (float) $default;
      } // if
      
    	parent::__construct($name, $lenght, $default);
    	
    	$this->scale = (integer) $scale;
    } // __construct
    
    /**
     * Create and return decimal column
     *
     * @param string $name
     * @param integer $lenght
     * @param integer $scale
     * @param mixed $default
     * @return DBDecimalColumn
     */
    static public function create($name, $lenght = 12, $scale = 2, $default = null) {
      return new DBDecimalColumn($name, $lenght, $scale, $default);
    } // create
    
    /**
     * Process additional field parameters
     *
     * @param array $additional
     */
    function processAdditional($additional) {
      parent::processAdditional($additional);
      
      if(is_array($additional) && isset($additional[1]) && $additional[1]) {
    	  $this->scale = (integer) $additional[1];
    	} // if
    } // processAdditional
    
    /**
     * Prepare type definition
     *
     * @return string
     */
    function prepareTypeDefinition() {
      $result = 'decimal(' . $this->length . ', ' . $this->scale . ')';
      if($this->unsigned) {
        $result .= ' unsigned';
      } // if
      return $result;
    } // prepareTypeDefinition
    
    /**
     * Return model definition code for this column
     *
     * @return string
     */
    function prepareModelDefinition() {
      $default = $this->getDefault() === null ? '' : ', ' . var_export($this->getDefault(), true);
      
      $result = "DBDecimalColumn::create('" . $this->getName() ."', " . $this->getLength() . ', ' . $this->getScale() . "$default)";
      
      if($this->unsigned) {
        $result .= '->setUnsigned(true)';
      } // if
      
      return $result;
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
      return 'float';
    } // getPhpType

    /**
     * Return PHP bit that will cast raw value to proper value
     *
     * @param string $var
     * @return string
     */
    function getCastingCode() {
      return '(float) $value';
    } // getCastingCode
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Return scale
     *
     * @return integer
     */
    function getScale() {
    	return $this->scale;
    } // getScale
    
    /**
     * Set scale
     *
     * @param integer $value
     * @return DBDecimalColumn
     */
    function &setScale($value) {
      $this->scale = (integer) $value;
      
      return $this;
    } // setScale
    
  }