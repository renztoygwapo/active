<?php

  /**
   * scheduled_task_url handler implementation file
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */

  /**
   * Return task URL based on parameters
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_scheduled_task_url($params, &$smarty) {
    if(defined('PROTECT_SCHEDULED_TASKS') && PROTECT_SCHEDULED_TASKS) {
      $url_params = array(
        'code' => substr(APPLICATION_UNIQUE_KEY, 0, 5)
      );
    } else {
      $url_params = null;
    } // if

    return escapeshellarg(Router::assemble(array_var($params, 'task'), $url_params));
  } // smarty_function_scheduled_task_url