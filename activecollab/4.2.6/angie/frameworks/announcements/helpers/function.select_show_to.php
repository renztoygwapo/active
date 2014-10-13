<?php

  /**
   * select_show_to helper implementation
   *
   * @package angie.frameworks.announcements
   * @subpackage helpers
   */

  /**
   * Render select show to field
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_show_to($params, &$smarty) {
    $user = array_required_var($params, 'user', true);
    $name = array_required_var($params, 'name', true);

    $value = array_var($params, 'value', null, true);

    $id = isset($params['id']) && $params['id'] ? $params['id'] : HTML::uniqueId('show_to');

    $result = '<table id="' . $id . '" class="select_show_to" cellspacing="0"><tbody><tr>';

    $result .= '<td class="show_to_select">' . HTML::selectFromPossibilities($name . '[target_type]', array(
      FwAnnouncement::ANNOUNCE_TARGET_TYPE_ROLE => lang('Users with the Selected Roles ...'),
      FwAnnouncement::ANNOUNCE_TARGET_TYPE_COMPANY => lang('Members of Companies ...'),
      FwAnnouncement::ANNOUNCE_TARGET_TYPE_USER => lang('Selected Users ...')
    ), $value['target_type'], $params) . '</td>';

    $result .= '<td class="show_to_data">';

    $available_user_instances = Users::getAvailableUserInstances();
    if(is_foreachable($available_user_instances)) {
      $possibilities = array();

      foreach($available_user_instances as $available_user_instance) {
        $possibilities[get_class($available_user_instance)] = $available_user_instance->getRoleName();
      } // foreach
      $result .= '<div id="role">' . HTML::checkboxGroupFromPossibilities($name . '[role]', $possibilities, isset($value['role']) && $value['role'] ? $value['role'] : null, $params) . '</div>';
    } // if

    require_once SYSTEM_MODULE_PATH . '/helpers/function.select_companies.php';
    $result .= '<div id="company" style="display: none;">' . smarty_function_select_companies(array(
      'user' => $user,
      'name' => $name . '[company]',
      'value' => isset($value['company']) && $value['company'] ? $value['company'] : null
    ), $smarty) . '</div>';

    require_once AUTHENTICATION_FRAMEWORK_PATH . '/helpers/function.select_users.php';
    $result .= '<div id="user" style="display: none;">' . smarty_function_select_users(array(
      'user' => $user,
      'name' => $name . '[user]',
      'value' => isset($value['user']) && $value['user'] ? $value['user'] : null
    ), $smarty) . '</div>';

    $result .= '</td>';

    AngieApplication::useWidget('select_show_to', ANNOUNCEMENTS_FRAMEWORK);

    return $result . '</tr></tbody></table><script type="text/javascript">App.widgets.SelectShowTo.init("' . $id . '");</script>';
  } // smarty_function_select_show_to