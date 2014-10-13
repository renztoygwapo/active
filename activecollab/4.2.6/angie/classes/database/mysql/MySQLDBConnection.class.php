<?php

  /**
   * Database connection
   *
   * @package angie.library.database
   * @subpackage mysql
   */
  class MySQLDBConnection extends DBConnection {
    
    /**
     * MySQL server hostname
     *
     * @var string
     */
    private $host;
    
    /**
     * Username that's used to connect to the server
     *
     * @var string
     */
    private $user;
    
    /**
     * Password that's used to connect to the server
     *
     * @var string
     */
    private $pass;
    
    /**
     * Name of the database that's used
     *
     * @var string
     */
    private $db_name;
    
    /**
     * Use persistant connections, or not
     *
     * @var boolean
     */
    private $persist;
    
    /**
     * Connection character set
     *
     * @var string
     */
    private $charset;
    
    /**
     * Database link
     *
     * @var resource
     */
    private $link;
    
    /**
     * Transaction level
     *
     * @var integer
     */
    private $transaction_level = 0;
    
    /**
     * Construct MySQLDBConnection instance
     *
     * @param string $host
     * @param string $user
     * @param string $pass
     * @param string $database
     * @param boolean $persist
     * @param string $charset
     */
    function __construct($host, $user, $pass, $database, $persist = false, $charset = null) {
      $this->connect(array(
        'host' => $host, 
        'user' => $user, 
        'pass' => $pass, 
        'db_name' => $database, 
        'persist' => $persist, 
        'charset' => $charset
      ));
    } // __construct
    
    /**
     * Connect to database
     *
     * @param array $parameters
     * @return boolean
     * @throws DBConnectError
     */
    function connect($parameters) {
      $host = $parameters['host'];
      $user = $parameters['user'];
      $pass = $parameters['pass'];
      $database = $parameters['db_name'];
      $persist = $parameters['persist'];
      $charset = $parameters['charset'];
      
      $this->link = $persist ? mysql_pconnect($host, $user, $pass) : mysql_connect($host, $user, $pass);
      
      if(is_resource($this->link)) {
        if(mysql_select_db($database, $this->link)) {
          if($charset && DB_FORCE_CHARSET) {
            $this->execute('SET NAMES ?', array($charset));
            if(version_compare($this->getServerVersion(), '5.0.0', '>=')) {
              $this->execute('SET SESSION character_set_database = ?', array($charset));
            } // if
          } // if

          $this->host = $host;
          $this->user = $user;
          $this->pass = $pass;
          $this->db_name = $database;
          $this->persist = $persist;
          $this->charset = $charset;

          $this->is_connected = true;
        } else {
          throw new DBConnectError($host, $user, $pass, $database, 'Failed to select database. Reason: ' . mysql_error($this->link));
        } // if
      } else {
        throw new DBConnectError($host, $user, $pass, $database, 'Failed to connect to database. Reason: ' . mysql_error());
      } // if
      
      return true;
    } // connect
    
    /**
     * Reopen connection, in case that connection has been lost
     *
     * @return boolean
     */
    function reconnect() {
    	return $this->connect(array(
        'host' => $this->host, 
        'user' => $this->user, 
        'pass' => $this->pass, 
        'db_name' => $this->db_name, 
        'persist' => $this->persist, 
        'charset' => $this->charset
      )); 
    } // reconnect
    
    /**
     * Disconnect
     *
     * @return boolean
     */
    function disconnect() {
      if(is_resource($this->link)) {
    	  mysql_close($this->link);
    	  $this->link = null;
    	  $this->is_connected = false;
      } // if
    } // disconnect

    /**
     * Execute SQL query
     *
     * @param string $sql
     * @param mixed $arguments
     * @param int $load
     * @param int $return_mode
     * @param string $return_class_or_field
     * @return array|bool|DBResult|mixed|MySQLDBResult|null
     * @throws DBQueryError
     * @throws DBNotConnectedError
     * @throws Exception
     */
    function execute($sql, $arguments = null, $load = DB::LOAD_ALL_ROWS, $return_mode = DB::RETURN_ARRAY, $return_class_or_field = null) {
      if($arguments && is_foreachable($arguments)) {
        $sql = $this->prepare($sql, $arguments);
      } // if
      
      if(!is_resource($this->link)) {
        throw new DBNotConnectedError();
      } // if

      $microtime = microtime(true);
      
      $query_result = mysql_query($sql, $this->link);

      DB::logQuery($sql, microtime(true) - $microtime);
      
      if((AngieApplication::isInDebugMode() || AngieApplication::isInDevelopment()) && !str_starts_with(strtolower($sql), 'explain')) {
        Logger::log($sql, Logger::INFO, 'sql');
      } // if
      
      if($query_result === false) {
        if(AngieApplication::isInDebugMode() || AngieApplication::isInDevelopment()) {
          Logger::log('SQL error. MySQL said: ' . mysql_error($this->link) . "\n($sql)", Logger::ERROR, 'sql');
        } // if
        
        $error_message = mysql_error($this->link);
        $error_number = mysql_errno($this->link);
        
        // Non-transactional tables not rolled back!
        if($error_number == 1196) {
          Logger::log('Non-transactional tables not rolled back!', Logger::WARNING, 'sql');
          return null;
          
        // Server gone away
        } elseif($error_number == 2006 || $error_number == 2013) {
          $executed = false;
          
          if(defined('DB_AUTO_RECONNECT') && DB_AUTO_RECONNECT > 0) {
            for($i = 1; $i <= DB_AUTO_RECONNECT; $i++) {
              if(AngieApplication::isInDebugMode() || AngieApplication::isInDevelopment()) {
                Logger::log("Trying to reconnect, attempt #$i", Logger::INFO, 'sql');
              } // if
              
              try {
                $this->reconnect();
                $query_result = mysql_query($sql, $this->link);
                if($query_result !== false) {
                  $executed = true;
                  break; // end of the loop
                } // if
              } catch(Exception $e) {
                throw $e; // rethrow exception
              } // try
            } // for
          } // if
          
          // Not executed after reconnects?
          if(!$executed) {
            throw new DBQueryError($sql, $error_number, $error_message);
          } // if
          
        // Deadlock detection and retry
        } elseif($error_number == 1213) {
          $executed = false;
          
          if(defined('DB_DEADLOCK_RETRIES') && DB_DEADLOCK_RETRIES) {
            for($i = 1; $i <= DB_DEADLOCK_RETRIES; $i++) {
              if(AngieApplication::isInDebugMode() || AngieApplication::isInDevelopment()) {
                Logger::log("Deadlock detected, retrying (attempt #$i)", Logger::INFO, 'sql');
              } // if
              
              // Seconds to miliseconds, and sleep
              usleep(DB_DEADLOCK_SLEEP * 1000000);
              
              $query_result = mysql_query($sql, $this->link);
              if($query_result !== false) {
                $executed = true;
                break; // end of the loop
              } // if
            } // for
          } // if
         
          // Not executed after retries?
          if(!$executed) {
            throw new DBQueryError($sql, $error_number, $error_message);
          } // if
          
        // Other
        } else {
          throw new DBQueryError($sql, $error_number, $error_message);
        } // if
      } // if
      
      $this->addToQueryLog($sql);
      
      switch($load) {
          
        // Return first row as array
        case DB::LOAD_FIRST_ROW:
          $result = mysql_num_rows($query_result) > 0 ? mysql_fetch_assoc($query_result) : null;
          mysql_free_result($query_result);
          
          switch($return_mode) {
            case DB::RETURN_OBJECT_BY_CLASS:
              $class_name = $return_class_or_field;
              
              $object = new $class_name();
              $object->loadFromRow($result);
              return $object;
            case DB::RETURN_OBJECT_BY_FIELD:
              $class_name = $result[$return_class_or_field];
              
              $object = new $class_name();
              $object->loadFromRow($result);
              return $object;
            default:
              return $result;
          } // switch
          
        // Return content of first column as an array
        case DB::LOAD_FIRST_COLUMN:
          if(mysql_num_rows($query_result) > 0) {
            $result = array();
            
            while($row = mysql_fetch_assoc($query_result)) {
              $result[] = array_shift($row);
            } // if
            
            mysql_free_result($query_result);
            
            return $result;
          } else {
            mysql_free_result($query_result);
            return null;
          } // if
          
        // Return first cell of first row
        case DB::LOAD_FIRST_CELL:
          if(mysql_num_rows($query_result) > 0) {
            $query_row = mysql_fetch_array($query_result);
            return array_shift($query_row);
          } else {
            return null;
          } // if
          
          //$result = mysql_num_rows($query_result) > 0 ? array_shift(mysql_fetch_array($query_result)) : null;
          //mysql_free_result($query_result);
          //return $result;
          
        // Load all unless we have a simple result
        default:
          if($query_result === true) {
            return true;
          } // if
          
          return mysql_num_rows($query_result) > 0 ? new MySQLDBResult($query_result, $return_mode, $return_class_or_field) : null;
      } // switch
    } // execute
    
    /**
     * Return number of affected rows
     *
     * @return integer
     */
    function affectedRows() {
      return mysql_affected_rows($this->link);
    } // affectedRows
    
    /**
     * Return last insert ID
     *
     * @return integer
     */
    function lastInsertId() {
      return mysql_insert_id($this->link);
    } // lastInsertId
    
    /**
     * Begin transaction
     *
     * @param string $message
     * @return boolean
     */
    function beginWork($message = null) {
      if($this->transaction_level == 0) {
        $this->execute('BEGIN WORK');
      } // if
      $this->transaction_level++;
      
      if(AngieApplication::isInDebugMode() || AngieApplication::isInDevelopment()) {
        Logger::log('Transaction level increased to ' . $this->transaction_level . ". Message: $message", Logger::INFO, 'sql');
      } // if
    } // beginWork
    
    /**
     * Commit transaction
     *
     * @param string $message
     * @return boolean
     */
    function commit($message = null) {
      if($this->transaction_level) {
        $this->transaction_level--;
        if($this->transaction_level == 0) {
          $this->execute('COMMIT');
        } else {
          if(AngieApplication::isInDebugMode() || AngieApplication::isInDevelopment()) {
            Logger::log('Transaction level decreased to ' . $this->transaction_level . ". Message: $message", Logger::INFO, 'sql');
          } // if
        } // if
      } // if
    } // commit
    
    /**
     * Rollback transaction
     *
     * @param string $message
     * @return boolean
     */
    function rollback($message = null) {
      if($this->transaction_level) {
        $this->transaction_level = 0;
        $this->execute('ROLLBACK');
      } // if
      
      if((AngieApplication::isInDebugMode() || AngieApplication::isInDevelopment()) && $message) {
        Logger::log("Rolling back the transaction. Reason: $message", Logger::INFO, 'sql');
      } // if
    } // rollback
    
    /**
     * Return true if system is in transaction
     * 
     * @return boolean
     */
    function inTransaction() {
      return $this->transaction_level > 0;
    } // inTransaction
    
    /**
     * Escape string before we use it in query...
     *
     * @param string $unescaped String that need to be escaped
     * @return string
     * @throws InvalidParamError
     */
    function escape($unescaped) {

      // Date time value
      if($unescaped instanceof DateTimeValue) {
        return "'" . mysql_real_escape_string(date(DATETIME_MYSQL, $unescaped->getTimestamp()), $this->link) . "'";
        
      // Date value
      } elseif($unescaped instanceof DateValue) {
        return "'" . mysql_real_escape_string(date(DATE_MYSQL, $unescaped->getTimestamp()), $this->link) . "'";
        
      // Float
      } elseif(is_float($unescaped)) {
        return "'" . str_replace(',', '.', (float) $unescaped) . "'"; // replace , with . for locales where comma is used by the system (German for example)
        
      // Boolean (maps to TINYINT(1))
      } elseif(is_bool($unescaped)) {
        return $unescaped ? "'1'" : "'0'";
        
      // NULL
      } elseif(is_null($unescaped)) {
        return 'NULL';
        
      // Escape first cell of each row
      } elseif($unescaped instanceof DBResult) {
        $escaped = array();
        foreach($unescaped as $v) {
          $escaped[] = $this->escape(array_shift($v));
        } // foreach
        
        return implode(', ', $escaped);
        
      // Escape each array element
      } elseif(is_array($unescaped)) {
        $escaped = array();
        foreach($unescaped as $v) {
          $escaped[] = $this->escape($v);
        } // foreach
        
        return implode(', ', $escaped);
        
      // Regular string and integer escape
      } else {
        if(!is_scalar($unescaped)) {
          throw new InvalidParamError('unescaped', $unescaped, '$unescaped is expected to be scalar, array, or instance of: DateValue, DateTimeValue, DBResult');
        } // if

      	return "'" . mysql_real_escape_string($unescaped, $this->link) . "'";
      } // if
    } // escape
    
    /**
     * Escape table field name
     *
     * @param string $unescaped
     * @return string
     */
    function escapeFieldName($unescaped) {
      return "`$unescaped`";
    } // escapeFieldName
    
    /**
     * Escape table name
     *
     * @param string $unescaped
     * @return string
     */
    function escapeTableName($unescaped) {
      return "`$unescaped`";
    } // escapeTableName
    
    // ---------------------------------------------------
    //  Table management
    // ---------------------------------------------------
    
    /**
     * Returns true if table $name exists
     * 
     * @param string $name
     * @return boolean
     */
    function tableExists($name) {
      if($name) {
        $result = $this->execute('SHOW TABLES LIKE ?', array($name));
        
        return $result instanceof DBResult && $result->count() == 1;
      } // if
      
      return false;
    } // tableExists
    
    /**
     * Create new table instance
     *
     * @param string $name
     * @return MySQLDBTable
     */
    function createTable($name) {
      return new MySQLDBTable($name);
    } // createTable
    
    /**
     * Load table information
     *
     * @param boolean $name
     * @return MySQLDBTable
     */
    function loadTable($name) {
      return new MySQLDBTable($name, true);
    } // laodTable
    
    /**
     * Return array of tables from selected database
     *
     * If there is no tables in database empty array is returned
     *
     * @param string $prefix
     * @return array
     */
    function listTables($prefix = null) {
      if($prefix) {
        $rows = $this->execute("SHOW TABLES LIKE '$prefix%'");
      } else {
        $rows = $this->execute('SHOW TABLES');
      } // if
      
      if(is_foreachable($rows)) {
        $tables = array();
        foreach($rows as $row) {
          $tables[] = array_shift($row);
        } // foreach
        return $tables;
      } else {
      	return null;
      } // if
    } // listTables
    
    /**
     * List names of the table
     *
     * @param string $table_name
     * @return array
     */
    function listTableFields($table_name) {
      $rows = $this->execute("DESCRIBE $table_name");
      if(is_foreachable($rows)) {
        $result = array();
        foreach($rows as $row) {
          $result[] = $row['Field'];
        } // foreach
        return $result;
      } // if
      
      return array();
    } // listTableFields
    
    /**
     * Drop list of tables
     *
     * @param array $tables
     * @param string $prefix
     * @throws DBQueryError
     */
    function dropTables($tables, $prefix = '') {
      if(!empty($tables)) {
        $tables = (array) $tables;
      
        foreach($tables as $k => $v) {
          $tables[$k] = $this->escapeTableName($prefix . $v);
        } // foreach
        
        $this->execute('DROP TABLES ' . implode(', ', $tables));
      } // if
    } // dropTables

    /**
     * Return array of table indexes
     *
     * @param string $table_name
     * @return array
     */
    function listTableIndexes($table_name) {
      $rows = $this->execute("SHOW INDEXES FROM $table_name");
      if(is_foreachable($rows)) {
        $result = array();
        foreach($rows as $row) {
          $key_name = $row['Key_name'];

          if(!in_array($key_name, $result)) {
            $result[] = $key_name;
          } // if
        } // foreach
        return $result;
      } // if

      return array();
    } // listTableIndexes
    
    /**
     * Drop all tables from database
     *
     * @return boolean
     */
    function clearDatabase() {
      $tables = $this->listTables();
      if(is_foreachable($tables)) {
        return $this->execute('DROP TABLES ' . implode(', ', $tables));
      } else {
        return true; // it's already clear
      } // if
    } // clearDatabase


    /**
     * Gets maximum packet size allowed to be inserted into database
     *
     * @return int
     */
    function getMaxPacketSize() {
      return intval($this->getServerVariable('max_allowed_packet'));
    } //if
    
    // ---------------------------------------------------
    //  File import / export
    // ---------------------------------------------------
    
    /**
     * Do a mysql dump of specified tables
     * 
     * If $table_name is empty it will dump all tables in current database
     *
     * @param array $tables
     * @param string $output_file
     * @param boolean $dump_structure
     * @param boolean $dump_data
     * @return boolean
     * @throws Error
     */
    function exportToFile($tables, $output_file, $dump_structure = true, $dump_data = true) {
      $max_query_length = 838860; // maximum query length
      
      if(empty($tables)) {
        $tables = $this->listTables();
      } // if
            
      if(is_foreachable($tables)) {
        $handle = fopen($output_file, 'w');
        
        if(empty($handle)) {
          throw new Error("Cannot create output file: '$output_file'");
        } // if
        
        foreach($tables as $table_name) {
          
          // Dump_structure
        	if($dump_structure) {
        	  $create_table = $this->executeFirstRow("SHOW CREATE TABLE $table_name");
        	  fwrite($handle, "DROP TABLE IF EXISTS $table_name;\n".$create_table['Create Table'].";\n\n");
        	} // if
        	
        	// Dump_data
        	if($dump_data) {
            fwrite($handle, "/*!40000 ALTER TABLE $table_name DISABLE KEYS */;\n");

            $query_result = mysql_query("SELECT * FROM $table_name", $this->link);
            
            $inserted_values = '';
            while($row = mysql_fetch_row($query_result)) {
              $values = '';
              
              foreach($row as $field) {
                if($values) {
                  $values .= ',';
                } // if
                
                $values .= $field === null ? "NULL" : "'" . mysql_real_escape_string($field, $this->link) . "'";
              } // foreach
              
              $inserted_values.= ($inserted_values ? ',' : '');
            	$inserted_values.='('.$values.')';
            	
              if(strlen($inserted_values) > $max_query_length) {
                fwrite($handle, "INSERT INTO $table_name VALUES $inserted_values;\n");
                $inserted_values = '';
              } // if
            } // while
            
            if($inserted_values) {
              fwrite($handle, "INSERT INTO $table_name VALUES $inserted_values;\n");
            } // if
            fwrite($handle, "/*!40000 ALTER TABLE $table_name ENABLE KEYS */;\n");
        	} // if
        } // foreach
        
        fclose($handle);
      } // if
    } // exportToFile
    
    /**
     * Import sql file into database
     *
     * @param string $sql_file
     * @param string $database
     * @return boolean
     * @throws Error
     * @throws InvalidParamError
     * @throws Exception
     */
    function importFromFile($sql_file, $database = null) {
      if(!is_file($sql_file)) {
        throw new InvalidParamError('sql_file', $sql_file, "SQL file '$sql_file' not found");
      } // if
      
      if($database) {
        $select_db_result = mysql_select_db($database, $this->link);
        if(!$select_db_result) {
          throw new InvalidParamError('database', $database, "Could not select '$database' database");
        } // if
      } // if
      
      $sql = file($sql_file);
      if(is_foreachable($sql)) {
        try {
          $this->beginWork('Importing SQL file @ ' . __CLASS__);
          
          $query = '';
          foreach($sql as $sql_line){
            if($sql_line && strpos(trim($sql_line), "--") !== 0) {
              $query .= $sql_line;
    
              if(preg_match("/;[\040]*\$/", $sql_line)){
                $this->execute($query);
                $query = '';
              } // if
            } // if
          } // foreach
          
          $this->commit('SQL file imported @ ' . __CLASS__);
        } catch(Exception $e) {
          $this->rollback('Failed to import SQL file @ ' . __CLASS__);
          throw $e; // Rethrow
        } // try
      } else {
        throw new Error("SQL file '$sql_file' is empty");
      } // if
    } // importFromFile
    
    /**
     * Get MySQL variable value
     *
     * @param string $variable_name
     * @return mixed
     */
    function getServerVariable($variable_name) {
      $variable = $this->executeFirstRow("SHOW VARIABLES LIKE '$variable_name'");
      
      return is_array($variable) && isset($variable['Value']) ? $variable['Value'] : null;
    } // getVariableValue
    
    /**
     * Return version of the server
     *
     * @return string
     */
    function getServerVersion() {
      return mysql_get_server_info($this->link);
    } // getServerVersion
    
    /**
     * Returns true if server we are connected to supports collation
     *
     * @return boolean
     */
    function supportsCollation() {
      return version_compare($this->getServerVersion(), '4.1') >= 0;
    } // supportsCollation

    /**
     * Return true if we have InnoDB support
     *
     * @return boolean
     */
    function hasInnoDBSupport() {
      $engines = DB::execute('SHOW ENGINES');

      if($engines) {
        foreach($engines as $engine) {
          if(strtolower($engine['Engine']) == 'innodb' && in_array(strtolower($engine['Support']), array('yes', 'default'))) {
            return true;
          } // if
        } // foreach
      } // if

      return false;
    } // hasInnoDBSupport
    
  }