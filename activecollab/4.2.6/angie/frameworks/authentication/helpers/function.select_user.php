<?php

  /**
   * select_user helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render select user control
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_user($params, &$smarty) {
    $user = array_required_var($params, 'user', true, 'IUser');
    
    $object = array_var($params, 'object', null, true);
    $filter_only_users_with_private_visibility = array_var($params, 'filter_only_users_with_private_visibility', false);

    $add_selected_user = array_var($params, 'add_selected_user', true, true);

    if(isset($params['class'])) {
      $params['class'] .= ' select_user';
    } else {
      $params['class'] = 'select_user';
    } // if
    
    // Users are provided
    if(array_key_exists('users', $params)) {
      $grouped_users = array_var($params, 'users', null, true);
      
    // We need to load users
    } else {
      if($object) {
        if($object instanceof IUsersContext) {
          $grouped_users = $object->users()->getForSelect($user, array_var($params, 'exclude_ids', null, true));
        } else {
          throw new InvalidInstanceError('object', $object, 'IUsersContext');
        } // if
      } else {
        $grouped_users = Users::getForSelect($user, array_var($params, 'exclude_ids', null, true));
      } // if
    } // if

    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('select_user');
    } // if
    
    $name = array_var($params, 'name', null, true);  
    $value = array_var($params, 'value', null, true);
    
    $options = array();
    if(is_foreachable($grouped_users)) {
      foreach($grouped_users as $company_name => $users) {
        $company_users = array();
        foreach($users as $user_id => $user_display_name) {
          if ($filter_only_users_with_private_visibility) {
            $object_user = Users::findById($user_id);
            if (!$object_user->canSeePrivate()) {
              continue;
            } //if
          } // if

          $company_users[] = option_tag($user_display_name, $user_id, array(
            'selected' => $user_id == $value, 
          ));
        } // foreach
        
        $options[] = option_group_tag($company_name, $company_users);
      } // foreach
    } else {
      return lang('No users available');
    } // if
    
    if(array_var($params, 'optional', false, true)) {
      $optional_text = array_var($params, 'optional_text', lang('-- None --'), true);
      
      return HTML::optionalSelect($name, $options, $params, $optional_text);
    } else {
      return HTML::select($name, $options, $params);
    } // if
  } // smarty_function_select_user