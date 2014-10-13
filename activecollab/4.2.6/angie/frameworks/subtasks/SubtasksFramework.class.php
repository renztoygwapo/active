<?php

  /**
   * Subtasks framework
   *
   * @package angie.frameworks.subtasks
   */
  class SubtasksFramework extends AngieFramework {
    
    /**
     * Short framework name
     *
     * @var string
     */
    protected $name = 'subtasks';
    
    /**
     * Define subtask routes for given context
     *
     * @param string $context
     * @param string $context_path
     * @param string $controller_name
     * @param string $module_name
     * @param array $context_requirements
     */
    function defineSubtasksRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
      $subtask_requirements = is_array($context_requirements) ? array_merge($context_requirements, array('subtask_id' => Router::MATCH_ID)) : array('subtask_id' => Router::MATCH_ID);
      
      Router::map("{$context}_subtasks", "$context_path/subtasks", array('controller' => $controller_name, 'action' => "{$context}_subtasks", 'module' => $module_name), $context_requirements);
      Router::map("{$context}_subtasks_archive", "$context_path/subtasks/archive", array('controller' => $controller_name, 'action' => "{$context}_subtasks_archive", 'module' => $module_name), $context_requirements);
      Router::map("{$context}_subtasks_add", "$context_path/subtasks/add", array('controller' => $controller_name, 'action' => "{$context}_add_subtask", 'module' => $module_name), $context_requirements);
      Router::map("{$context}_subtasks_reorder", "$context_path/subtasks/reorder", array('controller' => $controller_name, 'action' => "{$context}_reorder_subtasks", 'module' => $module_name), $context_requirements);
      
      Router::map("{$context}_subtask", "$context_path/subtasks/:subtask_id", array('controller' => $controller_name, 'action' => "{$context}_view_subtask", 'module' => $module_name), $subtask_requirements);
      Router::map("{$context}_subtask_edit", "$context_path/subtasks/:subtask_id/edit", array('controller' => $controller_name, 'action' => "{$context}_edit_subtask", 'module' => $module_name), $subtask_requirements);
      
      Router::map("{$context}_subtask_complete", "$context_path/subtasks/:subtask_id/complete", array('controller' => $controller_name, 'action' => "{$context}_complete_subtask", 'module' => $module_name), $subtask_requirements);
      Router::map("{$context}_subtask_reopen", "$context_path/subtasks/:subtask_id/reopen", array('controller' => $controller_name, 'action' => "{$context}_reopen_subtask", 'module' => $module_name), $subtask_requirements); 
      
      AngieApplication::getModule('subscriptions')->defineSubscriptionRoutesFor("{$context}_subtask", "$context_path/subtasks/:subtask_id", $controller_name, $module_name, $subtask_requirements);
      AngieApplication::getModule('environment')->defineStateRoutesFor("{$context}_subtask", "$context_path/subtasks/:subtask_id", $controller_name, $module_name, $subtask_requirements);
      
      if(AngieApplication::isFrameworkLoaded('schedule')) {
      	AngieApplication::getModule('schedule')->defineScheduleRoutesFor("{$context}_subtask", "$context_path/subtasks/:subtask_id", $controller_name, $module_name, $subtask_requirements);
      } // if
      
			if(AngieApplication::isFrameworkLoaded('complete')) {
        AngieApplication::getModule('complete')->definePriorityRoutesFor("{$context}_subtask", "$context_path/subtasks/:subtask_id", $controller_name, $module_name, $subtask_requirements);
      } // if
      
      if (AngieApplication::isFrameworkLoaded('labels')) {
      	AngieApplication::getModule('labels')->defineLabelsRoutesFor("{$context}_subtask", "$context_path/subtasks/:subtask_id", $controller_name, $module_name, $subtask_requirements);
      } // if
      
      if (AngieApplication::isFrameworkLoaded('assignees')) {
      	AngieApplication::getModule('assignees')->defineAssigneesRoutesFor("{$context}_subtask", "$context_path/subtasks/:subtask_id", $controller_name, $module_name, $subtask_requirements);
      } // if
    } // defineSubtasksRoutesFor
    
  }