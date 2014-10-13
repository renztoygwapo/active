<?php

  /**
   * Address field implementation
   *
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Render address field
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_address_field($params, &$smarty) {
    $value = (string) array_var($params, 'value', '', true);

    if(!isset($params['rows'])) {
      $params['rows'] = 10;
    } // if

    if(!isset($params['cols'])) {
      $params['cols'] = 48;
    } // if

    if(isset($params['class']) && $params['class']) {
      $params['class'] .= ' address_field';
    } else {
      $params['class'] = 'address_field';
    }

    return HTML::textarea(@$params['name'], $value, $params);
  } // smarty_function_address_field