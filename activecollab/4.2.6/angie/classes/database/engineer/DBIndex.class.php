<?php

  /**
   * Class that represents table index in a database
   *
   * @package angie.library.database
   * @subpackage engineer
   */
  class DBIndex {
    
    /**
     * Index types
     */
    const PRIMARY = 0;
    const UNIQUE = 1;
    const KEY = 2;
    const FULLTEXT = 3;
    
    /**
     * Index name
     *
     * @var string
     */
    protected $name;
    
    /**
     * Array of columns for composite keys
     *
     * @var array
     */
    protected $columns = array();
    
    /**
     * Key type
     *
     * @var integer
     */
    protected $type = DBIndex::KEY;
    
    /**
     * Parent DB table
     *
     * @var DBTable
     */
    protected $table;
    
    /**
     * Construct DBIndex
     * 
     * If $columns is NULL, system will create index for the field that has the 
     * same name as the index
     *
     * @param string $name
     * @param integer $type
     * @param mixed $columns
     */
    function __construct($name, $type = DBIndex::KEY, $columns = null) {
    	$this->name = $name;
    	$this->type = $type;
    	
    	if($name == 'PRIMARY') {
    	  $this->type = DBIndex::PRIMARY;
    	} // if
    	
    	// Use column name
    	if($columns === null) {
    	  $this->addColumn($name);
    	
    	// Columns are specified
    	} elseif($columns) {
    	  $columns = is_array($columns) ? $columns : array($columns);
    	  foreach($columns as $column) {
    	    if($column instanceof DBColumn) {
    	      $this->addColumn($column->getName());
    	    } else {
    	      $this->addColumn($column);
    	    } // if
    	  } // foreach
    	} // if
    } // __construct
    
    /**
     * Create and return new index instance
     *
     * @param string $name
     * @param integer $type
     * @param mixed $columns
     * @return DBIndex
     */
    static public function create($name, $type = DBIndex::KEY, $columns = null) {
      return new DBIndex($name, $type, $columns);
    } // create
    
    /**
     * Load index data from row returned by SHOW INDEX query
     *
     * @param array $row
     */
    function loadFromRow($row) {
      $this->columns[] = $row['Column_name'];
      
      if($this->name == 'PRIMARY') {
        $this->type = DBIndex::PRIMARY;
      } elseif($row['Index_type'] == 'FULLTEXT') {
        $this->type = DBIndex::FULLTEXT ;
      } elseif(! (boolean) $row['Non_unique']) {
        $this->type = DBIndex::UNIQUE;
      } else {
        $this->type = DBIndex::KEY;
      } // if
    } // loadFromRow
    
    /**
     * Interface to columns array
     *
     * @return array
     */
    function getColumns() {
      return $this->columns;
    } // getColumns
    
    /**
     * Add a column to the list of columns
     *
     * @param string $column_name
     */
    function addColumn($column_name) {
    	$this->columns[] = $column_name;
    } // addColumn
    
    /**
     * Prepare key definition
     *
     * @return string
     */
    function prepareDefinition() {
      switch($this->type) {
        case DBIndex::PRIMARY:
          $result = 'PRIMARY KEY';
          break;
        case DBIndex::UNIQUE:
          $result = 'UNIQUE ' . DB::escapeFieldName($this->name);
          break;
        case DBIndex::FULLTEXT:
          $result = 'FULLTEXT ' . DB::escapeFieldName($this->name);
          break;
        default:
          $result = 'INDEX ' . DB::escapeFieldName($this->name);
          break;
      } // if
      
      $column_names = array();
      foreach($this->columns as $column) {
        $column_names[] = DB::escapeFieldName($column);
      } // foreach
      
      return $result . ' (' . implode(', ', $column_names) . ')';
    } // prepareDefinition
    
    /**
     * Return model definition code for this index
     *
     * @return string
     */
    function prepareModelDefinition() {
      if(count($this->columns) == 1) {
        $columns = var_export($this->columns[0], true);
      } else {
        $columns = array();
        foreach($this->columns as $k => $v) {
          $columns[] = var_export($v, true);
        } // foreach
        $columns = 'array(' . implode(', ', $columns) . ')';
      } // if
      
      // Primary key
      if($this->type == DBIndex::PRIMARY) {
        return "DBIndexPrimary::create($columns)";
        
      // Index where the name of the index is the same as the column
      } elseif($this->type == DBINdex::KEY && count($this->columns) == 1 && $this->getName() == $this->columns[0]) {
        return "DBIndex::create('" . $this->getName() . "')";
        
      // Everything else
      } else {
        switch($this->type) {
          case DBIndex::UNIQUE:
            $type = 'DBIndex::UNIQUE';
            break;
          case DBIndex::FULLTEXT:
            $type = 'DBIndex::FULLTEXT';
            break;
          default:
            $type = 'DBIndex::KEY';
            break;
        } // if
        
        return "DBIndex::create('" . $this->getName() . "', $type, $columns)";
      } // if
    } // function
    
    // ---------------------------------------------------
    //  Type
    // ---------------------------------------------------
    
    /**
     * Returns true if this key is primary key
     *
     * @return boolean
     */
    function isPrimary() {
    	return $this->type == DBIndex::PRIMARY;
    } // isPrimary
    
    /**
     * Returns true if this is UNIQUE key
     *
     * @return boolean
     */
    function isUnique() {
    	return ($this->type == DBIndex::PRIMARY) || ($this->type == DBIndex::UNIQUE);
    } // isUnique
    
    /**
     * Returns true if this is FULLTEXT key
     *
     * @return boolean
     */
    function isFulltext() {
    	return $this->type == DBIndex::FULLTEXT;
    } // isFulltext
    
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
     * @return DBIndex
     */
    function &setName($value) {
      $this->name = $value;
      
      return $this;
    } // setName
    
    /**
     * Return type
     *
     * @return integer
     */
    function getType() {
    	return $this->type;
    } // getType
    
    /**
     * Set type
     *
     * @param integer $value
     * @return DBIndex
     */
    function &setType($value) {
      $this->type = $value;
      
      return $this;
    } // setType
    
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
     * @return DBIndex
     */
    function &setTable(DBTable $value) {
      $this->table = $value;
      
      return $this;
    } // setTable
    
  }

?>