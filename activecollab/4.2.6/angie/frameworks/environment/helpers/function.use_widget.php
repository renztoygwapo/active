<?php

  /**
   * use_widget helper implementation
   *
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Add widget to required list
   *
   * Parameters:
   *
   * - name - widget filename
   * - module - name of the module
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_use_widget($params, &$smarty) {
    $name = array_required_var($params, 'name', true);
    $module = array_required_var($params, 'module', true);

    AngieApplication::useWidget($name, $module);
  } // smarty_function_use_widget