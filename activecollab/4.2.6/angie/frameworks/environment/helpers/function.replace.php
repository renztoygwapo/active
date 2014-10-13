<?php

  /**
   * replace helper implementation
   * 
   * @package angie.framework.environment
   * @subpackage helpers
   */

  /**
   * Replace in given string
   * 
   * Usage:
   * 
   * {replace search='--TASKID--' in=$task_url replacement=$task.task_id}
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_replace($params, &$smarty) {
    if(isset($params['explode']) && $params['explode']) {
      return str_replace(explode($params['explode'], $params['search']), explode($params['explode'], $params['replacement']), $params['in']);
    } else {
      return str_replace($params['search'], $params['replacement'], $params['in']);
    } // if
  } // smarty_function_replace