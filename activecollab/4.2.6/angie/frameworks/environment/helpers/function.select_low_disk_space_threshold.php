<?php

/**
 * select_low_disk_space_threshold helper
 *
 * @package angie.framework.environment
 * @subpackage helpers
 */

/**
 * Render select disk space threshold limit
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_select_low_disk_space_threshold($params, &$smarty) {
  $value = array_var($params, 'value', null, true);
  $threshold_values = array(80, 85, 90, 95);

  $options = array();
  foreach ($threshold_values as $threshold_value) {
    $options[] = option_tag($threshold_value . '%', $threshold_value, array('selected' => ($value == $threshold_value ? 'selected' : false)));
  } // foreach

  if (isset($params['class'])) {
    $params['class'] = $params['class'] . ' select_low_disk_space_threshold';
  } else {
    $params['class'] = 'select_low_disk_space_threshold';
  } // if

  return select_box($options, $params);
} // smarty_function_select_low_disk_space_threshold

?>