<?php

  /**
   * Complete framework definition
   *
   * @package angie.frameworks.complete
   */
  class CompleteFramework extends AngieFramework {
    
    /**
     * Short framework name
     *
     * @var string
     */
    protected $name = 'complete';
    
    /**
     * Define change status routes for given context
     *
     * @param string $context
     * @param array $context_path
     * @param string $controller_name
     * @param string $module_name
     * @param mixed $context_requirements
     */
    function defineChangeStatusRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
      Router::map("{$context}_complete", "$context_path/complete", array('controller' => $controller_name, 'action' => "{$context}_complete", 'module' => $module_name), $context_requirements);
      Router::map("{$context}_reopen", "$context_path/reopen", array('controller' => $controller_name, 'action' => "{$context}_reopen", 'module' => $module_name), $context_requirements);
    } // defineChangeStatusRoutesFor
    
    /**
     * Define priority routes for given context
     * 
     * @param string $context
     * @param array $context_path
     * @param string $controller_name
     * @param string $module_name
     * @param mixed $context_requirements
     */
    function definePriorityRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
      Router::map("{$context}_update_priority", "$context_path/update-priority", array('controller' => $controller_name, 'action' => "{$context}_update_priority", 'module' => $module_name), $context_requirements);
    } // definePriorityRoutesFor    
    
    /**
     * Define event handlers
     */
    function defineHandlers() {

    } // defineHandlers
    
  }