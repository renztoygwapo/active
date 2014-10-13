<?php

	/**
	* MySQL to MS SQL Migration class
	*
	* @package angie.library.database
	* @subpackage conversion
	*/

	class MyToMSSQLConvertor {
		
		/**
		 * MySQL connection object
		 * 
		 * @var object
		 */
		private $mysqlobj;
		
		/**
		 * MS SQL connection object
		 * 
		 * @var object
		 */
		private $mssqlobj;
		
		
		/**
		 * Construct MyToMSSQLConvertor
		 * 
		 * @param array $scource_server_info array
		 * @param array $destination_server_info
		 * 
		 */
		
		function __construct($scource_server_info,$destination_server_info) {
			
		 $this->mysqlobj = new MySQLDBConnection(
		 	$scource_server_info['host'],
		 	$scource_server_info['user'],
		 	$scource_server_info['pass'],
		 	$scource_server_info['database']
		 ); 
		 
		 $this->mssqlobj = new MSSQLDB2Connection(
		 	$destination_server_info['host'],
		 	$destination_server_info['user'],
		 	$destination_server_info['pass'],
		 	$destination_server_info['database']
		 );
		 		 
		} // __construct
		
		/**
		 * Migrate MyToMSSQLConvertor
		 * 
		 * @param bool $migrate_data
		 * @param array $table_names
		 * @param string $table_prefix
		 * 
		 */
		function migrate($migrate_data = TRUE,$table_names = 'ALL',$table_prefix = NULL) {
			$this->mssqlobj->clearDatabase();
			$tables = array();
			if ($table_names === 'ALL') {
				$tables = $this->mysqlobj->listTables($table_prefix);
			} else {
				foreach ($table_names as $table_name) {
					$tables[] = $table_prefix ? $table_prefix.$table_name : $table_name ;
				} //foreach
			} //if
			
			//iteration for tables
			foreach ($tables as $table_name) {
				$has_identity = false;
				$dump = "CREATE TABLE dbo.$table_name ( \r\n";
				$result = $this->mysqlobj->execute('DESCRIBE '.$table_name);
				$mysqltable = $result->toArray();
				$result->free();
				$table_columns = array();
				// creating table
				foreach ($mysqltable as $column) {
					$table_columns[] = $column['Field'];
					$dump.= '['.$column['Field'].'] '
							.strtoupper($this->getTypeLengthPrecision($column['Type'])).' '
							.$this->getNullability($column['Null']).' '
							.$this->getIdentity($column['Extra']).' '
							.$this->getDefault($column['Default']).",\r\n";
					if ($column['Extra'] == 'auto_increment') {
						$has_identity = true;
					} //if
				} //foreach
				
				//primary key 
				$primary = false;
				$primary_key_string = "";
				foreach ($mysqltable as $column) {
					if ($column['Key'] === "PRI") {
						$primary = true;
						$primary_key_string.= "$column[Field],";
					} //if
				} //foreach
				if ($primary) {
					$primary_key_string = "PRIMARY KEY (" . substr($primary_key_string,0,strlen($primary_key_string)-1)."),\r\n";
					$dump.=$primary_key_string;
				} //if
				// unique indexes
				foreach ($mysqltable as $column) {
					if ($column['Key'] === "UNI") {
						$dump.= "UNIQUE ($column[Field]),\r\n";
					} //if
				} //foreach
				$dump.= ") \r\n";
				
				// creating indexes
				$result = $this->mysqlobj->execute('SHOW INDEX FROM '.$table_name);
				$indices = $result->toArray();
				$result->free();
				$keys = array();
				foreach ($indices as $index) {
					$key_name = $index['Key_name'];
					if (($key_name != "PRIMARY") && ($index['Non_unique'] == '1')) {
						$keys[$key_name][] = $index['Column_name']; 
					} //if
				} //foreach
				foreach ($keys as $key_name => $columns) {
					$key_string = "CREATE INDEX $key_name ON dbo.$table_name (";
					foreach ($columns as $column) {
						$key_string.=$column.",";
					} // foreach
					$key_string = substr($key_string,0,strlen($key_string)-1);
					$key_string.= ")\r\n";
					$dump.=$key_string;
				} // foreach
				
				$this->mssqlobj->execute($dump);
				unset($dump);
				
				// creating data
				if ($migrate_data) {
					$result = $this->mysqlobj->execute("SELECT * FROM $table_name");
					if (is_object($result)) {
						if ($has_identity) {
							$dump= "SET IDENTITY_INSERT dbo.$table_name ON\r\n";
							$this->mssqlobj->execute($dump);
						} //if				
						$data = $result->toArray();
						foreach ($data as $row) {
							$insert_row = "INSERT INTO dbo.$table_name (";
							foreach ($table_columns as $column) {
								$insert_row.="[$column],";
							} //foreach
							$insert_row = substr($insert_row,0,strlen($insert_row)-1);
							$insert_row.= ") \r\n VALUES (";
							foreach ($row as $cell) {
								$insert_row.=$this->mssqlobj->escape($cell).",";
							}// foreach
							$insert_row = substr($insert_row,0,strlen($insert_row)-1);
							$insert_row.= ")\r\n";
							$this->mssqlobj->execute($insert_row);
							unset($insert_row);
						} //foreach
						if ($has_identity) {
							$dump="SET IDENTITY_INSERT dbo.$table_name OFF\r\n";
							$this->mssqlobj->execute($dump);
						} //if
						$result->free();
					}// if
				} // if
				unset($dump);
			} //foreach
			$this->mysqlobj->disconnect();
			$this->mssqlobj->disconnect();
			return true;
		} //migrate
		
		/**
		 * getTypeLengthPrecision MyToMSSQLConvertor
		 * 
		 * @param string $value
		 * 
		 */
		private function getTypeLengthPrecision($value) {
			$value = str_replace_first('unsigned','',$value);
			$value = trim($value);
			$type = '';
			for ($i = 0; $i < strlen($value);$i++) {
				if (! ctype_alpha($value[$i])) {
					break;
				}
				$type.=$value[$i];
			}
			switch ($type) {
				// Text types
				case "char" :
					return $value;
				case "varchar":
					return $value;
				case "tinytext":
					return "varchar(max)";
				case "text":
					return "varchar(max)";
				case "blob":
					return "varchar(max)";
				case "mediumtext":
					return "varchar(max)";
				case "mediumblob":
					return "varchar(max)";
				case "longtext":
					return "varchar(max)";
				case "longblob":
					return "varchar(max)";
					
				// Number types
				case "tinyint":
					if ($value == "tinyint(1)") {
						return "bit";
					} else {
						return "smallint";
					} //if
				case "smallint":
					return "smallint";
				case "mediumint":
					return "int";
				case "int":
					return "int";
				case "bigint":
					return "bigint";
				case "float":
					return "real";
				case "double":
					return str_replace_first("double","decimal",$value);
				case "decimal":
					return $value;
					
				//Date types
				case "date":
					return "smalldatetime";
				case "datetime":
					return "datetime";
				case "timestamp":
					return "timestamp";
				case "time":
					return "time";
					
				//Misc types
				case "enum":
					return "varchar(100)";
				case "set":
					return "not supported";
			} //switch
		} //getTypeLengthPrecision
		
		/**
		 * getNullability MyToMSSQLConvertor
		 * 
		 * @param string $value
		 * 
		 */
		private function getNullability($value) {
			if ($value == "NO") {
				return "NOT NULL";
			} else {
				return 'NULL';
			} // if
		} //getNullability
		
		/**
		 * getIdentity MyToMSSQLConvertor
		 * 
		 * @param string $value
		 * 
		 */
		private function getIdentity($value) {
			if ($value == 'auto_increment') {
				return "IDENTITY";
			} else {
				return "";
			} //if
		} // getIdentity
		
		/**
		 * getDefault MyToMSSQLConvertor
		 * 
		 * @param string $value
		 * 
		 */
		private function getDefault($value) {
			if ($value === NULL) {
				return '';
			} else {
				return "DEFAULT '$value'";
			} //if
		} //getDefault
		
	}