<?php

  // Include application specific model base
  require_once APPLICATION_PATH . '/resources/ActiveCollabModuleModel.class.php';

  /**
   * Status module model definition
   *
   * @package activeCollab.modules.status
   * @subpackage resources
   */
  class StatusModuleModel extends ActiveCollabModuleModel {
    
    /**
     * Construct status module model definition
     *
     * @param StatusModule $parent
     */
    function __construct(StatusModule $parent) {
      parent::__construct($parent);
      
      $this->addModel(DB::createTable('status_updates')->addColumns(array(
        DBIdColumn::create(), 
        DBIntegerColumn::create('parent_id', 10)->setUnsigned(true), 
        DBStringColumn::create('message', 255, ''), 
        DBActionOnByColumn::create('created', true), 
        DBDateTimeColumn::create('last_update_on'), 
      ))->addIndices(array(
        DBIndex::create('last_update_on'), 
        DBIndex::create('parent_id'), 
      )));
    } // __construct
    
    /**
     * Load initial module data
     *
     * @param string $environment
     */
    function loadInitialData($environment = null) {
      $this->addConfigOption('status_update_last_visited');
      
      parent::loadInitialData($environment);
    } // loadInitialData
    
  }