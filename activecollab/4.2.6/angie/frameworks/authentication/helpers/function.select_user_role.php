<?php

  /**
   * Select role helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render select role helper
   * 
   * Params:
   * 
   * - name - Select name attribute
   * - value - ID of selected role
   * - optional - Wether value is optional or not
   * - active_user - Set if we are changing role of existing user so we can 
   *   handle situations when administrator role is displayed or changed
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_user_role($params, &$smarty) {
    $name = array_required_var($params, 'name', true);

    if(isset($params['value']) && is_array($params['value'])) {
      $selected_type = array_var($params['value'], 'type');
      $selected_custom_permissions = array_var($params['value'], 'custom_permissions');
    } else {
      $selected_custom_permissions = array();
    } // if

    if(empty($selected_type)) {
      $selected_type = Users::getDefaultUserClass();
    } // if

    $id = array_var($params, 'id');
    if(empty($id)) {
      $id = HTML::uniqueId('select_user_role');
    } // if

    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    
    $logged_user = Authentication::getLoggedUser();
    if($logged_user instanceof User) {
      AngieApplication::useWidget('select_user_role', AUTHENTICATION_FRAMEWORK);

      $result = '<div class="select_user_role" id="' . $id . '">';

      // Default, web interface
      if($interface == AngieApplication::INTERFACE_DEFAULT) {
        if(isset($params['label']) && $params['label']) {
          $result .= HTML::label($params['label'], null, true, array(
            'class' => 'main_label',
          ));
        } // if

        foreach(Users::getAvailableUserInstances() as $available_user_instance) {
          if($available_user_instance->isAdministrator() && !$logged_user->isAdministrator()) {
            continue; // Don't show Administration role to users who are not administrators
          } // if

          $available_user_instance_class = get_class($available_user_instance);
          $selected = $selected_type == $available_user_instance_class;

          $result .= '<div class="select_user_role_wrapper">';
          $result .= HTML::radio($name . '[type]', $selected, array(
              'label' => $available_user_instance->getRoleName(),
              'value' => $available_user_instance_class,
            )) . ' &mdash; <span class="select_user_role_description">' . clean($available_user_instance->getRoleDescription()) . '</span>';
          
          // determine rolename to put it on the slide_down_settings as class
          $slideDownClass = '';
          $slideDownClass = 'role_'.trim(strtolower($available_user_instance->getRoleName()));

          $result .=   '<div class="slide_down_settings '.$slideDownClass.'" style="display: ' . ($selected ? 'block' : 'none') . ';">';

          $custom_permissions = $available_user_instance->getAvailableCustomPermissions();

          if($custom_permissions->count()) {
            $result .=     '<div class="select_user_role_extra_permissions">';
            $result .=       '<p>' . lang('Extra Permissions') . ':</p>';

            foreach($custom_permissions as $permission_name => $permission_details) {
              $result .= '<div class="select_user_role_extra_permission">';
              $result .= HTML::checkbox($name . '[custom_permissions][]', in_array($permission_name, $selected_custom_permissions), array(
                'label' => $permission_details['name'],
                'value' => $permission_name,
                'class' => 'custom_permission_checkbox'
              ));

              if($permission_details['description']) {
                $result .= '<span class="select_user_role_extra_permission"> &mdash; ' . clean($permission_details['description']) . '</span>';
              } // if

              $result .= '</div>';
            } // foreach

            $result .=     '</div>';
          } // if

          $result .=   '</div>';
          $result .= '</div>';
        } // foreach

      // Phone interface
      } elseif($interface == AngieApplication::INTERFACE_PHONE) {
        $result .= '<fieldset data-role="controlgroup" data-theme="j">';

        foreach(Users::getAvailableUserInstances() as $available_user_instance) {
          if($available_user_instance->isAdministrator() && !$logged_user->isAdministrator()) {
            continue; // Don't show Administration role to users who are not administrators
          } // if

          $available_user_instance_class = get_class($available_user_instance);
          $selected = $selected_type == $available_user_instance_class;

          $result .= '<div class="select_user_role_wrapper">';

          $result .= HTML::radio($name . '[type]', $selected, array(
            'label' => $available_user_instance->getRoleName(),
            'value' => $available_user_instance_class,
          ));

          $result .=   '<div class="slide_down_settings" style="display: ' . ($selected ? 'block' : 'none') . ';">';

          $custom_permissions = $available_user_instance->getAvailableCustomPermissions();

          if($custom_permissions->count()) {
            $result .=     '<div class="select_user_role_extra_permissions">';
            $result .=       '<p>' . lang('Extra Permissions') . ':</p>';
            $result .=       '<fieldset data-role="controlgroup" data-theme="j">';

            foreach($custom_permissions as $permission_name => $permission_details) {
              $result .= '<div class="select_user_role_extra_permission">';
              $result .= HTML::checkbox($name . '[custom_permissions][]', in_array($permission_name, $selected_custom_permissions), array(
                'label' => $permission_details['name'],
                'value' => $permission_name,
                'class' => 'custom_permission_checkbox'
              ));
              $result .= '</div>';
            } // foreach

            $result .=       '</fieldset>';
            $result .=     '</div>';
          } // if

          $result .=   '</div>';
          $result .= '</div>';
        } // foreach

        $result .= '</fieldset>';
      } // if

      $settings = array(
        'client_interface' => $interface
      );

      return $result . '</div><script type="text/javascript">$("#' . $id . '").selectUserRole(' . JSON::encode($settings) . ');</script>';
    } else {
      return new InvalidParamError('logged_user', $logged_user, '$logged_user is expected to be an instance of user class');
    } // if
  } // smarty_function_select_user_role