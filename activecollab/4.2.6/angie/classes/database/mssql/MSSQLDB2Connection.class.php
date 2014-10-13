<?php
	
  /**
   * Database connection for MicroSoft SQL Server (mssql library)
   *
   * @package angie.library.database
   * @subpackage mssql
   */
	class MSSQLDB2Connection extends DBConnection {
	
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
    function __construct($host, $user, $pass, $database, $persist = true, $charset = null) {
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
     * @param string $host Hostname
     * @param string $user Username
     * @param string $pass Password
     * @param string $database Database name
     * @param boolean $persist Create persistant database connection
     * @param string $charset
     * @return boolean
     * @throws DBConnectError
     */
    function connect($parameters) {
      $host     = $parameters['host'];
      $user     = $parameters['user'];
      $pass     = $parameters['pass'];
      $database = $parameters['db_name'];
      $persist  = $parameters['persist'];
      $charset  = $parameters['charset'];
      
      $this->link = $persist ? mssql_pconnect($host, $user, $pass) : mysql_connect($host, $user, $pass);
       
      if(!is_resource($this->link)) {
        throw new DBConnectError($host, $user, $pass, $database);
      } // if
      
      if(!mssql_select_db($database, $this->link)) {
        throw new DBConnectError($host, $user, $pass, $database);
      } // if
      
      
 
      $this->host = $host;
      $this->user = $user;
      $this->pass = $pass;
      $this->db_name = $database;
      $this->persist = $persist;
      $this->charset = $charset;
      
      $this->is_connected = true;
      
      return true;
    }//connect
    
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
	}//reconnect

	/**
     * Disconnect
     *
     * @param void
     * @return boolean
     */
	function disconnect() {
		if(is_resource($this->link)) {
    	  mssql_close($this->link);
    	  $this->link = null;
    	  $this->is_connected = false;
      } // if
	}//Disconnect

	/**
     * Return number of affected rows
     *
     * @param void
     * @return integer
     */
	function affectedRows() {
		return mssql_rows_affected($this->link);
	}//affectedRows

	/**
     * Return last insert ID
     *
     * @param void
     * @return integer
     */
	function lastInsertId() {
		$query="SELECT @@IDENTITY as last_insert_id";
		
        $query_result = mssql_query($query,$this->link); 
        $query_result = mssql_fetch_object($query_result);
		mssql_free_result($query_result);
        return $query_result->last_insert_id;
	} //lastInsertId

	/**
     * Begin transaction
     *
     * @param string $message
     * @return boolean
     */
	function beginWork($message = null) {
      if($this->transaction_level == 0) {
        $this->execute('BEGIN TRANSACTION');
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
     * Execute SQL query
     *
     * @param string $sql
     * @param arary $arguments
     * @param boolean $load
     * @param integer $return_mode
     * @param string $return_class_or_field
     * @return DBResult
     * @throws DBQueryError
     */
	function execute($sql, $arguments = null, $load = DB::LOAD_ALL_ROWS, $return_mode = DB::RETURN_ARRAY, $return_class_or_field = null) {
		  if($arguments && is_foreachable($arguments)) {
	        $sql = $this->prepare($sql, $arguments);
	      } // if
	      
	      if(!is_resource($this->link)) {
	        debug_print_backtrace();;
	        throw new DBNotConnectedError();
	      } // if
	      
	      $query_result = mssql_query($sql, $this->link);
	      
	      if((AngieApplication::isInDebugMode() || AngieApplication::isInDevelopment()) && !str_starts_with(strtolower($sql), 'explain')) {
	        Logger::log($sql, Logger::INFO, 'sql');
	      } // if
	      
	      if($query_result === false) {
	        if(AngieApplication::isInDebugMode() || AngieApplication::isInDevelopment()) {
	          Logger::log('SQL error. MySQL said: ' . mssql_get_last_message() . "\n($sql)", Logger::ERROR, 'sql');
	        } // if
	        
	        $error_message = mssql_get_last_message();
	        
	        $query = "select @@ERROR as ErrorCode";
	        $query_result = mssql_query($query,$this->link);
	        $result = mssql_fetch_object($query_result);
	        mssql_free_result($query_result);
        	$error_number = $result->ErrorCode;
	        
	        // Non-transactional tables not rolled back!
	        if($error_number == 1196) {
	          Logger::log('Non-transactional tables not rolled back!', Logger::WARNING, 'sql');
	          return;
	          
	        // Server gone away
	        } elseif($error_number == 2006 || $error_number == 2013) {
	          if(defined('DB_AUTO_RECONNECT') && DB_AUTO_RECONNECT > 0) {
	            
	            $executed = false;
	            for($i = 1; $i <= DB_AUTO_RECONNECT; $i++) {
	              if(AngieApplication::isInDebugMode() || AngieApplication::isInDevelopment()) {
	                Logger::log("Trying to reconnect, attempt #$i", Logger::INFO, 'sql');
	              } // if
	              
	              try {
	                $this->reconnect();
	                $query_result = mssql_query($sql, $this->link);
	                if($query_result !== false) {
	                  $executed = true;
	                  break; // end of the loop
	                } // if
	              } catch(Exception $e) {
	                throw $e; // rethrow exception
	              } // try
	            } // for
	            
	            // Not executed after reconnects?
	            if(!$executed) {
	              throw new DBQueryError($sql, $error_number, $error_message);
	            } // if
	            
	          // No auto-reconnection
	          } else {
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
	          $result = mssql_num_rows($query_result) > 0 ? mssql_fetch_array($query_result) : null;
	          mssql_free_result($query_result);
	          
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
	          if(mssql_num_rows($query_result) > 0) {
	            $result = array();
	            
	            while($row = mssql_fetch_array($query_result)) {
	              $result[] = array_shift($row);
	            } // if
	            
	            mssql_free_result($query_result);
	            
	            return $result;
	          } else {
	            mssql_free_result($query_result);
	            return null;
	          } // if
	          
	          
	        // Return first cell of first row
	        case DB::LOAD_FIRST_CELL:
	          $result = mssql_num_rows($query_result) > 0 ? array_shift(mssql_fetch_array($query_result)) : null;
	          mssql_free_result($query_result);
	          return $result;
	          
	        // Load all unless we have a simple result
	        default:
	        	if($query_result === true) {
	            return true;
	          } // if
	          
	          return mssql_num_rows($query_result) > 0 ? new MSSQLDB2Result($query_result, $return_mode, $return_class_or_field) : null;
	      } // switch
	}// execute

	/**
     * Escape string before we use it in query...
     *
     * @param string $unescaped String that need to be escaped
     * @return string
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
        return str_replace(',', '.', (float) $unescaped); // replace , with . for locales where comma is used by the system (German for example)
        
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
      	$search=array("\\","\0","\n","\r","\x1a","'",'"',"%","_");
        $replace=array("\\\\","\\0","\\n","\\r","\Z","''",'\"',"[%]","[_]");
        return "'" . str_replace($search,$replace,$unescaped) . "'";
      } // if
	} // escape

	// Utility methods
    
    /**
     * Return array of tables from selected database
     *
     * If there is no tables in database empty array is returned
     * 
     * @param void
     * @return array
     */
	function listTables($prefix = null) {
      if($prefix) {
        $rows = $this->execute("select name as tables from $this->db_name..sysobjects where (xtype = 'U') and (name like '$prefix%')");
      } else {
        $rows = $this->execute("select name as tables from $this->db_name..sysobjects where (xtype = 'U')");
      } // if
      
      if(is_foreachable($rows)) {
        $tables = array();
        foreach($rows as $row) {
          $tables[] = array_shift($row);
        } // foreach
        return $tables;
      } // if
      
      return null;
    } // listTables

	/**
     * List names of the table
     *
     * @param string $table_name
     * @return array
     */
    function listTableFields($table_name) {
      $rows = $this->execute("SELECT COLUMN_NAME as Field FROM INFORMATION_SCHEMA.Columns WHERE TABLE_NAME = '$table_name'");
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
     * Drop all tables from database
     *
     * @param void
     * @return boolean
     */
    function clearDatabase() {
      $tables = $this->listTables();
      if(is_foreachable($tables)) {
        return $this->execute('DROP TABLE ' . implode(', ', $tables));
      } else {
        return true; // it's already clear
      } // if
    } // clearDatabase

	/**
     * Do a backup of database 
     * 
     * @param array $tables
     * @param string $output_file_path
     * @param boolean $dump_structure
     * @param boolean $dump_data
     * @return boolean
     */
	function exportToFile($tables, $output_file, $dump_structure = true, $dump_data = true) {
	  if ((strlen($output_file) < 5) OR (substr($output_file,strlen($output_file)-4,4) !== '.bak')) {
		$output_file .= '.bak';
	  }
      $exec = $this->execute("BACKUP DATABASE $this->db_name TO DISK = '$output_file'");
      
      if (!$exec) {
        throw new Error("Cannot create output file: '$output_file'; Reason: ".mssql_get_last_message());
      } // if
      
      return true;
		
    } // exportToFile

	/**
	 * @param string $file_path
	 * @param string $database
	 * @return boolean
	 */
	function importFromFile($sql_file, $database = null) {
		if($this->db_name != 'master') {
        throw new InvalidParamError('db_name', $this->db_name, "Database selected must be master");
      } // if
      $new_conn = !mssql_connect( $this->host,$this->user,$this->pass);
      if(!mssql_select_db($database,$new_conn)) {
        throw new InvalidParamError("Could not select '$database' database");
      } else {
      	mssql_close($new_conn);
      }	//if
      $exec = $this->execute("  USE master;
						GO
						ALTER DATABASE $database
						SET MULTI_USER;
						GO
						RESTORE DATABASE $database 
						FROM DISK = '$sql_file'");  
      if (!$exec) {
        throw new Error("Cannot restore database $database from: '$file_path'; Reason: ".mssql_get_last_message());
      } // if
      return  true;   
	}      

    /**
     * Get INFORMATION_SCHEMA data
     * 
  	 * @param string $suffix_name
  	 * @return mixed
  	 */
  	function getServerVariable($variable_name) {
  		return $this->execute("SELECT * FROM INFORMATION_SCHEMA.$variable_name");
  	}

		/**
     * Return version of the server
     *
     * @return string
     */
    function getServerVersion() {
      return 'unavailable';
    } // getServerVersion
}