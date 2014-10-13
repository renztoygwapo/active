<?php

  /**
   * Implode function helper
   * 
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Interface to implode() function
   * 
   * Function params:
   * 
   * - $values - array of values that need to be imploded
   * - $separator - separator string
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_implode($params, &$smarty) {
    return implode(array_var($params, 'separator'), array_var($params, 'values'));
  } // smarty_function_implode