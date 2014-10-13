<?php

/**
 * Select Number of decimal spaces
 *
 * @package activeCollab.modules.system
 * @subpackage helpers
 */

/**
 * Render select number of decimal spaces
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_select_number_of_decimal_spaces($params, &$smarty) {
  $name = array_required_var($params, 'name', true);
  $value = array_var($params, 'value', false, true);
  $optional = array_var($params, 'optional', false, true);

  if ($value === false || $value === null) {
    $value = 2;
  } // if

  $decimal_spaces_options = array(
    '0' => '0',
    '1' => '1',
    '2' => '2',
    '3' => '3',
  );

  return HTML::selectFromPossibilities($name, $decimal_spaces_options, $value, $params);
} // smarty_function_select_number_of_decimal_spaces