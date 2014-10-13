<?php

  /**
   * Schedule framework definition
   *
   * @package angie.frameworks.complete
   */
  class ScheduleFramework extends AngieFramework {
    
    /**
     * Short framework name
     *
     * @var string
     */
    protected $name = 'schedule';

    /**
     * Define schedule routes for given context
     *
     * @param string $context
     * @param string $context_path
     * @param string $controller_name
     * @param string $module_name
     * @param array $context_requirements
     */
    function defineScheduleRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
      Router::map("{$context}_reschedule", "$context_path/reschedule", array('controller' => $controller_name, 'action' => "{$context}_reschedule", 'module' => $module_name), $context_requirements);
    } // defineScheduleRoutesFor
    
  }