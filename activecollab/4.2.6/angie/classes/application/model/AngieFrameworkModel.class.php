<?php

  /**
   * Angie framework model implementation
   *
   * @package angie.library.application
   */
  abstract class AngieFrameworkModel {
    
    /**
     * Parent framework or module
     *
     * @var AngieFramework
     */
    protected $parent;
    
    /**
     * List of tables used by this framework
     *
     * @var DBTable[]
     */
    protected $tables = array();
    
    /**
     * Array of model builders, indexed by table name
     *
     * @var array
     */
    protected $model_builders = array();
    
    /**
     * Construct framework or module model instance
     *
     * @param AngieFramework $parent
     * @throws InvalidInstanceError
     */
    function __construct(AngieFramework $parent) {
      if($parent instanceof AngieFramework) {
        $this->parent = $parent;
      } else {
        throw new InvalidInstanceError('parent', $parent, 'AngieFramework');
      } // if
    } // __construct
    
    /**
     * Add table to the list of tables used by this framework or model
     *
     * @param DBTable $table
     * @return DBTable
     */
    function &addTable(DBTable $table) {
      $this->tables[$table->getName()] = $table;
      
      return $this->tables[$table->getName()];
    } // addTable

    /**
     * Add table that is loaded from a definition AMQPChannel
     *
     * @param string $table_name
     * @return DBTable
     */
    function &addTableFromFile($table_name) {
      return $this->addTable($this->loadTableDefinion($table_name));
    } // addTableFromFile
    
    /**
     * Add model
     *
     * @param DBTable $table
     * @return AngieFrameworkModelBuilder
     */
    function &addModel(DBTable $table) {
      $this->tables[$table->getName()] = $table;
      
      $this->model_builders[$table->getName()] = new AngieFrameworkModelBuilder($this, $table);
      return $this->model_builders[$table->getName()];
    } // addModel

    /**
     * Add model from a file
     *
     * @param string $table_name
     * @return AngieFrameworkModelBuilder
     */
    function &addModelFromFile($table_name) {
      return $this->addModel($this->loadTableDefinion($table_name));
    } // addModelFromFile
    
    /**
     * Return all tables defined by this model
     *
     * @return array
     */
    function getTables() {
      return $this->tables;
    } // getTables
    
    /**
     * Return single table
     *
     * @param string $name
     * @return DBTable
     * @throws InvalidParamError
     */
    function getTable($name) {
      if(isset($this->tables[$name])) {
        return $this->tables[$name];
      } else {
        throw new InvalidParamError('name',$name, "Table '$name' is not defined in this model");
      } // if
    } // getTable

    /**
     * Return parent module or framework
     *
     * @return AngieFramework
     */
    function getParent() {
      return $this->parent;
    } // getParent
    
    /**
     * Return all model builders defined by this model
     *
     * @return array
     */
    function getModelBuilders() {
      return $this->model_builders;
    } // getModelBuilders
    
    /**
     * Return specific model builder
     *
     * @param string $for_table_name
     * @return AngieFrameworkModelBuilder
     * @throws InvalidParamError
     */
    function getModelBuilder($for_table_name) {
      if(isset($this->model_builders[$for_table_name])) {
        return $this->model_builders[$for_table_name];
      } else {
        throw new InvalidParamError('for_table_name', $for_table_name, "Model builder is not defined for '$for_table_name' table in this model");
      } // if
    } // getModelBuilder

    /**
     * Load table from a file file
     *
     * @param string $table_name
     * @return DBTable
     * @throws FileDnxError
     */
    function loadTableDefinion($table_name) {
      $class = new ReflectionClass($this);

      $table_file = dirname($class->getFileName()) . "/table.{$table_name}.php";

      if(is_file($table_file)) {
        return require $table_file;
      } else {
        throw new FileDnxError($table_file, "Table '$table_name' definition was not found");
      } // if
    } // loadTableDefinion
    
    // ---------------------------------------------------
    //  Install and initialize
    // ---------------------------------------------------
    
    /**
     * Create framework tables
     */
    function createTables() {
      foreach($this->tables as &$table) {
        $table->save(TABLE_PREFIX);
      } // foreach
    } // createTables
    
    /**
     * Enter description here...
     */
    function dropTables() {
      foreach($this->tables as &$table) {
        DB::execute('DROP TABLE IF EXISTS ' . TABLE_PREFIX . $table->getName());
      } // foreach
    } // dropTables
    
    /**
     * Load initial framework data
     *
     * @param string $environment
     */
    function loadInitialData($environment = null) {
      if($environment && is_valid_function_name($environment)) {
        if($this->parent instanceof AngieFramework) {
          $file = $this->parent->getPath() . '/resources/initial_data_for_' . $environment . '.php';
        
          if(is_file($file)) {
            require $file;
          } // if
        } // if
      } // if
    } // loadInitialData
    
    /**
     * Load data to table
     *
     * @param string $table
     * @param array $rows
     * @throws Exception
     */
    function loadTableData($table, $rows) {
      try {
        $table_name = TABLE_PREFIX . $table;
        
        DB::beginWork("Loading table data for '" . $this->parent->getName() . "' @ " . __CLASS__);
        foreach($rows as $row) {
          DB::execute("INSERT INTO $table_name (" . implode(', ', array_keys($row)) . ") VALUES (?)", $row);
        } // foreach
        DB::commit("Table data loaded for '" . $this->parent->getName() . "' @ " . __CLASS__);
      } catch(Exception $e) {
        DB::rollback("Failed to load table data for '" . $this->parent->getName() . "' @ " . __CLASS__);
        throw $e;
      } // try
    } // loadTableData
    
    // ---------------------------------------------------
    //  Helper options
    // ---------------------------------------------------
    
    /**
     * Create new configuration option
     *
     * @param string $name
     * @param mixed $default
     */
    protected function addConfigOption($name, $default = null) {
      DB::execute('INSERT INTO ' . TABLE_PREFIX . 'config_options (name, module, value) VALUES (?, ?, ?)', $name, $this->parent->getName(), ($default === null ? null : serialize($default)));
      AngieApplication::cache()->remove("config_options");
    } // addConfigOption
    
    /**
     * Get value of a given config option
     * 
     * @param string $name
     * @return mixed
     * @throws ConfigOptionDnxError
     */
    protected function getConfigOptionValue($name) {
			$config_options_table = TABLE_PREFIX . 'config_options';
			
			$result = DB::executeFirstRow("SELECT value FROM $config_options_table WHERE name = ?", $name);
			if (!$result) {
				throw new ConfigOptionDnxError($name);
			} // if
			
			return $result['value'] ? unserialize($result['value']) : null;
    } // getConfigOptionValue
    
    /**
     * Set value of a given config option
     * 
     * @param string $name
     * @param mixed $value
     */
    protected function setConfigOptionValue($name, $value = null) {
      $config_options_table = TABLE_PREFIX . 'config_options';
      
      if(DB::executeFirstCell("SELECT COUNT(name) FROM $config_options_table WHERE name = ?", $name) > 0) {
        DB::execute("UPDATE $config_options_table SET value = ? WHERE name = ?", serialize($value), $name);
        AngieApplication::cache()->remove("config_options");
      } else {
        $this->addConfigOption($name, $value);
      } // if
    } // setConfigOptionValue

    /**
     * Create a user and return user ID
     *
     * @param string $email
     * @param array $additional
     * @return integer
     */
    protected function addUser($email, $additional = null) {
      $properties = array(
        'state' => 3, // STATE_VISIBLE
        'email' => $email,
      );

      if(is_array($additional)) {
        $properties = array_merge($properties, $additional);
      } // if

      if(isset($properties['password'])) {
        $properties['password'] = base64_encode(pbkdf2($properties['password'], APPLICATION_UNIQUE_KEY, 1000, 40));
      } else {
        $properties['password'] = base64_encode(pbkdf2('test', APPLICATION_UNIQUE_KEY, 1000, 40));
      } // if

      $properties['password_hashed_with'] = 'pbkdf2';

      $properties['created_on'] = date(DATETIME_MYSQL);
      if(!isset($properties['created_by_id'])) {
        $properties['created_by_id'] = 1;
      } // if

      return $this->createObject('users', $properties);
    } // addUser

    /**
     * Create a new object in a given table, with given properties
     *
     * This function is specific because it creates proper records in search
     * index, modification log etc
     *
     * @param string $table
     * @param array $properties
     * @return integer
     */
    protected function createObject($table, $properties) {
      $to_insert = array();
      foreach($properties as $k => $v) {
        $to_insert[DB::escapeFieldName($k)] = DB::escape($v);
      } // foreach

      DB::execute('INSERT INTO ' . DB::escapeTableName(TABLE_PREFIX . $table) . ' (' . implode(', ', array_keys($to_insert)) . ') VALUES (' . implode(', ', $to_insert) . ')');

      return DB::lastInsertId();
    } // createObject

    /**
     * Register custom fields for type
     *
     * @param string $type
     * @param integer $num
     */
    protected function registerCustomFieldsForType($type, $num = 3) {
      for($i = 1; $i <= $num; $i++) {
        DB::execute('INSERT INTO ' . TABLE_PREFIX . 'custom_fields (field_name, parent_type) VALUES (?, ?)', "custom_field_$i", $type);
      } // for
    } // registerCustomFieldsForType
    
    // ---------------------------------------------------
    //  Upgrade
    // ---------------------------------------------------
    
    /**
     * Return list of steps that need to be executed for this framework to or 
     * module to be updated to the latest version
     *
     * @param array
     */
    function getUpgradeSteps() {
      
    } // getUpgradeSteps
    
    /**
     * Execute specified upgrade step
     * 
     * This function validates step name before executing it
     *
     * @param string $step_name
     * @throws InvalidParamError
     */
    function executeUpgradeStep($step_name) {
      if(preg_match('/^v([0-9]*)_(.*)$/', $step_name) && method_exists($this, $step_name)) {
        $this->$step_name();
      } else {
        throw new InvalidParamError('step_name', $step_name, "'$step_name' is not a valid upgrade function");
      } // if
    } // executeUpgradeStep
    
    // ---------------------------------------------------
    //  Utility
    // ---------------------------------------------------
    
    /**
     * Returns true if current framework or module version is smaller than 
     * $version
     *
     * @param string $version
     * @return boolean
     */
    protected function currentVersionSmallerThan($version) {
      return version_compare($this->parent->getVersion(), $version) == -1;
    } // currentVersionSmallerThan
    
  }