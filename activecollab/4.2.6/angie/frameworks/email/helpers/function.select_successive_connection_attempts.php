<?php

  /**
   * select_conflict_notification_delivery helper implementation
   *
   * @package angie.frameworks.email
   * @subpackage helpers
   */

  /**
   * Render select_conflict_notification_delivery select box
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_successive_connection_attempts($params, &$smarty) {
    $name = array_required_var($params, 'name', true);
    $value = array_var($params, 'value');

    return HTML::selectFromPossibilities($name, array(
      3  => lang('After :num failures', array('num' => 3)),
      10 => lang('After :num failures', array('num' => 10)),
      30 => lang('After :num failures', array('num' => 30)),
    ), $value, $params);
  } // smarty_function_select_successive_connection_attempts