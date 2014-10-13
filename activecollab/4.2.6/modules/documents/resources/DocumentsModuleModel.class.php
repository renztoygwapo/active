<?php

  // Include application specific model base
  require_once APPLICATION_PATH . '/resources/ActiveCollabModuleModel.class.php';

  /**
   * Documents module model definition
   *
   * @package activeCollab.modules.documents
   * @subpackage models
   */
  class DocumentsModuleModel extends ActiveCollabModuleModel {
    
    /**
     * Construct documents module model definition
     *
     * @param DocumentsModule $parent
     */
    function __construct(DocumentsModule $parent) {
      parent::__construct($parent);
      
      $this->addModel(DB::createTable('documents')->addColumns(array(
        DBIdColumn::create(), 
        DBIntegerColumn::create('category_id', 11)->setUnsigned(true), 
        DBEnumColumn::create('type', array('text', 'file'), 'text'), 
        DBNameColumn::create(150),
        DBTextColumn::create('body'),
        DBIntegerColumn::create('size', 11),
        DBStringColumn::create('mime_type', 255),
        DBStringColumn::create('location', 50),
        DBStringColumn::create('md5', 32),
        DBStateColumn::create(),
        DBVisibilityColumn::create(),
        DBBoolColumn::create('is_pinned', false), 
        DBActionOnByColumn::create('created')
      )));
    } // __construct
    
    /**
     * Load initial framework data
     *
     * @param string $environment
     */
    function loadInitialData($environment = null) {
      $this->loadTableData('categories', array(
        array(
          'type' => 'DocumentsCategory',
          'name' => 'General'
        )
      ));
      
      parent::loadInitialData($environment);
    } // loadInitialData
    
  }