<?php

  /**
   * select_project_requests_managers helper implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Select project requests managers
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_project_requests_managers($params, &$smarty) {
    if(isset($params['class'])) {
      $params['class'] .= ' select_project_requests_managers';
    } else {
      $params['class'] = 'select_project_requests_managers';
    } // if
    
    if(!array_key_exists('inline', $params)) {
      $params['inline'] = true;
    } // if

    $user_ids = Users::findIdsByType(array('Administrator', 'Manager'), STATE_VISIBLE, null, function($id, $type, $custom_permissions, $state) {
      return $type == 'Administrator' || in_array('can_manage_project_requests', $custom_permissions);
    });

    if($user_ids) {
      AngieApplication::useHelper('select_users', AUTHENTICATION_FRAMEWORK);

      $params['users'] = Users::getForSelectByConditions(array('id IN (?)', $user_ids));

      return smarty_function_select_users($params, $smarty);
    } else {
      return '<span class="no_project_requests_managers">' . lang('There are no users with project requests management permissions') . '</span>';
    } // if
  } // smarty_function_select_project_requests_managers