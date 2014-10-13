<?php

/**
 * Select Decimal rounding
 *
 * @package activeCollab.modules.system
 * @subpackage helpers
 */

/**
 * Render select decimal rounding
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_select_decimal_rounding($params, &$smarty) {
  $name = array_required_var($params, 'name', true);
  $value = array_var($params, 'value', false, true);
  $optional = array_var($params, 'optional', false, true);

  if ($value === false || $value === null) {
    $value = '';
  } // if

  $decimal_rounding_options = array(
    ''      => lang('No Rounding'),
    '0.05'  => '0.05',
    '0.10'  => '0.10',
    '0.50'  => '0.50',
    '1.00'  => '1.00'
  );

  return HTML::selectFromPossibilities($name, $decimal_rounding_options, $value, $params);
} // smarty_function_select_decimal_rounding