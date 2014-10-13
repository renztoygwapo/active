<?php

  /**
   * Reports framework
   * 
   * @package angie.frameworks.reports
   */
  class ReportsFramework extends AngieFramework {
  
    /**
     * Short framework name
     *
     * @var string
     */
    protected $name = 'reports';
    
    /**
     * Define module routes
     */
    function defineRoutes() {
      Router::map('reports', 'reports', array('controller' => 'reports', 'action' => 'index', 'module' => REPORTS_FRAMEWORK_INJECT_INTO));
    } // defineRoutes

    /**
     * Define data filter routes
     *
     * @param string $context
     * @param string $context_path
     * @param string $controller_name
     * @param string $module_name
     * @param array|null $context_requirements
     */
    function defineDataFilterRoutes($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
      $id = "{$context}_id";

      if(is_array($context_requirements)) {
        $context_requirements[$id] = Router::MATCH_ID;
      } else {
        $context_requirements = array($id => Router::MATCH_ID);
      } // if

      $plural = Inflector::pluralize($context);

      Router::map($plural, "reports/$context_path", array('controller' => $controller_name, 'module' => $module_name));
      Router::map("{$plural}_add", "reports/$context_path/add", array('controller' => $controller_name, 'module' => $module_name, 'action' => 'add'));
      Router::map("{$plural}_run", "reports/$context_path/run", array('controller' => $controller_name, 'module' => $module_name, 'action' => 'run'));
      Router::map("{$plural}_export", "reports/$context_path/export", array('controller' => $controller_name, 'module' => $module_name, 'action' => 'export'));

      Router::map($context, "reports/$context_path/:{$id}", array('controller' => $controller_name, 'module' => $module_name, 'action' => 'view'), $context_requirements);
      Router::map("{$context}_edit", "reports/$context_path/:{$id}/edit", array('controller' => $controller_name, 'module' => $module_name, 'action' => 'edit'), $context_requirements);
      Router::map("{$context}_delete", "reports/$context_path/:{$id}/delete", array('controller' => $controller_name, 'module' => $module_name, 'action' => 'delete'), $context_requirements);
    } // defineDataFilterRoutes

    /**
     * Define handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_main_menu', 'on_main_menu');
    } // defineHandlers(
    
  }