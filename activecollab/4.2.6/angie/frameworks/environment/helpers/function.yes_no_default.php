<?php

  /**
   * yes_no_default helper implementation
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render Yes / No / Default value helper
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_yes_no_default($params, &$smarty) {
    $name = array_required_var($params, 'name');
    $value = array_var($params, 'value', null, true);

    $yes_label = isset($params['yes_label']) && $params['yes_label'] ? lang($params['yes_label']) : lang('Yes');
    $no_label = isset($params['no_label']) && $params['no_label'] ? lang($params['no_label']) : lang('No');

    if ($value === true) {
      $value = 1;
    } else if ($value === false) {
      $value = 0;
    } else {
      $value = '';
    } // if

    $default_value = array_var($params, 'default', false, true) ? $yes_label : $no_label;

    return HTML::optionalSelectFromPossibilities($name, array(
       1 => $yes_label,
       0 => $no_label,
    ), $value, $params, lang('-- System Default (:default) --', array('default' => $default_value)), '');
  } // smarty_function_yes_no_default