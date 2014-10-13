<?php

  /**
   * Modules framework definition
   * 
   * Modules framework adds modules management (install, uninstall etc) features 
   * to the application
   *
   * @package angie.frameworks.environment
   */
  class ModulesFramework extends AngieFramework {
    
    /**
     * Framework name
     *
     * @var string
     */
    protected $name = 'modules';
    
    /**
     * Define framework routes
     */
    function defineRoutes() {
      Router::map('modules_admin', MODULES_FRAMEWORK_ADMIN_ROUTE_BASE . '/modules', array('controller' => 'modules_admin', 'action' => 'index', 'module' => MODULES_FRAMEWORK_INJECT_INTO));
      
      Router::map('module_admin_module', MODULES_FRAMEWORK_ADMIN_ROUTE_BASE . '/modules/:module_name', array('controller' => 'modules_admin', 'action' => 'module', 'module' => MODULES_FRAMEWORK_INJECT_INTO));
      Router::map('module_admin_module_install', MODULES_FRAMEWORK_ADMIN_ROUTE_BASE . '/modules/:module_name/install', array('controller' => 'modules_admin', 'action' => 'install', 'module' => MODULES_FRAMEWORK_INJECT_INTO));
      Router::map('module_admin_module_uninstall', MODULES_FRAMEWORK_ADMIN_ROUTE_BASE . '/modules/:module_name/uninstall', array('controller' => 'modules_admin', 'action' => 'uninstall', 'module' => MODULES_FRAMEWORK_INJECT_INTO));
      Router::map('module_admin_module_enable', MODULES_FRAMEWORK_ADMIN_ROUTE_BASE . '/modules/:module_name/enable', array('controller' => 'modules_admin', 'action' => 'enable', 'module' => MODULES_FRAMEWORK_INJECT_INTO));
      Router::map('module_admin_module_disable', MODULES_FRAMEWORK_ADMIN_ROUTE_BASE . '/modules/:module_name/disable', array('controller' => 'modules_admin', 'action' => 'disable', 'module' => MODULES_FRAMEWORK_INJECT_INTO));
      Router::map('execute_installation_steps', MODULES_FRAMEWORK_ADMIN_ROUTE_BASE . '/execute-installation-step', array('controller' => 'modules_admin', 'action'=>'execute_installation_steps', 'module' => MODULES_FRAMEWORK_INJECT_INTO));
      Router::map('disable_custom_modules', MODULES_FRAMEWORK_ADMIN_ROUTE_BASE . '/disable-custom-modules', array('controller' => 'modules_admin', 'action'=>'disable_custom_modules', 'module' => MODULES_FRAMEWORK_INJECT_INTO));
    } // defineRoutes
    
  }