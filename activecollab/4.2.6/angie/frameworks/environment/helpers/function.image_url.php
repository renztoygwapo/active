<?php

  /**
   * image_url helper implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Return image URL
   * 
   * Parameters:
   * 
   * - name - image filename
   * - module - name of the module, if not present global data is used
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_image_url($params, &$smarty) {
    return AngieApplication::getImageUrl(array_var($params, 'name'), array_var($params, 'module'), array_var($params, 'interface'));
  } // smarty_function_image_url