<?php

  /**
   * Authentication framework model definition
   *
   * @package angie.frameworks.authentication
   * @subpackage resources
   */
  class AuthenticationFrameworkModel extends AngieFrameworkModel {
    
    /**
     * Construct authentication framework model
     * 
     * @param AuthenticationFramework $parent
     */
    function __construct(AuthenticationFramework $parent) {
      parent::__construct($parent);
      
      // API client subscriptions
      $this->addModel(DB::createTable('api_client_subscriptions')->addColumns(array(
        DBIdColumn::create(), 
        DBTypeColumn::create('ApiClientSubscription'), 
        DBIntegerColumn::create('user_id', 10, '0')->setUnsigned(true), 
        DBStringColumn::create('token', 40), 
        DBStringColumn::create('client_name', 100), 
        DBStringColumn::create('client_vendor', 100), 
        DBDateTimeColumn::create('created_on'), 
        DBDateTimeColumn::create('last_used_on'), 
        DBBoolColumn::create('is_enabled', false), 
        DBBoolColumn::create('is_read_only', true), 
      ))->addIndices(array(
        DBIndex::create('token', DBIndex::UNIQUE, 'token'), 
      )))->setTypeFromField('type');
      
      // Users model
      $this->addModel(DB::createTable('users')->addColumns(array(
        DBIdColumn::create(),
        DBTypeColumn::create('User'),
        DBStateColumn::create(), 
        DBStringColumn::create('first_name', 50), 
        DBStringColumn::create('last_name', 50), 
        DBStringColumn::create('email', 150, ''), 
        DBStringColumn::create('password', 255, ''),
        DBEnumColumn::create('password_hashed_with', array('pbkdf2', 'sha1'), 'pbkdf2'),
        DBDateColumn::create('password_expires_on'),
        DBStringColumn::create('password_reset_key', 20), 
        DBDateTimeColumn::create('password_reset_on'),
        DBActionOnByColumn::create('created'), 
        DBActionOnByColumn::create('updated'),
        DBDateTimeColumn::create('invited_on'),
        DBDateTimeColumn::create('last_login_on'), 
        DBDateTimeColumn::create('last_visit_on'), 
        DBDateTimeColumn::create('last_activity_on'),
        DBAdditionalPropertiesColumn::create(),
      ))->addIndices(array(
        DBIndex::create('email'),
        DBIndex::create('last_activity_on'), 
      )))->setObjectIsAbstract(true)->setTypeFromField('type')->setOrderBy('CONCAT(first_name, last_name, email)');

      $this->addTableFromFile('user_addresses'); // Alternative user addresses
      
      // User sessions
      $this->addTable(DB::createTable('user_sessions')->addColumns(array(
        DBIdColumn::create(), 
        DBIntegerColumn::create('user_id', 10, '0')->setUnsigned(true),
        DBIpAddressColumn::create('user_ip'), 
        DBTextColumn::create('user_agent'),
        DBIntegerColumn::create('visits', 10, '0')->setUnsigned(true), 
        DBIntegerColumn::create('remember', 3, '0')->setUnsigned(true),
        DBEnumColumn::create('interface', array('default', 'phone', 'tablet'), 'default'), 
        DBDateTimeColumn::create('created_on'), 
        DBDateTimeColumn::create('last_activity_on'), 
        DBDateTimeColumn::create('expires_on'), 
        DBStringColumn::create('session_key', 40), 
      ))->addIndices(array(
        DBIndex::create('session_key', DBIndex::UNIQUE, 'session_key'), 
        DBIndex::create('expires_on'), 
      )));

	    // Security Logs
	    $this->addModel(DB::createTable('security_logs')->addColumns(array(
		    DBIdColumn::create()->setSize(DBColumn::BIG),
		    DBUserColumn::create('user', true),
		    DBUserColumn::create('login_as', true),
		    DBUserColumn::create('logout_by', true),
		    DBEnumColumn::create('event', array('login', 'logout', 'expired', 'failed')),
		    DBDateTimeColumn::create('event_on'),
		    DBIpAddressColumn::create('user_ip'),
		    DBTextColumn::create('user_agent'),
		    DBBoolColumn::create('is_api'),
	    ))->addIndices(array(
			  DBIndex::create('event_on'),
		  )));

	    // User sessions
	    $this->addTable(DB::createTable('api_token_logs')->addColumns(array(
		    DBIdColumn::create(),
		    DBDateTimeColumn::create('counts_on'),
		    DBIntegerColumn::create('total', 10, '0')->setUnsigned(true),
	    ))->addIndices(array(
		    DBIndex::create('counts_on'),
	    )));
    } // __construct
    
    /**
     * Load initial data
     * 
     * @param string $environment
     */
    function loadInitialData($environment = null) {
      parent::loadInitialData($environment);

      $this->addConfigOption('maintenance_enabled', false);
    } // loadInitialData
    
  }