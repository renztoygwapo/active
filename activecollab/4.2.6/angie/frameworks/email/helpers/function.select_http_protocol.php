<?php

/**
 * select_http_protocol helper implementation
 *
 * @package angie.frameworks.environment
 * @subpackage helpers
 */

/**
 * Render select http protocol control
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_select_http_protocol($params, &$smarty) {
  $filter_types = array(
    'http' => 'HTTP',
    'https' => 'HTTPS',
  );

  $value = null;
  if(isset($params['value'])) {
    $value = $params['value'];
    unset($params['value']);
  } // if

  $options = array();
  foreach($filter_types as $filter_type) {
    $option_attributes = $filter_type == $value ? array('selected' => true) : null;
    $options[] = option_tag($filter_type, $filter_type, $option_attributes);
  } // foreach

  return select_box($options, $params);
} // smarty_function_select_http_protocol