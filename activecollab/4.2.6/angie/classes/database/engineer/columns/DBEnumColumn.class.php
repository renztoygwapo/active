<?php

  /**
   * Class that represents ENUM database columns
   *
   * @package angie.library.database
   * @subpackage engineer
   */
  class DBEnumColumn extends DBColumn {
    
    /**
     * Enum possibilities
     *
     * @var array
     */
    private $possibilities = array();
    
    /**
     * Construct enum column
     *
     * @param string $name
     * @param array $possibilities
     * @param mixed $default
     */
    function __construct($name, $possibilities = null, $default = null) {
    	parent::__construct($name, $default);
    	
    	if(is_array($possibilities)) {
    	  $this->possibilities = $possibilities;
    	} // if
    } // __construct
    
    /**
     * Create new column instance
     *
     * @param string $name
     * @param array $possibilities
     * @param mixed $default
     * @return DBEnumColumn
     */
    static public function create($name, $possibilities = null, $default = null) {
      return new DBEnumColumn($name, $possibilities, $default);
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
     * Prepare type definition
     *
     * @return string
     */
    function prepareTypeDefinition() {
      $possibilities = array();
      foreach($this->possibilities as $v) {
        $possibilities[] = var_export((string) $v, true);
      } // foreach
      
      return 'enum(' . implode(', ', $possibilities) . ')';
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
      
      return "DBEnumColumn::create('" . $this->getName() ."', $possibilities$default)";
    } // prepareModelDefinition

    /**
     * Return PHP bit that will cast raw value to proper value
     *
     * @return string
     */
    function getCastingCode() {
      return '(empty($value) ? NULL : (string) $value)';
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
     * Set array of possibilities
     *
     * @param array $value
     * @return DBEnumColumn
     */
    function &setPossibilities($value) {
      $this->possibilities = $value;
      
      return $this;
    } // &setPossibilities
    
  }