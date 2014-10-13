<?php

  /**
   * Database table manager tool
   *
   * @package angie.library.database
   */
  abstract class DBTable {
    
    /**
     * Table name
     *
     * @var string
     */
    private $name;
    
    /**
     * Table is new (does not exist in database)
     *
     * @var boolean
     */
    private $new = true;
    
    /**
     * Named list of all table columns
     *
     * @var NamedList
     */
    private $columns;
    
    /**
     * Named list of all table indices
     *
     * @var NamedList
     */
    private $indices;
    
    /**
     * Column type map
     * 
     * Array maps specific field types with classes that represent them
     *
     * @var array
     */
    private $type_map = array(
      'DBBinaryColumn' => array(
        'tinyblob' => DBColumn::TINY,
        'blob' => DBColumn::NORMAL,
        'mediumblob' => DBColumn::MEDIUM,
        'longblob' => DBColumn::BIG,
      ),
      
      'DBBoolColumn' => array(
        'bool' => null,
        'boolean' => null,
      ),
      
      'DBDateColumn' => array(
        'date' => null,
      ),
      
      'DBDateTimeColumn' => array(
        'timestamp' => null,
        'datetime' => null,
      ),
      
      'DBEnumColumn' => array(
        'enum' => null,
      ),
      
      'DBFloatColumn' => array(
        'float' => null,
        'double' => null,
        'real' => null,
      ),
      
      'DBDecimalColumn' => array(
        'decimal' => null,
      	'numeric' => null,
      ), 
      
      'DBIntegerColumn' => array(
        'tinyint' => DBColumn::TINY,
        'smallint' => DBColumn::SMALL,
        'mediumint' => DBColumn::MEDIUM,
        'int' => DBColumn::NORMAL,
        'bigint' => DBColumn::BIG,
      ),
      
      'DBSetColumn' => array(
        'set' => null,
      ),
      
      'DBStringColumn' => array(
        'varchar' => null,
      ),
      
      'DBTextColumn' => array(
        'tinytext' => DBColumn::TINY,
        'text' => DBColumn::NORMAL,
        'mediumtext' => DBColumn::MEDIUM,
        'longtext' => DBColumn::BIG,
      ),
      
      'DBTimeColumn' => array(
        'time' => null,
      ),
    );
    
    /**
     * Construct new table
     * 
     * If $load is set to true instance will load table informatin from database
     *
     * @param string $name
     * @param boolean $load
     */
    function __construct($name, $load = false) {
      $this->columns = new NamedList();
      $this->indices = new NamedList();
      
    	$this->name = $name;
    	
    	if($load) {
    	  $this->load();
    	} // if
    } // __construct

    /**
     * Create new table instance
     *
     * This is a stub method, that needs to be overriden in classes that inherit
     * DBTable
     *
     * @param string $name
     * @param bool $load
     * @throws NotImplementedError
     */
    static public function create($name, $load = false) {
      throw new NotImplementedError(__METHOD__);
    } // create
    
    // ---------------------------------------------------
    //  CRUD
    // ---------------------------------------------------
    
    /**
     * Return CREATE TABLE command code
     * 
     * @param string $table_prefix
     * @return string
     */
    function getCreateCommand($table_prefix = null) {
      $column_definitions = array();
      foreach($this->columns as $column) {
        $column_definitions[] = '  ' . $column->prepareDefinition();
      } // if
      
      $index_definitions = array();
      foreach($this->indices as $index) {
        $index_definitions[] = '  ' . $index->prepareDefinition();
      } // if
      
      $options = array();
      
      foreach($this->getOptions() as $k => $v) {
        $options[] = "$k=$v";
      } // foreach
      
      $table_name = DB::escapeTableName("{$table_prefix}{$this->name}");
      
      $command = "CREATE TABLE $table_name (\n";
      $command .= implode(",\n", $column_definitions);
      if(is_foreachable($index_definitions)) {
        $command .= ",\n" . implode(",\n", $index_definitions);
      } // if
      $command .= "\n) " . implode(' ', $options) . ';';
      
      return $command;
    } // getCreateCommand
    
    /**
     * Check if table with this name exists in database
     *
     * @param string $table_prefix
     * @return boolean
     */
    function exists($table_prefix = null) {
    	$info = DB::execute('SHOW TABLES LIKE ?', $table_prefix . $this->name);

      return $info instanceof DBResult && $info->count() > 0;
    } // exists
    
    /**
     * Load table data from database
     *
     * @param string $table_prefix
     */
    function load($table_prefix = null) {
    	$column_rows = DB::execute('SHOW COLUMNS FROM ' . DB::escapeTableName($table_prefix . $this->name));
    	if(is_foreachable($column_rows)) {
    	  foreach($column_rows as $column_row) {
    	    $column = $this->typeStringToColumn($column_row['Field'], $column_row['Type']);
    	    $column->loadFromRow($column_row);
    	    
    	    $this->getColumns()->add($column->getName(), $column);
    	  } // foreach
    	  
    	  $index_rows = DB::execute('SHOW INDEX FROM ' . $this->name);
    	  if(is_foreachable($index_rows)) {
    	    foreach($index_rows as $index_row) {
    	      $name = $index_row['Key_name'];
    	      
    	      if($this->getIndices()->get($name) instanceof DBIndex) {
    	        $this->getIndices()->get($name)->addColumn($index_row['Column_name']); // Key on multiple columns
    	      } else {
    	        $index = new DBIndex($name, DBIndex::KEY, false);
      	      $index->loadFromRow($index_row);
      	      $index->setTable($this);
      	      
      	      $this->getIndices()->add($index->getName(), $index);
    	      } // if
    	    } // foreach
    	  } // if
    	  
    	  $this->new = false;
    	} // if
    } // load
    
    /**
     * Save new table into the database via provided connection
     *
     * @param string $table_prefix
     */
    function save($table_prefix = null) {
      DB::execute($this->getCreateCommand($table_prefix));
      $this->new = false;
    } // save
    
    /**
     * Drop table from database
     *
     * @param string $table_prefix
     * @throws InvalidParamError
     */
    function delete($table_prefix = null) {
      if($this->exists($table_prefix)) {
    	  DB::execute('DROP TABLE ' . DB::escapeTableName($table_prefix . $this->name));
    	  $this->new = true;
      } else {
      	throw new InvalidParamError('name', $this->name, "Table '$this->name' does not exist");
      } // if
    } // delete
    
    // ---------------------------------------------------
    //  Columns
    // ---------------------------------------------------
    
    /**
     * Provide interface to columns property
     *
     * @return NamedList
     */
    function getColumns() {
    	return $this->columns;
    } // getColumns
    
    /**
     * Return column by name
     *
     * @param string $name
     * @return DBColumn
     */
    function getColumn($name) {
      return isset($this->columns[$name]) ? $this->columns[$name] : null;
    } // getColumn
    
    /**
     * Add column to the list of columns
     *
     * @param DBColumn $column
     * @param string $after_column_name
     * @return DBColumn
     * @throws InvalidInstanceError
     */
    function addColumn($column, $after_column_name = null) {
      
      // Add single column to the table
      if($column instanceof DBColumn) {
        $column->setTable($this);
      
        if($this->isLoaded()) {
          $after = $after_column_name && $this->getColumn($after_column_name) instanceof DBColumn ? " AFTER $after_column_name " : '';
          
          if($column instanceof DBIntegerColumn && $column->getAutoIncrement()) {
            $prepared_definition = str_replace('auto_increment', '', $column->prepareDefinition());
            
            // Add field without auto - increment flag
            DB::execute("ALTER TABLE $this->name ADD $prepared_definition $after");
            
            if($this->hasPrimaryKey()) {
              $this->addIndex(new DBIndex($column->getName(), DBIndex::PRIMARY, $column->getName()));;
            } else {
              $this->addIndex(new DBIndexPrimary($column->getName()));
            } // if
            
            // Now that we have a key we can set auto_increment flag
            DB::execute("ALTER TABLE $this->name CHANGE " . $column->getName() . ' ' . $column->prepareDefinition() . ' ' . $after);
          } else {
            DB::execute("ALTER TABLE $this->name ADD " . $column->prepareDefinition() . ' ' . $after);
          } // if
        } else {
          if($column instanceof DBIntegerColumn && $column->getAutoIncrement()) {
            if($this->hasPrimaryKey()) {
              $this->addIndex(new DBIndex($column->getName(), DBIndex::PRIMARY, $column->getName()));;
            } else {
              $this->addIndex(new DBIndexPrimary($column->getName()));;
            } // if
          } // if
        } // if
        
        // Add and trigger added event
        $this->columns->add($column->getName(), $column);
        $column->addedToTable();
        
      // Add composite column to the table
      } elseif($column instanceof DBCompositeColumn) {
        $column->setTable($this);
        
        $after = $after_column_name;
        
        foreach($column->getColumns() as $c) {
          $this->addColumn($c, $after);
          $after = $c->getName();
        } // foreach
        
        $column->addedToTable();
      } else {
        throw new InvalidInstanceError('column', $column, array('DBColumn', 'DBCompositeColumn'));
      } // if 
      
      return $column;
    } // addColumn
    
    /**
     * Add array of columns
     *
     * @param array $columns
     * @return DBTable
     */
    function &addColumns($columns) {
      foreach($columns as &$column) {
        $this->addColumn($column);
      } // foreach
      
      return $this;
    } // addColumns
    
    /**
     * Alter existing column
     *
     * @param string $name
     * @param DBColumn $new_definition
     * @return DBColumn
     * @throws InvalidParamError
     */
    function alterColumn($name, DBColumn $new_definition) {
      if($this->getColumn($name) instanceof DBColumn) {
        if(!($new_definition->getTable() instanceof DBTable) || ($new_definition->getTable()->getName() != $this->name)) {
          $new_definition->setTable($this);
        } // if
        
        if($new_definition instanceof DBIntegerColumn && $new_definition->getAutoIncrement()) {
          $key_exists = false;
          foreach($this->getIndices() as $index) {
            if(in_array($new_definition->getName(), $index->getColumns())) {
              $key_exists = true;
              break;
            } // if
          } // foreach
          
          if(!$key_exists) {
            if($this->hasPrimaryKey()) {
              $this->addIndex(new DBIndex($new_definition->getName(), DBIndex::PRIMARY, $new_definition->getName()));;
            } else {
              $this->addIndex(new DBIndexPrimary($new_definition->getName()));
            } // if
          } // if
        } // if
        
      	DB::execute("ALTER TABLE $this->name CHANGE $name " . $new_definition->prepareDefinition());
      	$this->columns[$name] = $new_definition;
      } else {
        throw new InvalidParamError('name', $name, "Column '$name' does not exist");
      } // if
      
      return $this->getColumn($name);
    } // alterColumn
    
    /**
     * Drop specific column
     *
     * @param string $name
     * @throws InvalidParamError
     */
    function dropColumn($name) {
      if($this->getColumn($name) instanceof DBColumn) {
        DB::execute("ALTER TABLE $this->name DROP $name");
      } else {
        throw new InvalidParamError('name', $name, "Column '$name' does not exist");
      } // if
    } // dropColumn
    
    // ---------------------------------------------------
    //  Indices
    // ---------------------------------------------------
    
    /**
     * Provide interface to indices property
     *
     * @return NamedList
     */
    function getIndices() {
    	return $this->indices;
    } // getIndices
    
    /**
     * Return single index
     *
     * @return DBIndex
     */
    function getIndex($name) {
    	return isset($this->indices[$name]) ? $this->indices[$name] : null;
    } // getIndex
    
    /**
     * Add index to table definition
     *
     * @param DBIndex $index
     */
    function addIndex(DBIndex $index) {
      if($this->isLoaded()) {
        DB::execute("ALTER TABLE $this->name ADD " . $index->prepareDefinition());
      } // if
      
      $index->setTable($this);
      $this->getIndices()->add($index->getName(), $index);
    } // addIndex
    
    /**
     * Add indices
     *
     * @param array $indices
     * @return DBTable
     */
    function &addIndices($indices) {
      foreach($indices as &$index) {
        $this->addIndex($index);
      } // foreach
      
      return $this;
    } // addIndices
    
    /**
     * Alter existing index
     *
     * @param string $name
     */
    function alterIndex($name, DBIndex $new_definition) {
      if($this->getIndex($name) instanceof DBIndex) {
      	DB::execute("ALTER TABLE $this->name DROP INDEX $name, ADD " . $new_definition->prepareDefinition());
      	$this->indices[$name] = $new_definition;
      } else {
        throw new InvalidParamError('name', $name, "Index '$name' does not exist");
      }
    } // alterIndex
    
    /**
     * Returns true if this table has primary key
     *
     * @return boolean
     */
    function hasPrimaryKey() {
    	foreach($this->getIndices() as $index) {
    	  if($index->getType() == DBIndex::PRIMARY) {
    	    return true;
    	  } // if
    	} // foreach
    	
    	return false;
    } // hasPrimaryKey
    
    // ---------------------------------------------------
    //  Options
    // ---------------------------------------------------
    
    /**
     * Return array of table options
     * 
     * @return array
     */
    abstract function getOptions();
    
    // ---------------------------------------------------
    //  Flags
    // ---------------------------------------------------
    
    /**
     * Returns true if this table is new, not loaded
     *
     * @return boolean
     */
    function isNew() {
    	return $this->new;
    } // isNew
    
    /**
     * Returns true if this table exists and is loaded
     *
     * @return boolean
     */
    function isLoaded() {
    	return !$this->new;
    } // isLoaded
    
    // ---------------------------------------------------
    //  Util
    // ---------------------------------------------------
    
    /**
     * Analyze type string and return proper column instance
     *
     * @param string $name
     * @param string $string
     * @return DBColumn
     */
    private function typeStringToColumn($name, $string) {
      $string = str_replace('tinyint(1)', 'bool', $string); // alias!
      
      $parts = explode(' ', $string);
      
      $first_part = isset($parts[0]) ? $parts[0] : '';
      
      $open_bracket = strpos($first_part, '(');
      $close_bracket = strpos($first_part, ')');
      
      if($open_bracket !== false && $close_bracket !== false) {
        $type_name = substr($first_part, 0, $open_bracket);
        $additional = explode(',', substr($first_part, $open_bracket + 1, $close_bracket - $open_bracket - 1));
        if(is_foreachable($additional)) {
          foreach($additional as $k => $v) {
            $additional[$k] = trim($v, "'");
          } // foreach
        } // if
      } else {
        $type_name = $first_part;
        $additional = null;
      } // if
      
      $type_class = '';
      $type_size = null;
      
      foreach($this->type_map as $class_name => $variations) {
        foreach($variations as $k => $v) {
          if($k == $type_name) {
            $type_class = $class_name;
            if($v !== null) {
              $type_size = $v;
            } // if
            break 2;
          } // if
        } // foreach
      } // foreach
      
      $column = new $type_class($name);
      $column->setTable($this);
      if($additional) {
        $column->processAdditional($additional);
      } // if
      if($type_size !== null) {
        $column->setSize($type_size);
      } // if
      
      return $column;
    } // typeStringToColumn
    
    /**
     * Return model definition code basde on this table
     *
     * @return string
     */
    function prepareModelDefinition() {
      $table_name = str_starts_with($this->getName(), TABLE_PREFIX) ? substr($this->getName(), strlen(TABLE_PREFIX)) : $this->getName();
      
      $result = "DB::createTable('$table_name')";
      
      $id_added = false; // Indicator whether ID column is added
      $type_added = false; // Indicator whether type column is added
      
      if(is_foreachable($this->columns)) {
        $result .= "->addColumns(array(\n";
        foreach($this->columns as $column) {
          $result .= '  ' . $column->prepareModelDefinition() . ", \n";
          
          if($column->getName() == 'id' && $column instanceof DBIntegerColumn && $column->getAutoIncrement()) {
            $id_added = true;
          } // if
          
          if($column->getName() == 'type' && $column instanceof DBStringColumn) {
            $type_added = true;
          } // if
        } // foreach
        $result .= '))';
      } // if
      
      if(is_foreachable($this->indices)) {
        $indices = array();
        
        foreach($this->indices as $index) {
          if(($index instanceof DBIndexPrimary || $index->getType() == DBIndex::PRIMARY) && $id_added) {
            continue; // Skip primary key if we have it added via DBIdColumn
          } // if
          
          if($type_added && $index->getName() == 'type') {
            continue;
          } // if
          
          $indices[] = '  ' . $index->prepareModelDefinition() . ", \n";
        } // foreach
        
        if(count($indices)) {
          $result .= "->addIndices(array(\n" . implode('', $indices) . '))';
        } // if
      } // if
      
      return $result;
    } // prepareModelDefinition
    
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
     * @return DBTable
     */
    function &setName($value) {
      $this->name = $value;
      
      return $this;
    } // setName
    
  }