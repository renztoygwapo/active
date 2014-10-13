<?php

  /**
   * Select_repository helper
   *
   * @package activeCollab.modules.source
   * @subpackage helpers
   */

  /**
   * Select repository widget
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_repository_update_type($params, &$smarty) {
    $selected = array_var($params, 'selected', null, true);

    $options = array();
    foreach ($params['data'] as $key=>$item) {
      $option_attributes = $key == $selected ? array('selected' => true) : null;
      $options[] = option_tag($item, $key, $option_attributes);
    } // foreach

    $filtered_params = array();

    foreach ($params as $key => $value) {
      if (is_scalar($value)) {
        $filtered_params[$key] = $value;
      } //if
    } //foreach

    return select_box($options, $filtered_params);
  } // smarty_function_select_repository