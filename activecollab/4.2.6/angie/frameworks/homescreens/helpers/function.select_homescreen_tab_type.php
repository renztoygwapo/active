<?php

  /**
   * select_homescreen_tab_type helper implementation
   * 
   * @package angie.frameworks.homescreens
   * @subpackage helpers
   */

  /**
   * Render select home screen types box
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_homescreen_tab_type($params, &$smarty) {
    $value = array_var($params, 'value', null, true);
    $user = array_required_var($params, 'user', true, 'IUser');
    
    $possibilities = array();
    
    foreach(Homescreens::getTabTypes($user) as $type) {
      $possibilities[get_class($type)] = $type->getDescription();
    } // foreach
    
    return HTML::radioGroupFromPossibilities($params['name'], $possibilities, $value, $params);
  } // smarty_function_select_homescreen_tab_type