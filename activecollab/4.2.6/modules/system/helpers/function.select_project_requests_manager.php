<?php

  /**
   * select_project_requests_manager helper implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Select project requests manager
   * 
   * This helper will return select box that list only users that have 
   * permissions to manage project requests
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_project_requests_manager($params, &$smarty) {
    if(isset($params['class'])) {
      $params['class'] .= ' select_project_requests_manager';
    } else {
      $params['class'] = 'select_project_requests_manager';
    } // if

    $users = array();

    foreach(Users::findByType(array('Administrator', 'Manager')) as $user) {
      if($user->isAdministrator() || ($user->isManager() && $user->getSystemPermission('can_manage_project_requests'))) {
        $users[] = $user;
      } // if
    } // foreach

    if(count($users)) {
      require_once AUTHENTICATION_FRAMEWORK_PATH . '/helpers/function.select_user.php';
      
      $params['users'] = Users::sortUsersForSelect($users);
      
      return smarty_function_select_user($params, $smarty);
    } else {
      return '<span class="no_project_requests_managers">' . lang('There are no users with project requests management permissions') . '</span>';
    } // if
  } // smarty_function_select_project_requests_manager