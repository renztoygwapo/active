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
  function smarty_function_select_conflict_notification_delivery($params, &$smarty) {
    $name = array_required_var($params, 'name', true);
    $value = array_var($params, 'value');

    return HTML::radioGroupFromPossibilities($name, array(
      IncomingMail::CONFLICT_NOTIFY_DO_NOT => lang("Don't Notify Administrators About New Conflicts"),
      IncomingMail::CONFLICT_NOTIFY_INSTANTLY => lang('Instantly Notify Administrators About New Conflicts'),
      IncomingMail::CONFLICT_NOTIFY_ON_DAILY => lang('Notify Administrators About New Conflicts Once a Day'),
    ), $value, $params);
  } // smarty_function_select_conflict_notification_delivery