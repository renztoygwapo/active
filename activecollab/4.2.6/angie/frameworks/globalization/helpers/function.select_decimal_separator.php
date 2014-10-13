<?php

/**
 * Select Decimal separator
 *
 * @package activeCollab.modules.system
 * @subpackage helpers
 */

/**
 * Render select decimal separator
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_select_decimal_separator($params, &$smarty) {
  $name = array_required_var($params, 'name', true);
  $value = array_var($params, 'value', false, true);
  $optional = array_var($params, 'optional', false, true);

  if ($value === false || $value === null) {
    $value = '.';
  } // if

  $decimal_separator_options = array(
    '.' => '.',
    ',' => ','
  );

  return HTML::selectFromPossibilities($name, $decimal_separator_options, $value, $params);
} // smarty_function_select_decimal_separator