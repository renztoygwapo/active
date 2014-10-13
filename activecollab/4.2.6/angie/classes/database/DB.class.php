<?php

  /**
   * Database interface
   *
   * @package angie.library.database
   */
  final class DB {
    
    /**
     * Load mode
     *
     * LOAD_ALL_ROWS - Load all rows
     * LOAD_FIRST_ROW - Limit result set to first row and load it
     * LOAD_FIRST_COLUMN - Return content of first column
     * LOAD_FIRST_CELL - Load only first cell of first row
     */
    const LOAD_ALL_ROWS = 0;
    const LOAD_FIRST_ROW = 1;
    const LOAD_FIRST_COLUMN = 2;
    const LOAD_FIRST_CELL = 3;
    
    /**
     * Return method for DB results
     * 
     * RETURN_ARRAY - Return fields as associative array
     * RETURN_OBJECT_BY_CLASS - Create new object instance and hydrate it
     * RETURN_OBJECT_BY_FIELD - Read class from record field, create instance 
     *   and hydrate it
     */
    const RETURN_ARRAY = 0;
    const RETURN_OBJECT_BY_CLASS = 1;
    const RETURN_OBJECT_BY_FIELD = 2;
    
    /**
     * Array of open connections
     * 
     * Default connection is available at key 'default'
     *
     * @var array
     */
    private static $connections = array();
    
    /**
     * Return connection instance by name
     * 
     * If $name is not provided, default DB connection will be used
     *
     * @param string $name
     * @return DBConnection
     * @throws InvalidParamError
     */
    public static function getConnection($name = 'default') {
      if(isset(self::$connections[$name])) {
        return self::$connections[$name];
      } // if
    } // getConnection
    
    /**
     * Set connection
     *
     * @param string $name
     * @param DBConnection $connection
     * @throws InvalidParamError
     */
    public static function setConnection($name, DBConnection $connection) {
      if($connection->isConnected()) {
        self::$connections[$name] = $connection;
      } else {
        throw new InvalidParamError('connection', $connection, 'Connection needs to be open');
      } // if
    } // setConnection
    
    // Interface methods
    
    /**
     * Execute sql
     *
     * @param mixed
     * @return DbResult
     * @throws InvalidParamError
     * @throws DBQueryError
     */
    public static function execute() {
      $arguments = func_get_args();
      if(empty($arguments)) {
        throw new InvalidParamError('arguments', $arguments, 'DB::execute() function requires at least SQL query to be provided');
      } else {
        return self::$connections['default']->execute(array_shift($arguments), $arguments);
      } // if
    } // execute
    
    /**
     * Execute query and return first row. If there is no first row NULL is returned
     *
     * @param mixed
     * @return array
     * @throws InvalidParamError
     * @throws DBQueryError
     */
    public static function executeFirstRow() {
      $arguments = func_get_args();
      if(empty($arguments)) {
        throw new InvalidParamError('arguments', $arguments, 'DB::executeFirstRow() function requires at least SQL query to be provided');
      } else {
        return self::$connections['default']->executeFirstRow(array_shift($arguments), $arguments);
      } // if
    } // executeFirstRow
    
    /**
     * Execute SQL query and return content of first column as an array
     *
     * @param mixed
     * @return array
     * @throws InvalidParamError
     * @throws DBQueryError
     */
    public static function executeFirstColumn() {
      $arguments = func_get_args();
      if(empty($arguments)) {
        throw new InvalidParamError('arguments', $arguments, 'DB::executeFirstColumn() function requires at least SQL query to be provided');
      } else {
        return self::$connections['default']->executeFirstColumn(array_shift($arguments), $arguments);
      } // if
    } // executeFirstColumn
    
    /**
     * Return value from the first cell
     *
     * @param mixed
     * @return mixed
     * @throws InvalidParamError
     * @throws DBQueryError
     */
    public static function executeFirstCell() {
      $arguments = func_get_args();
      if(empty($arguments)) {
        throw new InvalidParamError('arguments', $arguments, 'DB::executeFirstCell() function requires at least SQL query to be provided');
      } else {
        return self::$connections['default']->executeFirstCell(array_shift($arguments), $arguments);
      } // if
    } // executeFirstCell
    
    /**
     * Return number of affected rows
     *
     * @return integer
     */
    public static function affectedRows() {
      return self::$connections['default']->affectedRows();
    } // affectedRows
    
    /**
     * Return last insert ID
     *
     * @param void
     * @return integer
     */
    public static function lastInsertId() {
      return self::$connections['default']->lastInsertId();
    } // lastInsertId

    /**
     * Run within a transation
     *
     * @param Closure $callback
     * @param string $operation
     * @throws Exception
     * @throws InvalidInstanceError
     */
    public static function transact(Closure $callback, $operation = null) {
      if($callback instanceof Closure) {
        try {
          DB::beginWork("Begin Work: $operation");

          $callback();

          DB::commit("Commit: $operation");
        } catch(Exception $e) {
          DB::rollback("Rollbacl: $operation");
          throw $e;
        } // try
      } else {
        throw new InvalidInstanceError('callback', $callback, 'Closure');
      } // if
    } // transact
    
    /**
     * Begin transaction
     *
     * @param string $message
     * @return boolean
     */
    public static function beginWork($message = null) {
      return self::$connections['default']->beginWork($message);
    } // beginWork
    
    /**
     * Commit transaction
     *
     * @param string $message
     * @return boolean
     */
    public static function commit($message = null) {
      return self::$connections['default']->commit($message);
    } // commit
    
    /**
     * Rollback transaction
     *
     * @param string $message
     * @return boolean
     */
    public static function rollback($message = null) {
      return self::$connections['default']->rollback($message);
    } // rollback
    
    /**
     * Return true if system is in transaction
     *
     * @return boolean
     */
    public static function inTransaction() {
      return self::$connections['default']->inTransaction();
    } // inTransaction
    
    /**
     * Prepare a batch insert instance
     * 
     * @param string $table_name
     * @param array $fields
     * @param integer $rows_per_batch
     * @return DBBatchInsert
     */
    public static function batchInsert($table_name, $fields, $rows_per_batch = 50) {
      return new DBBatchInsert($table_name, $fields, $rows_per_batch);
    } // batchInsert
    
    /**
     * Prepare SQL with given arguments
     *
     * @throws InvalidParamError
     * @return string
     */
    public static function prepare() {
      $arguments = func_get_args();
      if(empty($arguments)) {
        throw new InvalidParamError('arguments', $arguments, 'DB::prepare() function requires at least SQL query to be provided');
      } else {
        return self::$connections['default']->prepare(array_shift($arguments), $arguments);
      } // if
    } // prepare
    
    /**
     * Prepare conditions
     *
     * @param mixed $conditions
     * @return string
     */
    public static function prepareConditions($conditions) {
      return is_array($conditions) ? DB::getConnection()->prepare(array_shift($conditions), $conditions) : $conditions;
    } // prepareConditions
    
    /**
     * Escape $unescaped value
     *
     * @param mixed $unescaped
     * @return string
     */
    public static function escape($unescaped) {
      return self::$connections['default']->escape($unescaped);
    } // escape
    
    /**
     * Escape field name
     *
     * @param mixed $unescaped
     * @return string
     */
    public static function escapeFieldName($unescaped) {
      return self::$connections['default']->escapeFieldName($unescaped);
    } // escapeFieldName
    
    /**
     * Escape table name
     *
     * @param mixed $unescaped
     * @return string
     */
    public static function escapeTableName($unescaped) {
      return self::$connections['default']->escapeTableName($unescaped);
    } // escapeTableName
    
    /**
     * Return number of queries that are executed
     * 
     * @return integer
     */
    public static function getQueryCount() {
      return self::$connections['default']->getQueryCount();
    } // getQueryCount
    
    // ---------------------------------------------------
    //  Engineer
    // ---------------------------------------------------
    
    /**
     * Return true if table $name exists
     * 
     * @param string $name
     * @return boolean
     */
    public static function tableExists($name) {
      return self::$connections['default']->tableExists($name);
    } // tableExists
    
    /**
     * Create a new database table
     *
     * @param string $name
     * @return DBTable
     */
    public static function createTable($name) {
      return self::$connections['default']->createTable($name);
    } // createTable
    
    /**
     * Load table details
     *
     * @param string $name
     * @return DBTable
     */
    public static function loadTable($name) {
      return self::$connections['default']->loadTable($name);
    } // loadTable
    
    /**
     * Return array of tables from the database
     *
     * @param string $prefix
     * @return array
     */
    public static function listTables($prefix = null) {
      return self::$connections['default']->listTables($prefix);
    } // listTables
    
    /**
     * Return list of fields from given table
     *
     * @param string $table_name
     * @return array
     */
    public static function listTableFields($table_name) {
      return self::$connections['default']->listTableFields($table_name);
    } // listTableFields
    
    /**
     * Drop specific table
     * 
     * @param string $table_name
     */
    public static function dropTable($table_name) {
      self::dropTables(array($table_name));
    } // dropTable
    
    /**
     * Drop one or more tables
     *
     * @param array $tables
     * @param string $prefix
     */
    public static function dropTables($tables, $prefix = '') {
      self::$connections['default']->dropTables($tables, $prefix);
    } // dropTables

    /**
     * List indexes for a given table name
     *
     * @param string $table_name
     * @param array
     */
    public static function listTableIndexes($table_name) {
      return self::$connections['default']->listTableIndexes($table_name);
    } // listTableIndexes
    
    /**
     * Drop all tables from database
     *
     * @return boolean
     */
    public static function clearDatabase() {
      return self::$connections['default']->clearDatabase();
    } // clearDatabase

    /**
     * Gets maximum packet size allowed to be inserted into database
     *
     * @return int
     */
    public static function getMaxPacketSize() {
      return self::$connections['default']->getMaxPacketSize();
    }
    
    // ---------------------------------------------------
    //  File export / import
    // ---------------------------------------------------
    
    /**
     * Do a database dump of specified tables
     * 
     * If $table_name is empty it will dump all tables in current database
     *
     * @param array $tables
     * @param string $destination_file
     * @param boolean $dump_structure
     * @param boolean $dump_data
     */
    static function exportToFile($tables, $output_file, $dump_structure = true, $dump_data = true) {
      self::$connections['default']->exportToFile($tables, $output_file, $dump_structure, $dump_data);
    } // exportToFile
    
    /**
     * Import sql file into database
     *
     * @param string $sql_file
     * @param string $database
     */
    static function importFromFile($sql_file, $database = null) {
      self::$connections['default']->importFromFile($sql_file, $database);
    } // importFromFile

    // ---------------------------------------------------
    //  Track
    // ---------------------------------------------------

    /**
     * All quries that are captured by this class (captured only when system is in debelopment mode)
     *
     * @var array
     */
    private static $all_queries = array();

    /**
     * Trapped queries
     *
     * @var array
     */
    private static $trapped_queries = false;

    /**
     * Log query
     *
     * @param $sql
     */
    static function logQuery($sql, $execution_time = 0) {
      if(AngieApplication::isInDevelopment()) {
        self::$all_queries[] = array($sql, $execution_time);
      } // if

      if(self::$trapped_queries !== false) {
        self::$trapped_queries[] = $sql;
      } // if
    } // logQuery

    /**
     * Return all queries that DB layer logged
     *
     * @return array
     * @throws Error
     */
    static function getAllQueries() {
      if(AngieApplication::isInDevelopment()) {
        return self::$all_queries;
      } else {
        throw new Error('This method is available only when application is in development mode');
      } // if
    } // getAllQueries

    /**
     * Return trapped queries, if any
     *
     * @return array|bool
     */
    static function getTrappedQueries() {
      return self::$trapped_queries;
    } // getTrappedQueries

    /**
     * Prepare query trap
     */
    static function setUpTrap() {
      if(is_array(self::$trapped_queries)) {
        throw new Error('Query trap already set');
      } else {
        self::$trapped_queries = array();
      } // if
    } // setUpTrap

    /**
     * Tear down trap
     */
    static function tearDownTrap() {
      self::$trapped_queries = false;
    } // tearDownTrap
  }