<?php

  /**
   * Modules framework intialization file
   *
   * @package angie.frameworks.modules
   */

  const MODULES_FRAMEWORK = 'modules';
  const MODULES_FRAMEWORK_PATH = __DIR__;
  
  defined('MODULES_FRAMEWORK_INJECT_INTO') or define('MODULES_FRAMEWORK_INJECT_INTO', 'system');
  defined('MODULES_FRAMEWORK_ADMIN_ROUTE_BASE') or define('MODULES_FRAMEWORK_ADMIN_ROUTE_BASE', 'admin');
  defined('MODULES_MANAGEMENT_ENABLED') or define('MODULES_MANAGEMENT_ENABLED', true);