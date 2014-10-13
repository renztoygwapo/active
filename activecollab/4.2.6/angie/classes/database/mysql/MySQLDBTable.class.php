<?php

  /**
   * MySQL database table implementation
   *
   * @package angie.library.database
   */
  class MySQLDBTable extends DBTable {
    
    // Storage engines
    const ENGINE_INNODB = 'InnoDB';
    const ENGINE_MYISAM = 'MyISAM';
    const ENGINE_MEMORY = 'Memory';
    
    /**
     * Prefered storage engine for this table
     *
     * @var string
     */
    protected $storage_engine = self::ENGINE_INNODB;
    
    /**
     * Default table character set
     *
     * @var string
     */
    protected $character_set = 'utf8';
    
    /**
     * Default table collation
     *
     * @var string
     */
    protected $collation = 'utf8_general_ci';
    
    /**
     * Create and return new table instance
     *
     * @param string $name
     * @param boolean $load
     */
    static public function create($name, $load = false) {
      return new MySQLDBTable($name, $load);
    } // create
    
    // ---------------------------------------------------
    //  Options
    // ---------------------------------------------------
    
    /**
     * Return array of table options
     * 
     * @return array
     */
    function getOptions() {
      return array(
        'ENGINE' => $this->getStorageEngine(), 
        'DEFAULT CHARSET' => $this->getCharacterSet(), 
        'COLLATE' => $this->getCollation(), 
      );
    } // getOptions
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Return storage_engine
     *
     * @return string
     */
    function getStorageEngine() {
    	return $this->storage_engine;
    } // getStorageEngine
    
    /**
     * Set storage_engine
     *
     * @param string $value
     * @return MySQLDBTable
     */
    function &setStorageEngine($value) {
      $this->storage_engine = $value;
      
      return $this;
    } // setStorageEngine
    
    /**
     * Return character_set
     *
     * @return string
     */
    function getCharacterSet() {
    	return $this->character_set;
    } // getCharacterSet
    
    /**
     * Set character_set
     *
     * @param string $value
     * @param string $collation
     * @return MySQLDBTable
     */
    function &setCharacterSet($value, $collation = null) {
      if($this->character_set != $value) {
        $this->character_set = $value;
        $this->collation = $collation ? $collation : $this->getDefaultCollationForCharset($value);
      } // if
      
      return $this;
    } // setCharacterSet
    
    /**
     * Return collation
     *
     * @return string
     */
    function getCollation() {
    	return $this->collation;
    } // getCollation
    
    /**
     * Set collation
     *
     * @param string $value
     * @return MySQLDBTable
     */
    function &setCollation($value) {
      $this->collation = $value;
      
      return $this;
    } // setCollation
    
    // ---------------------------------------------------
    //  Utils
    // ---------------------------------------------------
    
    /**
     * Returns default collation for given charset
     *
     * @param string $charset
     * @return string
     */
    function getDefaultCollationForCharset($charset) {
      $row = DB::executeFirstRow("SHOW CHARACTER SET LIKE ?", array($charset));
      
      if($row && isset($row['Default collation'])) {
        return $row['Default collation'];
      } else {
        throw new InvalidParamError('charset', $charset, "Unknown MySQL charset '$charset'");
      } // if
    } // getDefaultCollationForCharset
    
  }