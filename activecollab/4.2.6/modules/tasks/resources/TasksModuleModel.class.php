<?php

  // Include application specific model base
  require_once APPLICATION_PATH . '/resources/ActiveCollabModuleModel.class.php';

  /**
   * Tasks module model
   * 
   * @package activeCollab.modules.tasks
   * @subpackage models
   */
  class TasksModuleModel extends ActiveCollabModuleModel {
    
    /**
     * Construct tasks module model
     * 
     * @param TasksModule $parent
     */
    function __construct(TasksModule $parent) {
      parent::__construct($parent);
      
      $this->addModel(DB::createTable('public_task_forms')->addColumns(array(
        DBIdColumn::create(), 
        DBIntegerColumn::create('project_id', 11, '0')->setUnsigned(true), 
        DBStringColumn::create('slug', 50, ''), 
        DBNameColumn::create(100), 
        DBTextColumn::create('body'), 
        DBBoolColumn::create('is_enabled', true), 
        DBAdditionalPropertiesColumn::create(), 
      ))->addIndices(array(
        DBIndex::create('slug', DBIndex::UNIQUE, 'slug'), 
      )));

      $this->addTable(DB::createTable('related_tasks')->addColumns(array(
        DBIntegerColumn::create('parent_task_id', 10, 0)->setUnsigned(true),
        DBIntegerColumn::create('related_task_id', 10, 0)->setUnsigned(true),
        DBStringColumn::create('note', 255),
        DBActionOnByColumn::create('created', true),
      ))->addIndices(array(
        DBIndex::create('PRIMARY', DBIndex::PRIMARY, array('parent_task_id', 'related_task_id')),
      )));

      $this->addModel(DB::createTable('task_segments')->addColumns(array(
        DBIdColumn::create(),
        DBNameColumn::create(50),
        DBAdditionalPropertiesColumn::create(),
      )))->setOrderBy('name');
    } // __construct
  
    /**
     * Load initial framework data
     *
     * @param string $environment
     */
    function loadInitialData($environment = null) {
      $this->addConfigOption('task_categories', array('General'));
      
      $this->addConfigOption('tasks_auto_reopen', true);
      $this->addConfigOption('tasks_auto_reopen_clients_only', true);
      $this->addConfigOption('tasks_public_submit_enabled', false);
      $this->addConfigOption('tasks_use_captcha', false);

      $this->registerCustomFieldsForType('Task');

      $project_tabs = $this->getConfigOptionValue('project_tabs');

      if(!in_array('tasks', $project_tabs)) {
        $project_tabs[] = 'tasks';
        $this->setConfigOptionValue('project_tabs', $project_tabs);
      } // if
      
      parent::loadInitialData($environment);
    } // loadInitialData
    
  }