<?php

  // Include applicaiton specific model base
  require_once APPLICATION_PATH . '/resources/ActiveCollabModuleModel.class.php';

  /**
   * Source module model definition
   *
   * @package activeCollab.modules.source
   * @subpackage resources
   */
  class SourceModuleModel extends ActiveCollabModuleModel {
  
    /**
     * Construct source module model definition
     *
     * @param SourceModule $parent
     */
    function __construct(SourceModule $parent) {
      parent::__construct($parent);

      $this->addModel(DB::createTable('source_repositories')->addColumns(array(
        DBIdColumn::create(),
        DBStringColumn::create('name',255),
        DBTypeColumn::create('SourceRepository'),
        DBActionOnByColumn::create('created'),
        DBActionOnByColumn::create('updated'),
        DBStringColumn::create('repository_path_url', 255),
        DBStringColumn::create('username', 255),
        DBStringColumn::create('password', 255),
        DBIntegerColumn::create('update_type', 3),
        DBTextColumn::create('graph'),
        DBAdditionalPropertiesColumn::create(),
      )))->setTypeFromField('type');
  
      $this->addModel(DB::createTable('source_paths')->addColumns(array(
        DBIdColumn::create(), 
        DBIntegerColumn::create('commit_id', 11)->setUnsigned(true), 
        DBBoolColumn::create('is_dir'), 
        DBStringColumn::create('path', 255), 
        DBStringColumn::create('action', 1), 
      ))->addIndices(array(
        DBIndex::create('commit_id', DBIndex::KEY, array('commit_id')), 
      )));
  
      $this->addModel(DB::createTable('source_users')->addColumns(array(
        DBIdColumn::create(),
        DBIntegerColumn::create('repository_id', 5, '0')->setUnsigned(true), 
        DBStringColumn::create('repository_user', 50, ''), 
        DBIntegerColumn::create('user_id', 5)->setUnsigned(true), 
      ))->addIndices(array(
        DBIndex::create('repository_user', DBIndex::UNIQUE, array('repository_id', 'repository_user')),
      )));
  
      $this->addModel(DB::createTable('source_commits')->addColumns(array(
        DBIdColumn::create(),
        DBStringColumn::create('name',255),
        DBTypeColumn::create('SourceCommit'),
        DBIntegerColumn::create('revision_number', 11)->setUnsigned(true), 
        DBIntegerColumn::create('repository_id', 11)->setUnsigned(true), 
        DBTextColumn::create('message_title'), 
        DBTextColumn::create('message_body'), 
        DBDateTimeColumn::create('authored_on'), 
        DBStringColumn::create('authored_by_name', 100), 
        DBStringColumn::create('authored_by_email', 100), 
        DBDateTimeColumn::create('commited_on'), 
        DBStringColumn::create('commited_by_name', 100), 
        DBStringColumn::create('commited_by_email', 100),
        DBStringColumn::create('branch_name', 255),
        DBTextColumn::create('diff'),
      ))->addIndices(array(
        DBIndex::create('repository_id'),
        DBIndex::create('commited_on'),
        DBIndex::create('branch_name'),
      )))->setTypeFromField('type');
  
      $this->addModel(DB::createTable('commit_project_objects')->addColumns(array(
        DBIdColumn::create(),
        DBIntegerColumn::create('parent_id', 11)->setUnsigned(true),
        DBStringColumn::create('parent_type', 50),
        DBIntegerColumn::create('project_id', 11, '0')->setUnsigned(true), 
        DBIntegerColumn::create('revision', 11, '0')->setUnsigned(true),
        DBStringColumn::create('branch_name', 255),
        DBIntegerColumn::create('repository_id', 11, '0')->setUnsigned(true), 
      ))->addIndices(array(
        DBIndex::create('parent', DBIndex::KEY, array('parent_id', 'parent_type')),
      )));
    } //__construct
    
    /**
     * Load initial framework data
     *
     * @param string $environment
     */
    function loadInitialData($environment = null) {
      $project_tabs = $this->getConfigOptionValue('project_tabs');

      if(!in_array('source', $project_tabs)) {
        $project_tabs[] = 'source';
        $this->setConfigOptionValue('project_tabs', $project_tabs);
      } // if

      if (extension_loaded('svn')) {
        $this->addConfigOption('source_svn_type','extension');
      } elseif (extension_loaded('xml') && function_exists('xml_parser_create')) {
        $this->addConfigOption('source_svn_type','exec');
      } else {
        $this->addConfigOption('source_svn_type','none');
      } // if
      
      $this->addConfigOption('source_svn_path', '/usr/bin/');
      $this->addConfigOption('source_svn_config_dir', null);

      $this->addConfigOption('source_svn_trust_server_cert', false);
      
      $this->addConfigOption('source_mercurial_path', '/usr/local/bin/');

      $this->addConfigOption('default_source_branch', null);

      if (!is_dir(WORK_PATH . '/git')) {
      	mkdir(WORK_PATH . '/git', 0777);
      } //if

      if (!file_exists(WORK_PATH . '/git/.htaccess')) {
        $file_name = WORK_PATH . '/git/.htaccess';
        $file_handle = fopen($file_name, 'w') or die("can't open file");
        $file_data = "Deny from all";
        fwrite($file_handle, $file_data);
        fclose($file_handle);
      } //if
      
      if (!is_dir(WORK_PATH . '/hg')) {
      	mkdir(WORK_PATH . '/hg', 0777);
      } //if

      if (!file_exists(WORK_PATH . '/hg/.htaccess')) {
        $file_name = WORK_PATH . '/hg/.htaccess';
        $file_handle = fopen($file_name, 'w') or die("can't open file");
        $file_data = "Deny from all";
        fwrite($file_handle, $file_data);
        fclose($file_handle);
      } //if
      
      parent::loadInitialData($environment);
    } // loadInitialData
    
  }