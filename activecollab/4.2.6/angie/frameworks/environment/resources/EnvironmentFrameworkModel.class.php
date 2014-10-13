<?php

  /**
   * Envrionment framework model definition
   *
   * @package angie.frameworks.environment
   * @subpackage resources
   */
  class EnvironmentFrameworkModel extends AngieFrameworkModel {
    
    /**
     * Construct environment framework model definition
     *
     * @param EnvironmentFramework $parent
     */
    function __construct(EnvironmentFramework $parent) {
      parent::__construct($parent);
      
      $this->addTable(DB::createTable('config_options')->addColumns(array(
        DBNameColumn::create(100), 
        DBStringColumn::create('module', 30), 
        DBTextColumn::create('value'), 
      ))->addIndices(array(
        DBIndexPrimary::create('name'), 
      )));
      
      $this->addTable(DB::createTable('config_option_values')->addColumns(array(
        DBNameColumn::create(50), 
        DBParentColumn::create(true), 
        DBTextColumn::create('value'), 
      ))->addIndices(array(
        DBIndexPrimary::create(array('name', 'parent_type', 'parent_id')),
      )));

      $this->addTableFromFile('modules');
      $this->addTableFromFile('executed_model_migrations');

      $this->addTable(DB::createTable('update_history')->addColumns(array(
        DBIdColumn::create()->setSize(DBColumn::SMALL), 
        DBStringColumn::create('version', 30, ''), 
        DBDateTimeColumn::create('created_on'), 
      ))->addIndices(array(
        DBIndex::create('version', DBIndex::UNIQUE, 'version'), 
        DBIndex::create('created_on'), 
      )));
      
      $this->addModel(DB::createTable('access_logs')->addColumns(array(
        DBIdColumn::create()->setSize(DBColumn::BIG), 
        DBParentColumn::create(), 
        DBUserColumn::create('accessed_by', true), 
        DBDateTimeColumn::create('accessed_on'), 
        DBStringColumn::create('ip_address', 50), 
        DBBoolColumn::create('is_download', false), 
      ))->addIndices(array(
        DBIndex::create('accessed_on'), 
      )));
      
      $this->addTable(DB::createTable('access_logs_archive')->addColumns(array(
        DBIdColumn::create()->setSize(DBColumn::BIG), 
        DBParentColumn::create(), 
        DBUserColumn::create('accessed_by', true), 
        DBDateTimeColumn::create('accessed_on'), 
        DBStringColumn::create('ip_address', 50), 
        DBBoolColumn::create('is_download', false), 
      ))->addIndices(array(
        DBIndex::create('accessed_on'), 
      )));
      
      $this->addTable(DB::createTable('object_contexts')->addColumns(array(
        DBIdColumn::create(), 
        DBParentColumn::create(false), 
        DBStringColumn::create('context', 255, ''), 
      ))->addIndices(array(
        DBIndex::create('context', DBIndex::UNIQUE), 
        DBIndex::create('parent', DBIndex::UNIQUE, array('parent_type', 'parent_id')), 
      )));
      
      $this->addTable(DB::createTable('routing_cache')->addColumns(array(
        DBIdColumn::create(), 
        DBStringColumn::create('path_info', 255), 
        DBStringColumn::create('name', 255), 
        DBTextColumn::create('content'), 
        DBDateTimeColumn::create('last_accessed_on'), 
      ))->addIndices(array(
        DBIndex::create('path_info', DBIndex::UNIQUE), 
      )));

//      $this->addModel(DB::createTable('test_data_objects')->addColumns(array(
//        DBIdColumn::create(),
//        DBNameColumn::create(100),
//        DBTextColumn::create('description'),
//        DBEnumColumn::create('type', array('Task', 'Milestone', 'Message', 'File'), 'File'),
//        DBDateTimeColumn::create('created_on'),
//        DBDateTimeColumn::create('updated_on'),
//      )));
    } // __construct
    
    /**
     * Load initial framework data
     *
     * @param string $environment
     */
    function loadInitialData($environment = null) {
      parent::loadInitialData($environment);

      $this->addConfigOption('maintenance_message');
      
      $this->addConfigOption('first_run_on');
      $this->addConfigOption('help_improve_application', true);
      
      $this->addConfigOption('identity_name', 'Application');

      $this->addConfigOption('on_logout_url');
      
      $this->addConfigOption('last_frequently_activity');
      $this->addConfigOption('last_hourly_activity');
      $this->addConfigOption('last_daily_activity');

      $this->addConfigOption('control_tower_check_scheduled_tasks', true);
      $this->addConfigOption('control_tower_check_disk_usage', true);
      $this->addConfigOption('control_tower_check_performance', true);

      $this->addConfigOption('disk_space_limit', 0);
      $this->addConfigOption('disk_space_email_notifications', true);
      $this->addConfigOption('disk_space_low_space_threshold', 95);
      $this->addConfigOption('disk_space_old_versions_size', null);

      $this->addConfigOption('current_scheme', 'default');
      $this->addConfigOption('custom_schemes', null);

      $this->addConfigOption('whitelisted_tags', array(
				'environment' => array(
			  	'a' 	=> array('href', 'title', 'class', 'object-id', 'object-class'),
			  	'div' => array('class', 'placeholder-type', 'placeholder-object-id', 'placeholder-extra'),
    	  	'img' => array('src', 'alt', 'title', 'class')
				)
			));
			
			$this->addConfigOption('require_index_rebuild', false);

      // firewall settings
	    $this->addConfigOption('firewall_enabled', false);
	    $this->addConfigOption('firewall_settings', null);
	    $this->addConfigOption('firewall_white_list', null);
	    $this->addConfigOption('firewall_black_list', null);
	    $this->addConfigOption('firewall_temp_list', null);

      // proxy settings
      $this->addConfigOption('network_proxy_enabled', false);
      $this->addConfigOption('network_proxy_protocol', 'http');
      $this->addConfigOption('network_proxy_address', '');
      $this->addConfigOption('network_proxy_port', '');
    } // loadInitialData
    
  }