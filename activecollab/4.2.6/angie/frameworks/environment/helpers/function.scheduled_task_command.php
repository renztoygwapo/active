<?php

  /**
   * Scheduled task command implementation
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render scheduled tasks command
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_scheduled_task_command($params, &$smarty) {
    if(defined('PROTECT_SCHEDULED_TASKS') && PROTECT_SCHEDULED_TASKS) {
      $sufix = ' ' . escapeshellarg(substr(APPLICATION_UNIQUE_KEY, 0, 5));
    } else {
      $sufix = '';
    } // if

    return escapeshellarg(ENVIRONMENT_PATH . "/tasks/" . array_var($params, 'task') . ".php") . $sufix;
  } // smarty_function_scheduled_task_command