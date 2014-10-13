<?php

  /**
   * Angie model migration
   *
   * @package angie.library.application
   * @subpackage model
   */
  abstract class AngieModelMigration {

    /**
     * List of steps that need to be executed before we can execute this migration
     *
     * @var array
     */
    private $execute_after = array();

    /**
     * Return array of migrations that need to be executed before we can execute this migration
     *
     * @return array|null
     */
    function getExecuteAfter() {
      return count($this->execute_after) ? $this->execute_after : null;
    } // getExecuteAfter

    /**
     * Make sure that this migration is executed after given list of migrations
     *
     * @param array|string $migration_names
     */
    function executeAfter($migration_names) {
      if($migration_names) {
        $migration_names = (array) $migration_names;

        foreach($migration_names as $migration_name) {
          $this->execute_after[] = $migration_name;
        } // foreach

        if(count($this->execute_after) > 1) {
          $this->execute_after = array_unique($this->execute_after);
        } // if
      } // if
    } // executeAfter

    /**
     * Upgrade the data
     */
    abstract function up();

    /**
     * Downgrade the data
     */
    function down() {
      // Some migrations are a one way street
    } // down

    // ---------------------------------------------------
    //  Misc
    // ---------------------------------------------------

    /**
     * Return migration description
     *
     * @return string
     */
    function getDescription() {
      return Inflector::humanize(Inflector::underscore(get_class($this)));
    } // getDescription

    /**
     * Cached changeset name
     *
     * @var bool
     */
    private $changeset = false;

    /**
     * Return migration's changeset name
     *
     * @return string
     */
    function getChangeset() {
      if($this->changeset === false) {
        $reflection = new ReflectionClass($this);

        $this->changeset = basename(dirname($reflection->getFileName()));
      } // if

      return $this->changeset;
    } // getChangeset

    // ---------------------------------------------------
    //  Table locking / unlocking
    // ---------------------------------------------------

    /**
     * When migration uses a table, create a copy to work with
     *
     * @var bool
     */
    private $copy_used_tables = false; // Don't copy used tables by default

    /**
     * Return whether system should work on table copies or original tables
     *
     * @return boolean
     */
    function getCopyUsedTables() {
      return (boolean) $this->copy_used_tables;
    } // getCopyUsedTables

    /**
     * Set whether system should work on table copies or original tables
     *
     * @param boolean $value
     */
    function setCopyUsedTables($value) {
      $this->copy_used_tables = (boolean) $value;
    } // setCopyUsedTables

    /**
     * Cached list of used tables
     *
     * @var array
     */
    private $used_tables = array();

    /**
     * Return list of used tables
     *
     * @return array
     */
    function getUsedTables() {
      return $this->used_tables;
    } // getUsedTables

    /**
     * Mark table as used and return it's full name (with table prefix)
     *
     * @param string $name_without_prefix
     * @return string
     */
    function useTable($name_without_prefix) {
      if(empty($this->used_tables[$name_without_prefix])) {
        if($this->copy_used_tables) {
          $original_table_name = TABLE_PREFIX . $name_without_prefix;
          $new_table_name = substr(TABLE_PREFIX . $name_without_prefix . '_' . Inflector::underscore(get_class($this)), 0, 64);

          if(DB::getConnection()->tableExists($new_table_name)) {
            $this->execute("DROP TABLE `$new_table_name`");
          } // if

          $this->execute("CREATE TABLE `$new_table_name` LIKE `$original_table_name`");
          $this->execute("INSERT INTO `$new_table_name` SELECT * FROM `$original_table_name`");

          $this->used_tables[$name_without_prefix] = $new_table_name;
        } else {
          $this->used_tables[$name_without_prefix] = TABLE_PREFIX . $name_without_prefix;
        } // if
      } // if

      return $this->used_tables[$name_without_prefix];
    } // useTable

    /**
     * Use table and return instance of DBTable that we can transform
     *
     * @param string $name_without_prefix
     * @return DBTable
     */
    function useTableForAlter($name_without_prefix) {
      return DB::loadTable($this->useTable($name_without_prefix));
    } // useTableForAlter

    /**
     * Use multiple tables
     *
     * @return array
     * @throws InvalidParamError
     */
    function useTables() {
      $table_names = func_get_args();

      if($table_names) {
        $used_tables = array();

        foreach($table_names as $table_name) {
          $used_tables[] = $this->useTable($table_name);
        } // foreach

        return $used_tables;
      } else {
        throw new InvalidParamError('table_names', $table_names, 'One or more table names expected');
      } // if
    } // useTables

    /**
     * Set listed tables as no longer in use
     */
    function doneUsingTables() {
      if($this->copy_used_tables) {
        foreach($this->used_tables as $original_without_prefix => $copy) {
          $original = TABLE_PREFIX . $original_without_prefix;

          do {
            $temp = substr($original . '_' . make_string(), 0, 64);
          } while(DB::getConnection()->tableExists($temp));

          $this->execute("RENAME TABLE $original TO $temp, $copy TO $original");
          $this->execute("DROP TABLE $temp");

          unset($this->used_tables[$original_without_prefix]);
        } // foreach
      } // if
    } // doneUsingTables

    /**
     * Clean up used table copies
     */
    function cleanUpUsedTableCopies() {
      foreach($this->used_tables as $original_without_prefix => $copy) {
        $this->execute("DROP TABLE $copy");
        unset($this->used_tables[$original_without_prefix]);
      } // if
    } // cleanUpUsedTableCopies

    // ---------------------------------------------------
    //  Operations
    // ---------------------------------------------------

    /**
     * Execute sql
     *
     * @param mixed
     * @return DbResult
     * @throws InvalidParamError
     * @throws DBQueryError
     */
    function execute() {
      $arguments = func_get_args();

      if(empty($arguments)) {
        throw new InvalidParamError('arguments', $arguments, 'DB::execute() function requires at least SQL query to be provided');
      } else {
        return DB::getConnection()->execute(array_shift($arguments), $arguments);
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
    function executeFirstRow() {
      $arguments = func_get_args();

      if(empty($arguments)) {
        throw new InvalidParamError('arguments', $arguments, 'DB::executeFirstRow() function requires at least SQL query to be provided');
      } else {
        return DB::getConnection()->executeFirstRow(array_shift($arguments), $arguments);
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
    function executeFirstColumn() {
      $arguments = func_get_args();

      if(empty($arguments)) {
        throw new InvalidParamError('arguments', $arguments, 'DB::executeFirstColumn() function requires at least SQL query to be provided');
      } else {
        return DB::getConnection()->executeFirstColumn(array_shift($arguments), $arguments);
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
    function executeFirstCell() {
      $arguments = func_get_args();

      if(empty($arguments)) {
        throw new InvalidParamError('arguments', $arguments, 'DB::executeFirstCell() function requires at least SQL query to be provided');
      } else {
        return DB::getConnection()->executeFirstCell(array_shift($arguments), $arguments);
      } // if
    } // executeFirstCell

    /**
     * Execute a transaction
     *
     * @param Closure $callback
     * @param string $operation
     */
    function transact($callback, $operation = null) {
      DB::transact($callback, $operation);
    } // transact

    /**
     * Return number of affected rows
     *
     * @return integer
     */
    function affectedRows() {
      return DB::getConnection()->affectedRows();
    } // affectedRows

    /**
     * Return last insert ID
     *
     * @return integer
     */
    function lastInsertId() {
      return DB::getConnection()->lastInsertId();
    } // lastInsertId

    /**
     * Load table instance
     *
     * @param string $name
     * @return DBTable
     */
    function loadTable($name) {
      return DB::loadTable(TABLE_PREFIX . $name);
    } // loadTable

    /**
     * Upgrade the table
     *
     * $table can be a table instance. If it is not a table instance, than it should be a table name and
     * $colums and $indexes parameters are used
     *
     * @param string|DBTable $table
     * @param array|null $columns
     * @param array|null $indices
     * @throws InvalidParamError
     */
    function createTable($table, $columns = null, $indices = null) {
      if(!$this->getDryRun()) {
        if($table instanceof DBTable) {
          $table->save(TABLE_PREFIX);
        } elseif(is_string($table)) {
          $create_table = DB::createTable($table);

          $create_table->addColumns($columns);

          if($indices) {
            $create_table->addIndices($indices);
          } // if

          $create_table->save(TABLE_PREFIX);
        } else {
          throw new InvalidParamError('table', $table, 'Table is expected to be a table name or a DBTable instance');
        } // if
      } // if
    } // createTable

    /**
     * Rename table
     *
     * @param string $table_name
     * @param string $new_table_name
     */
    function renameTable($table_name, $new_table_name) {
      if(!$this->getDryRun()) {
        DB::execute("RENAME TABLE " . TABLE_PREFIX . "{$table_name} TO " . TABLE_PREFIX . "{$new_table_name}");
      } // if
    } // renameTable

    /**
     * Drop table
     *
     * @param string $name
     * @throws NotImplementedError
     */
    function dropTable($name) {
      if(!$this->getDryRun()) {
        DB::execute('DROP TABLE IF EXISTS ' . DB::escapeTableName(TABLE_PREFIX . $name));
      } // if
    } // dropTable

    // ---------------------------------------------------
    //  Config Options Management
    // ---------------------------------------------------

    /**
     * Return config option value
     *
     * @param string $name
     * @return mixed|null
     */
    function getConfigOptionValue($name) {
      $value = $this->executeFirstCell('SELECT value FROM ' . TABLE_PREFIX . 'config_options WHERE name = ?', $name);

      return $value ? unserialize($value) : null;
    } // getConfigOptionValue

    /**
     * Update configuration option
     *
     * @param string $name
     * @param mixed $value
     */
    function setConfigOptionValue($name, $value = null) {
      $config_options_table = TABLE_PREFIX . 'config_options';

      if($this->executeFirstCell("SELECT COUNT(name) FROM $config_options_table WHERE name = ?", $name)) {
        $this->execute("UPDATE $config_options_table SET value = ? WHERE name = ?", serialize($value), $name);
      } else {
        $this->addConfigOption($name, $value);
      } // if

      AngieApplication::cache()->remove('config_options');
    } // setConfigOptionValue

    /**
     * Add a new configuration option value
     *
     * @param string $name
     * @param mixed $value
     * @param string $module
     */
    function addConfigOption($name, $value = null, $module = 'system') {
      $config_options_table = TABLE_PREFIX . 'config_options';

      if($this->executeFirstCell("SELECT COUNT(name) FROM $config_options_table WHERE name = ?", $name)) {
        $this->execute("UPDATE $config_options_table SET value = ? WHERE name = ?", serialize($value), $name);
      } else {
        $this->execute("INSERT INTO $config_options_table (name, module, value) VALUES (?, ?, ?)", $name, $module, serialize($value));
      } // if

      AngieApplication::cache()->remove('config_options');
    } // addConfigOption

    /**
     * Remove configuration option from the system
     *
     * @param string $name
     */
    function removeConfigOption($name) {
      $this->transact(function() use ($name) {
        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'config_option_values WHERE name = ?', $name);
        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'config_options WHERE name = ?', $name);
      });
      AngieApplication::cache()->remove('config_options');
    } // removeConfigOption

    /**
     * Schedule index rebuild
     *
     * @param boolean $drop_search_indexes
     */
    function scheduleIndexesRebuild($drop_search_indexes = false) {
      $this->setConfigOptionValue('require_index_rebuild', true);

      if($drop_search_indexes) {
        foreach(DB::listTables(TABLE_PREFIX) as $table_name) {
          if(str_starts_with($table_name, TABLE_PREFIX . 'search_index_for_')) {
            DB::execute('DROP TABLE IF EXISTS ' . DB::escapeTableName($table_name));
          } // if
        } // foreach
      } // if
    } // scheduleIndexesRebuild

    // ---------------------------------------------------
    //  Output and mode
    // ---------------------------------------------------

    /**
     * Output handler
     *
     * @var Output
     */
    private $output;

    /**
     * Return output handler
     *
     * @return Output
     */
    function &getOutput() {
      return $this->output;
    } // getOutput

    /**
     * Set output handler
     *
     * @param Output $output
     */
    function setOutput(Output $output) {
      $this->output = $output;
    } // setOutput

    /**
     * Cached dry run value
     *
     * @var bool
     */
    private $dry_run = false;

    /**
     * Return true if migration is running in dry run (test) mode
     *
     * @return bool
     */
    function getDryRun() {
      return $this->dry_run;
    } // getDryRun

    /**
     * Set if migration should run in dry run (test) mode
     *
     * @param bool $value
     */
    function setDryRun($value) {
      $this->dry_run = (boolean) $value;
    } // setDryRun

    // ---------------------------------------------------
    //  Execution log
    // ---------------------------------------------------

    /**
     * Set this migration as executed
     */
    function setAsExecuted() {
      $changeset = $this->getChangeset();

      $changeset_timestamp = $this->getChangesetTimestamp($changeset);
      $changeset_name = substr($changeset, 11);

      DB::execute('REPLACE INTO ' . TABLE_PREFIX . 'executed_model_migrations (migration, changeset_timestamp, changeset_name, executed_on) VALUES (?, ?, ?, UTC_TIMESTAMP())', get_class($this), $changeset_timestamp, $changeset_name);
    } // setAsExecuted

    /**
     * Return time stamp from a given change-set name
     *
     * @param string $name
     * @return string|false
     */
    private function getChangesetTimestamp($name) {
      $matches = array();

      if(preg_match('/^(\d{4})-(\d{2})-(\d{2})-(.*)$/', $name, $matches)) {
        return "$matches[1]-$matches[2]-$matches[3]";
      } // if

      return false;
    } // getChangesetTimestamp

    /**
     * Set this migration as not executed
     */
    function setAsNotExecuted() {
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'executed_model_migrations WHERE migration = ?', get_class($this));
    } // setAsNotExecuted

    /**
     * Returns true if this migration has been executed
     *
     * @return boolean
     */
    function isExecuted() {
      return (boolean) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'executed_model_migrations WHERE migration = ?', get_class($this));
    } // isExecuted

  }