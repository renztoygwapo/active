<?php

  /**
   * Initialize database classes
   *
   * @package angie.library.database
   */

  defined('DB_PERSIST') or define('DB_PERSIST', false);
  defined('DB_CHARSET') or define('DB_CHARSET', 'utf8');
  defined('DB_FORCE_CHARSET') or define('DB_FORCE_CHARSET', true); // Run SET NAMES when connecting
  defined('DB_AUTO_RECONNECT') or define('DB_AUTO_RECONNECT', 3); // Number of reconnection times if server drops connection in the middle of request
  defined('DB_DEADLOCK_RETRIES') or define('DB_DEADLOCK_RETRIES', 3);
  defined('DB_DEADLOCK_SLEEP') or define('DB_DEADLOCK_SLEEP', 0.5);
  defined('DB_INTERFACE') or define('DB_INTERFACE', 'mysql');
  
  // Database
  require_once ANGIE_PATH . '/classes/database/DB.class.php';
  require_once ANGIE_PATH . '/classes/database/DBConnection.class.php';
  require_once ANGIE_PATH . '/classes/database/DBResult.class.php';
  require_once ANGIE_PATH . '/classes/database/DBResultIterator.class.php';
  
  // Data object
  define('DATA_TYPE_NONE', 'NONE');
  define('DATA_TYPE_INTEGER', 'INTEGER');
  define('DATA_TYPE_STRING', 'STRING');
  define('DATA_TYPE_FLOAT', 'FLOAT');
  define('DATA_TYPE_BOOLEAN', 'BOOLEAN');
  define('DATA_TYPE_ARRAY', 'ARRAY');
  define('DATA_TYPE_RESOURCE', 'RESOURCE');
  define('DATA_TYPE_OBJECT', 'OBJECT');
  
  require_once ANGIE_PATH . '/classes/database/DataObject.class.php';
  require_once ANGIE_PATH . '/classes/database/DataManager.class.php';
  require_once ANGIE_PATH . '/classes/database/DataObjectPool.class.php';

  require_once ANGIE_PATH . '/classes/database/associations/DataAssociation.class.php';
  require_once ANGIE_PATH . '/classes/database/associations/DataAssociationHasMany.class.php';
  require_once ANGIE_PATH . '/classes/database/associations/DataAssociationHasAndBelongsToMany.class.php';

  AngieApplication::setForAutoload(array(
  
    // Utilities
    'DBBatchInsert' => ANGIE_PATH . '/classes/database/DBBatchInsert.class.php',
  
    // Database engineer
    'DBTable' => ANGIE_PATH . '/classes/database/engineer/DBTable.class.php', 
    'DBColumn' => ANGIE_PATH . '/classes/database/engineer/DBColumn.class.php', 
    'DBIndex' => ANGIE_PATH . '/classes/database/engineer/DBIndex.class.php', 
    
    'DBIndexPrimary' => ANGIE_PATH . '/classes/database/engineer/indexes/DBIndexPrimary.class.php', 
    
    'DBNumericColumn' => ANGIE_PATH . '/classes/database/engineer/columns/DBNumericColumn.class.php', 
    'DBBinaryColumn' => ANGIE_PATH . '/classes/database/engineer/columns/DBBinaryColumn.class.php', 
    'DBBoolColumn' => ANGIE_PATH . '/classes/database/engineer/columns/DBBoolColumn.class.php', 
    'DBDateColumn' => ANGIE_PATH . '/classes/database/engineer/columns/DBDateColumn.class.php', 
    'DBDateTimeColumn' => ANGIE_PATH . '/classes/database/engineer/columns/DBDateTimeColumn.class.php', 
    'DBEnumColumn' => ANGIE_PATH . '/classes/database/engineer/columns/DBEnumColumn.class.php', 
    'DBFloatColumn' => ANGIE_PATH . '/classes/database/engineer/columns/DBFloatColumn.class.php', 
    'DBDecimalColumn' => ANGIE_PATH . '/classes/database/engineer/columns/DBDecimalColumn.class.php', 
    'DBMoneyColumn' => ANGIE_PATH . '/classes/database/engineer/columns/DBMoneyColumn.class.php', 
    'DBIntegerColumn' => ANGIE_PATH . '/classes/database/engineer/columns/DBIntegerColumn.class.php', 
    'DBSetColumn' => ANGIE_PATH . '/classes/database/engineer/columns/DBSetColumn.class.php', 
    'DBStringColumn' => ANGIE_PATH . '/classes/database/engineer/columns/DBStringColumn.class.php', 
    'DBTextColumn' => ANGIE_PATH . '/classes/database/engineer/columns/DBTextColumn.class.php', 
    'DBTimeColumn' => ANGIE_PATH . '/classes/database/engineer/columns/DBTimeColumn.class.php', 
    'DBIpAddressColumn' => ANGIE_PATH . '/classes/database/engineer/columns/DBIpAddressColumn.class.php', 
    
    // Special columns
    'DBAdditionalPropertiesColumn' => ANGIE_PATH . '/classes/database/engineer/columns_special/DBAdditionalPropertiesColumn.class.php', 
    'DBIdColumn' => ANGIE_PATH . '/classes/database/engineer/columns_special/DBIdColumn.class.php', 
    'DBNameColumn' => ANGIE_PATH . '/classes/database/engineer/columns_special/DBNameColumn.class.php', 
    'DBTypeColumn' => ANGIE_PATH . '/classes/database/engineer/columns_special/DBTypeColumn.class.php', 
    
    // Composite columns
    'DBCompositeColumn' => ANGIE_PATH . '/classes/database/engineer/columns_composite/DBCompositeColumn.class.php', 
    'DBActionOnByColumn' => ANGIE_PATH . '/classes/database/engineer/columns_composite/DBActionOnByColumn.class.php', 
    'DBRelatedObjectColumn' => ANGIE_PATH . '/classes/database/engineer/columns_composite/DBRelatedObjectColumn.class.php',
    'DBParentColumn' => ANGIE_PATH . '/classes/database/engineer/columns_composite/DBParentColumn.class.php',
    'DBStateColumn' => ANGIE_PATH . '/classes/database/engineer/columns_composite/DBStateColumn.class.php',
    'DBUserColumn' => ANGIE_PATH . '/classes/database/engineer/columns_composite/DBUserColumn.class.php', 
    'DBVisibilityColumn' => ANGIE_PATH . '/classes/database/engineer/columns_composite/DBVisibilityColumn.class.php', 
    
    // Errors
    'DBError' => ANGIE_PATH . '/classes/database/errors/.class.php', 
    'DBConnectError' => ANGIE_PATH . '/classes/database/errors/DBConnectError.class.php', 
    'DBQueryError' => ANGIE_PATH . '/classes/database/errors/DBQueryError.class.php', 
    'ValidationErrors' => ANGIE_PATH . '/classes/database/errors/ValidationErrors.class.php', 
    'DBNotConnectedError' => ANGIE_PATH . '/classes/database/errors/DBNotConnectedError.class.php', 
  ));
  
  //if(defined('DB_INTERFACE') && DB_INTERFACE == 'mysql') {
    require_once ANGIE_PATH . '/classes/database/mysql/MySQLDBConnection.class.php';
    require_once ANGIE_PATH . '/classes/database/mysql/MySQLDBResult.class.php';
    require_once ANGIE_PATH . '/classes/database/mysql/MySQLDBTable.class.php';
  //} // if