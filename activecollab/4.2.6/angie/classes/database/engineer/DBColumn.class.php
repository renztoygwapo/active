<?php

  /**
   * Foudation class that describes general database column properties
   *
   * @package angie.library.database
   * @subpackage engineer
   */
  abstract class DBColumn {
    
    /**
     * Size variations
     *
     * @var string
     */
    const TINY = 'tiny'; 
    const SMALL = 'small'; 
    const NORMAL = 'normal'; 
    const MEDIUM = 'medium'; 
    const BIG = 'big'; 
    
    /**
     * Column name
     *
     * @var string
     */
    protected $name;
    
    /**
     * Default value
     *
     * @var mixed
     */
    protected $default = null;
    
    /**
     * Field comment
     *
     * @var string
     */
    protected $comment = null;
    
    /**
     * Field size, if field has it
     *
     * @var string
     */
    protected $size = DBColumn::NORMAL;
    
    /**
     * True for fields that have size (TINY, SMALL, NORMAL, MEDIUM, BIG)
     *
     * @var boolean
     */
    protected $has_size = false;
    
    /**
     * Indicates whether this column can have default value or not
     * 
     * @var boolean
     */
    protected $has_default = true;
    
    /**
     * Parent table
     *
     * @var DBTable
     */
    protected $table;
    
    /**
     * Construct database column
     *
     * @param string $name
     * @param mixed $default
     */
    function __construct($name, $default = null) {
    	$this->name = $name;
    	$this->default = $default;
    } // __construct
    
    /**
     * Create new column instance
     *
     * @param string $name
     * @param mixed $default
     */
    static public function create($name, $default = null) {
      throw new NotImplementedError(__METHOD__);
    } // create
    
    /**
     * Load column information from row returned from SHOW COLUMNS query
     *
     * @param array $row
     */
    function loadFromRow($row) {
    	$this->default = $row['Null'] == 'NO' && $row['Default'] !== null ? $row['Default'] : null;
    } // loadFromRow
    
    /**
     * Process additional parameters like VARCHAR(LENGHT), INT(10) or FLOAT(4,2)
     *
     * @param array $additional
     */
    function processAdditional($additional) {
      
    } // processAdditional
    
    /**
     * Prepare field definition
     *
     * @return string
     */
    function prepareDefinition() {
      $result = DB::escapeFieldName($this->name) . ' ' . $this->prepareTypeDefinition() . ' ' . $this->prepareNull();
      
      if($this->has_default && $this->prepareDefault() !== '') {
        $result .= ' DEFAULT ' . $this->prepareDefault();
      } // if
      
      if($this->comment) {
        $result .= ' COMMENT ' . DB::escape($this->comment);
      } // if
      
      return $result;
    } // prepareDefinition
    
    /**
     * Prepare type definition
     *
     * @return string
     */
    abstract function prepareTypeDefinition();
    
    /**
     * Return model definition code for this column
     *
     * @return string
     */
    function prepareModelDefinition() {
      $default = $this->getDefault() === null ? '' : ', ' . var_export($this->getDefault(), true);
      
      $result = get_class($this) . "::create('" . $this->getName() ."'$default)";
      
      if($this->has_size && $this->getSize() != DBColumn::NORMAL) {
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
     * Prepare null / not null part of the definition
     *
     * @return string
     */
    protected function prepareNull() {
      return $this->default === null ? '' : 'NOT NULL';
    } // prepareNull
    
    /**
     * Prepare default value
     *
     * @return string
     */
    function prepareDefault() {
    	if($this->default === null) {
        return 'NULL';
      } elseif($this->default === 0) {
        return '0';
      } elseif($this->default === '') {
        return "''";
      } else {
        return DB::escape($this->default);
      } // if
    } // prepareDefault
    
    /**
     * Check if this column belogs to an index
     *
     * @return boolean
     */
    function isPrimaryKey() {
      foreach($this->table->getIndices() as $index) {
        if(in_array($this->name, $index->getColumns()) && $index->isPrimary()) {
          return true;
        } // if
      } // foreach
      return false;
    } // isPrimaryKey
    
    /**
     * Event that table triggers when this column is added to the table
     */
    function addedToTable() {
      
    } // addedToTable

    // ---------------------------------------------------
    //  Model generator
    // ---------------------------------------------------

    /**
     * Return verbose PHP type
     *
     * @return string
     */
    function getPhpType() {
      return 'string';
    } // getPhpType

    /**
     * Return PHP bit that will cast raw value to proper value
     *
     * @return string
     */
    function getCastingCode() {
      return '(string) $value';
    } // getCastingCode
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Return name
     *
     * @return string
     */
    function getName() {
    	return $this->name;
    } // getName
    
    /**
     * Set name
     *
     * @param string $value
     * @return DBColumn
     */
    function &setName($value) {
      $this->name = $value;
      
      return $this;
    } // setName
    
    /**
     * Return size
     *
     * @return string
     */
    function getSize() {
    	return $this->size;
    } // getName
    
    /**
     * Set size
     *
     * @param string $value
     * @return DBColumn
     */
    function &setSize($value) {
      $this->size = $value;
      
      return $this;
    } // setName
    
    /**
     * Return default
     *
     * @return mixed
     */
    function getDefault() {
    	return $this->default;
    } // getDefault
    
    /**
     * Set default
     *
     * @param mixed $value
     * @return DBColumn
     */
    function &setDefault($value) {
      $this->default = $value;
      
      return $this;
    } // setDefault
    
    /**
     * Return comment
     *
     * @return string
     */
    function getComment() {
    	return $this->comment;
    } // getComment
    
    /**
     * Set comment
     *
     * @param string $value
     * @return DBColumn
     */
    function &setComment($value) {
      $this->comment = $value;
      
      return $this;
    } // setComment
    
    /**
     * Return table
     *
     * @return DBTable
     */
    function getTable() {
    	return $this->table;
    } // getTable
    
    /**
     * Set table
     *
     * @param DBTable $value
     * @return DBColumn
     */
    function &setTable(DBTable $value) {
      $this->table = $value;
      
      return $this;
    } // setTable
    
  }