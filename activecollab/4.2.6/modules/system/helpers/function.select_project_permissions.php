<?php

  /**
   * Render select project permissions widget
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render widgert
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_project_permissions($params, &$smarty) {
    $id = array_var($params, 'id');

    if(empty($id)) {
      $id = HTML::uniqueId('select_project_permissions');
    } // if

    $name = array_var($params, 'name');
    $value = array_var($params, 'value', array());
    $permissions = ProjectRoles::getPermissions();

    if($permissions) {
      $levels = array(
        ProjectRole::PERMISSION_NONE => lang('No Access'),
        ProjectRole::PERMISSION_ACCESS => lang('Access Only'),
        ProjectRole::PERMISSION_CREATE => lang('Access and Create'),
        ProjectRole::PERMISSION_MANAGE => lang('Access, Create and Manage'),
      );

      $result = '<div id="' . $id . '" class="select_project_permissions">
        <table cellspacing="0" cellpadding="0" class="common auto">
          <thead>
            <tr>
              <th class="permission_name">' . lang('Project Section') . '</th>
              <th class="permission_value">' . lang('Permissions Level') . '</th>
              <th class="options"><img src="' . AngieApplication::getImageUrl('icons/12x12/configure.png', ENVIRONMENT_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT) . '" title="' . lang('Options') . '">
                <div>
                  <img src="' . AngieApplication::getImageUrl('icons/12x12/delete.png', ENVIRONMENT_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT) . '" title="' . lang('Close') . '">
                  <p>' . lang('Set All Permissions To') . ':</p>
                  <ul>';

      foreach($levels as $level_value => $level_label) {
        $result .= '<li><a href="#" class="select_project_permissions_set_all" level_value="' . $level_value . '">' . $level_label . '</a></li>';
      } // foreach

      $result .= '</ul>
            </div>
          </th>
        </tr>
      </thead><tbody>';

      foreach($permissions as $permission => $permission_name) {
        $permission_value = array_var($value, $permission);

        if($permission_value === null) {
          $permission_value = ProjectRole::PERMISSION_NONE;
        } // if

        $result .= '<tr><td class="permission_name">' . clean($permission_name) . '</td><td class="permission_value" colspan="2"><select name="' . $name . '[' . $permission . ']">';

        foreach($levels as $level_value => $level_label) {
          $selected = $level_value == $permission_value ? ' selected' : '';

          $result .= '<option value="' . $level_value . '"' . $selected . '>' . $level_label . '</option>';
        } // foreach

        $result .= '</selecet></td></tr>';
      } // foreach

      AngieApplication::useWidget('select_project_permissions', SYSTEM_MODULE);
      return $result . '</tbody></table></div><script type="text/javascript">$("#' . $id . '").selectProjectPermissions();</script>';
    } else {
      return '';
    } // if
  } // smarty_function_select_project_permissions