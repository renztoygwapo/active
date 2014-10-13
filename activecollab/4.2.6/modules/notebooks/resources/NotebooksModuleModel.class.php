<?php

  // Include application specific model base
  require_once APPLICATION_PATH . '/resources/ActiveCollabModuleModel.class.php';

  /**
   * Notebooks module model definition
   *
   * @package activeCollab.modules.notebooks
   * @subpackage models
   */
	class NotebooksModuleModel extends ActiveCollabModuleModel {
    
    /**
     * Construct notebooks module model definition
     *
     * @param NotebooksModule $parent
     */
		function __construct(NotebooksModule $parent) {
      parent::__construct($parent);
      
      $this->addModel(DB::createTable('notebook_pages')->addColumns(array(
			  DBIdColumn::create(), 
			  DBParentColumn::create(),
			  DBNameColumn::create(255), 
			  DBTextColumn::create('body')->setSize(DBColumn::BIG),  
			  DBStateColumn::create(),
			  DBBoolColumn::create('is_locked', false), 
			  DBActionOnByColumn::create('created'),
			  DBActionOnByColumn::create('updated'),
			  DBActionOnByColumn::create('last_version'),
			  DBIntegerColumn::create('position', 10)->setUnsigned(true), 
			  DBIntegerColumn::create('version', 5, '0')->setUnsigned(true), 
			)))->setOrderBy('position');
			
			$this->addModel(DB::createTable('notebook_page_versions')->addColumns(array(
        DBIdColumn::create(), 
        DBIntegerColumn::create('notebook_page_id', 10, '0')->setUnsigned(true), 
        DBIntegerColumn::create('version', 5, '0')->setUnsigned(true), 
        DBNameColumn::create(255), 
        DBTextColumn::create('body')->setSize(DBColumn::BIG), 
        DBActionOnByColumn::create('created'), 
      ))->addIndices(array(
        DBIndex::create('notebook_page_version', DBIndex::UNIQUE, array('notebook_page_id', 'version')), 
      )))->setOrderBy('created_on DESC');
    } // __construct
    
    /**
     * Load initial framework data
     *
     * @param string $environment
     */
    function loadInitialData($environment = null) {
      $this->addConfigOption('notebook_categories', array('General'));

      $project_tabs = $this->getConfigOptionValue('project_tabs');

      if(!in_array('notebooks', $project_tabs)) {
        $project_tabs[] = 'notebooks';
        $this->setConfigOptionValue('project_tabs', $project_tabs);
      } // if
      
      parent::loadInitialData($environment);
    } // loadInitialData
    
  }