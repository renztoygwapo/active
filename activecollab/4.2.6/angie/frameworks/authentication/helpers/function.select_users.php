<?php

  /**
   * select_users helper
   *
   * @package angie.frameworks.authentication
   * @subpackage helpers
   */
  
  /**
   * Render select users from all clients box
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   * @throws NotImplementedError
   * @throws InvalidParamError
   * @throws InvalidInstanceError
   */
  function smarty_function_select_users($params, &$smarty) {
    $user = array_required_var($params, 'user', true, 'IUser');
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    
    $object = array_var($params, 'object', null, true);
    
    // We have provided users
    if(array_key_exists('users', $params)) {
      $users = array_var($params, 'users', null, true);
      
    // We need to load user based on settings provided
    } else {
      if($object) {
        if($object instanceof IUsersContext) {
          $users = $object->users()->getForSelect($user, array_var($params, 'exclude', null, true));
        } else {
          throw new InvalidInstanceError('object', $object, 'IUsersContext');
        } // if
      } else {
        $users = Users::getForSelect($user, array_var($params, 'exclude', null, true));
      } // if
    } // if
    
    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('select_users');
    } // if
    
    $name = array_var($params, 'name', 'select_users', true);
    $value = array_var($params, 'value', null, true);
    if(empty($value)) {
      $value = array();
    } // if
    
    // Default interface
    if($interface == AngieApplication::INTERFACE_DEFAULT) {
      $label = array_var($params, 'label', null, true);
      $mode = array_var($params, 'mode', 'list', true);

      switch($mode) {

        // Render inline list
        case 'list':
          if(isset($params['class'])) {
            $params['class'] .= ' select_users_inline';
          } else {
            $params['class'] = 'select_users_inline';
          } // if

          $result = HTML::openTag('div', $params);

          if($label) {
            $result .= HTML::label($label, null, array_var($params, 'required'), array('class' => 'main_label'));
          } // if

          if($users) {
            foreach($users as $group_name => $group_users) {
              $result .= '<div class="user_group">';
              $result .= HTML::checkbox('', false, array(
                'label' => strlen_utf($group_name) > 25 ? substr_utf($group_name, 0, 25) . '...' : $group_name,
                'class' => 'group_checkbox',
              ));
              $result .= '<ul class="group_users">';

              foreach($group_users as $group_user_id => $group_user_name) {
                $result .= '<li user_id="' . $group_user_id . '">' . HTML::checkbox($name . '[]', in_array($group_user_id, $value), array(
                  'label' => strlen_utf($group_user_name) > 25 ? substr_utf($group_user_name, 0, 25) . '...' : $group_user_name,
                  'value' => $group_user_id,
                  'class' => 'user_checkbox',
                )) . '</li>';
              } // foreach

              $result .= '</ul>';
              $result .= '</div>';
            } // foreach
          } // if

          AngieApplication::useWidget('select_users_inline', AUTHENTICATION_FRAMEWORK);
          return $result . '</div><script type="text/javascript">$("#' . $params['id'] . '").selectUsersInline();</script>';

          break;

        // Render input
        case 'input':
          if(isset($params['class'])) {
            $params['class'] .= ' select_users_input';
          } else {
            $params['class'] = 'select_users_input';
          } // if

          if(isset($params['width'])) {
            $width = (integer) array_var($params, 'width', 300, true);
          } else {
            $width = 300;
          } // if

          $result = HTML::openTag('div', $params);

          if($label) {
            $result .= HTML::label($label, null, array_var($params, 'required'), array('class' => 'main_label'));
          } // if

          if($users) {
            $result .= '<select name="' . $name . '[]" style="width: ' . $width . 'px" data-no_results_text="' . clean(lang('No users found')) . '" multiple>';
            foreach($users as $group_name => $group_users) {
              $result .= '<optgroup label="' . clean($group_name) . '">';

              foreach($group_users as $group_user_id => $group_user_name) {
                $selected = in_array($group_user_id, $value) ? ' selected' : '';

                $result .= '<option value="' . $group_user_id . '"' . $selected . '>' . clean($group_user_name);
              } // foreach

              $result .= '</optgroup>';
            } // foreach
          } // if

          AngieApplication::useWidget('chosen', ENVIRONMENT_FRAMEWORK);
          return $result . '</select></div><script type="text/javascript">$("#' . $params['id'] . ' select").chosen(' . JSON::encode(array(
            'placeholder_text' => lang('Select Users'),
          )) . ');</script>';

          break;

        // Render popup (not implemented)
        case 'popup':
          throw new NotImplementedError('select_users/popup', 'Popup mode for select_users widget is not yet implemented');
          break;

        // Unknown mode
        default:
          throw new InvalidParamError('mode', $mode, "'$mode' is not a valid select_users widget mode");
      } // switch
      
    // Mobile interface
    } else {
      $params['multiple'] = true;
      
      if(isset($params['class'])) {
        $params['class'] .= ' select_users';
      } else {
        $params['class'] = 'select_users';
      } // if
      
      $result = '<div data-role="fieldcontain">';

      $groups = null;
      if($users) {
        $groups = array(HTML::optionForSelect(lang('Please Select'), ''));
        
        foreach($users as $group_name => $group_users) {
          $options = array();
          foreach($group_users as $group_user_id => $group_user_name) {
            $options[] = HTML::optionForSelect($group_user_name, $group_user_id, in_array($group_user_id, $value));
          } // foreach
          
          $groups[] = HTML::optionGroup($group_name, $options);
        } // foreach
      } // if
      
      if(array_var($params, 'optional', true, true)) {
        $result .= HTML::optionalSelect($name, $groups, $params);
      } else {
        $result .= HTML::select($name, $groups, $params);
      } // if
      
      return $result . '</div>';
    } // if
  } // smarty_function_select_users