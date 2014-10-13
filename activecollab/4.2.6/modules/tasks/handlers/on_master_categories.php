<?php

  /**
   * on_master_categories handler definition
   *
   * @package activeCollab.modules.tasks
   * @subpackage handlers
   */

  /**
   * Handle on_master_categories event
   *
   * @param array $categories
   */
  function tasks_handle_on_master_categories(&$categories) {
  	$categories[] = array(
  	  'name' => 'task_categories',
  	  'label' => lang('Task Categories'),
  	  'value' => ConfigOptions::getValue('task_categories'),
  	  'type' => 'TaskCategory', 
  	);
  } // tasks_handle_on_master_categories