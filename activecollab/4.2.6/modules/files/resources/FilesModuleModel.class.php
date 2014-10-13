<?php

  // Include application specific model base
  require_once APPLICATION_PATH . '/resources/ActiveCollabModuleModel.class.php';

  /**
   * Files module model definition
   *
   * @package activeCollab.modules.files
   * @subpackage models
   */
  class FilesModuleModel extends ActiveCollabModuleModel {
    
    /**
     * Construct file module model definition
     *
     * @param FilesModule $parent
     */
    function __construct(FilesModule $parent) {
      parent::__construct($parent);
      
      $this->addModel(DB::createTable('file_versions')->addColumns(array(
        DBIdColumn::create(), 
        DBIntegerColumn::create('file_id', 10, '0')->setUnsigned(true), 
        DBIntegerColumn::create('version_num', 5, '0')->setUnsigned(true), 
        DBNameColumn::create(255), 
        DBStringColumn::create('mime_type', 255, 'application/octet-stream'), 
        DBIntegerColumn::create('size', 10, 0)->setUnsigned(true), 
        DBStringColumn::create('location', 50), 
        DBStringColumn::create('md5', 32), 
        DBActionOnByColumn::create('created', true), 
      ))->addIndices(array(
        DBIndex::create('file_version', DBIndex::KEY, array('file_id', 'version_num')), 
      )));
      
      $this->addModel(DB::createTable('text_document_versions')->addColumns(array(
        DBIdColumn::create(), 
        DBIntegerColumn::create('text_document_id', 10, '0')->setUnsigned(true), 
        DBIntegerColumn::create('version_num', 5, '0')->setUnsigned(true), 
        DBNameColumn::create(255), 
        DBTextColumn::create('body')->setSize(DBColumn::BIG), 
        DBActionOnByColumn::create('created'), 
      ))->addIndices(array(
        DBIndex::create('text_document_version', DBIndex::KEY, array('text_document_id', 'version_num')), 
      )));
    } // __construct
    
    /**
     * Load initial framework data
     *
     * @param string $environment
     */
    function loadInitialData($environment = null) {
      $this->addConfigOption('asset_categories', array('General'));

      $project_tabs = $this->getConfigOptionValue('project_tabs');

      if(!in_array('files', $project_tabs)) {
        $project_tabs[] = 'files';
        $this->setConfigOptionValue('project_tabs', $project_tabs);
      } // if
      
      parent::loadInitialData($environment);
    } // loadInitialData
    
  }