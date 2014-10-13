<?php

  /**
   * Numeric columns class (as foundation for integers and floats)
   *
   * @package angie.library.database
   * @subpackage subpackage
   */
  abstract class DBNumericColumn extends DBColumn {
    
    /**
     * Integer fields have size
     *
     * @var boolean
     */
    protected $has_size = true;
    
    /**
     * Field length
     *
     * @var integer
     */
    protected $length;
    
    /**
     * Check if this column is unsisgned or not
     *
     * @var boolean
     */
    protected $unsigned = false;
    
    /**
     * Construct numeric column
     *
     * @param string $name
     * @param integer $lenght
     * @param mixed $default
     */
    function __construct($name, $lenght = DBColumn::NORMAL, $default = null) {
    	parent::__construct($name, $default);
    	
    	$this->length = (integer) $lenght;
    } // __construct
    
    /**
     * Load numberic field details from row
     *
     * @param array $row
     */
    function loadFromRow($row) {
      parent::loadFromRow($row);
      $this->unsigned = strpos($row['Type'], 'unsigned') !== false;
    } // loadFromRow
    
    /**
     * Process additional parameters
     *
     * @param array $additional
     */
    function processAdditional($additional) {
      parent::processAdditional($additional);
      
      if(is_array($additional) && isset($additional[0]) && $additional[0]) {
    	  $this->length = (integer) $additional[0];
    	} // if
    } // processAdditional
    
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
     * Set field lenght
     *
     * @param integer $value
     * @return DBNumericColumn
     */
    function &setLenght($value) {
      $this->length = (integer) $value;
      
      return $this;
    } // setLenght
    
    /**
     * Return unsigned
     *
     * @return boolean
     */
    function getUnsigned() {
    	return $this->unsigned;
    } // getUnsigned
    
    /**
     * Set unsigned column flag
     *
     * @param boolean $value
     * @return DBNumericColumn
     */
    function &setUnsigned($value) {
      $this->unsigned = (boolean) $value;
      
      return $this;
    } // setUnsigned
    
  }