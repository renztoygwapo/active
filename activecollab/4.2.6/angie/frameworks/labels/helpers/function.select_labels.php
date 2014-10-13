<?php

  /**
   * Select labels helper implementation
   *
   * @package angie.frameworks.labels
   * @subpackage helpers
   */

  /**
   * Select labels helper
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_labels($params, &$smarty) {
    $name = array_required_var($params, 'name', true);
    $user = array_required_var($params, 'user', true);
    $type = array_required_var($params, 'type', true);
    
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    $value = array_var($params, 'value', null, true);
    
    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('select_label');
    } // if
    
    $possibilities = Labels::getIdNameMap($type);
    
    if(empty($possibilities)) {
      return '<p>' . lang('There are no labels defined') . '</p>';
    } else {
      return HTML::checkboxGroupFromPossibilities($name, $possibilities, $value, $params, $interface);
    } // if
  } // smarty_function_select_label