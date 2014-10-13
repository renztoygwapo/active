<?php

  /**
   * select_assignees helper
   *
   * @package angie.frameworks.assignees
   * @subpackage helpers
   */
  
  /**
   * Render inline select assignees
   * 
   * Parameters:
   * 
   * - object     - Parent object
   * - value      - Array of selected users as first element and ID of task 
   *                owner as second
   * - name       - Base name
   * - inline     - Inline select instead of popup @TODO
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_assignees($params, &$smarty) {
    $name = array_required_var($params, 'name', true);
    $object = array_required_var($params, 'object', true, 'IAssignees');
    $user = array_required_var($params, 'user', true, 'IUser');
    $choose_responsible = (boolean) array_var($params, 'choose_responsible', false, true);
    $choose_subscribers = (boolean) array_var($params, 'choose_subscribers', false, true);
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    $small_form = (boolean)array_var($params, 'small_form', false);

    $users = $object->assignees()->getAvailableUsersForSelect($user, array_var($params, 'exclude',null, true));

    $responsible_id = (integer) array_var($params, 'value', 0, true);
    $other_assignee_ids = array_var($params, 'other_assignees', null, true);
    if (!is_array($other_assignee_ids)) {
      $other_assignee_ids = array();
    } // if

    $user_ids = $can_see_private = array();
    if (is_foreachable($users)) {
      foreach ($users as $company_users) {
        if (is_foreachable($company_users)) {
          $user_ids = array_merge($user_ids, array_keys($company_users));
        } // if
      } // foreach

      $can_see_private = Users::whoCanSeePrivate($user_ids);
    } // if
    
    if (!is_foreachable($users)) {
      return '';
    } // if
    
    if (empty($params['id'])) {
      $params['id'] = HTML::uniqueId('select_assignees');
    } // if

    if ($choose_subscribers && $object instanceof ISubscriptions) {
      if (array_key_exists('subscribers', $params)) {
        $subscriber_ids = array_var($params, 'subscribers', null, true);
      } else {
        $subscriber_ids = $object->subscriptions()->getIds();
      } // if
    } else {
      $subscriber_ids = null;
    } // if
      
    $params['class'] = isset($params['class']) ? $params['class'] .= ' select_asignees' : $params['class'] = 'select_asignees';  
  
    $responsible_name = '';
    $subscribers_name = '';
      
    if ($choose_responsible || $choose_subscribers) {
      $subscribers_name = $name . '[subscribers]';
      if($choose_responsible) {
        $responsible_name = $name . '[assignee_id]';
        $name .= '[other_assignees]';
      } // if
    } // if
 
    // PHONE INTERFACE
    if($interface == AngieApplication::INTERFACE_PHONE) {
      $result = HTML::openTag('div', $params);
      $result .= '<input type="hidden" name="' . $name . '" value="" class="no_other_assignees" />';
      $key = -1;
      $result .= '<div id="'.$params['id'].'">';
      $assigned_to_select = '<div class="assigned_to_select" id="select_assigned_to_wrapper" data-role="fieldcontain">';
      $assigned_to_select .= '<label for="select_assigned_to" class="select ui-select">'.lang('Assigned to').': </label>';
      $assigned_to_select .= '<select class="select_assigned_to" name="select_assigned_to" id="select_assigned_to" data-native-menu="false">';
      $assigned_to_select .= '<option class="">'.lang('Select responsible').'</option>';
      
      $multiselect_assignees = '<div class="multiselect_assignees" id="multiselect_assignees_wrapper" data-role="fieldcontain" style="display: none;">';
      $multiselect_assignees .= '<label for="multiselect_assignees" class="multiselect ui-select">'.lang('Other assignees:').'</label>';
      $multiselect_assignees .= '<select class="multiselect_assignees" name="assignees[]" id="multiselect_assignees" multiple data-native-menu="false">';
      $multiselect_assignees .= '<option class="">'.lang('Choose assignees').'</option>';
      
      foreach($users as $group_name => $group_users) {
        if($group_users) {
          $key++;
          $assigned_to_select .= '<optgroup label="'.$group_name.'">';
          $multiselect_assignees .=  '<optgroup label="'.$group_name.'">';
          
          foreach($group_users as $group_user_id => $group_user_name) {
            $user_can_see_private = in_array($group_user_id, $can_see_private) ? 1 : 0;
            
            if($choose_subscribers) {
              $multiselect_assignees .= '<span class="subscription_toggler"></span>';
            } // if
            $assigned_to_select .= '<option class="user_option" can_see_private="'.$user_can_see_private.'" value="'.$group_user_id.'">'.$group_user_name.'</option>';
            $multiselect_assignees .= '<option class="user_option" can_see_private="'.$user_can_see_private.'" value="'.$group_user_id.'">'.$group_user_name.'</option>';
          } // foreach
          $assigned_to_select .= '</optgroup>';
          $multiselect_assignees .= '</optgroup>';
        } // if
      } // foreach
      
      $assigned_to_select .= '</select></div>';
      $multiselect_assignees .= '</select></div>';
      $result .= $assigned_to_select . $multiselect_assignees;
      if($choose_responsible) {
        $result .= '<input type="hidden" name="' . $responsible_name . '" value="0" class="responsible_user_id" />';
      } // if
      
      $options = array(
        'choose_responsible' => $choose_responsible, 
        'choose_subscribers' => $choose_subscribers,
        'assignees' => $other_assignee_ids, 
      );
      if($choose_responsible) {
        $options['other_assignees_name'] = $name;
        $options['responsible_user_id'] = $responsible_id;
      } // if
      if($choose_subscribers) {
        $options['subscribers_name'] = $subscribers_name;
        $options['subscribers'] = $subscriber_ids;
      } // if
      
      return $result . '</div><script type="text/javascript">$("#' . $params['id'] . '").selectAssignees(' . JSON::encode($options, $user) . ');</script>';
    
    // DEFAULT INTERFACE
    } else {
      foreach($users as $k => $v) {
        $users[$k] = JSON::valueToMap($v);
      } // foreach

      // get all companies used
      $companies = method_exists($user, 'getCompany') ? Companies::getIdNameMap() : array();

      $options = array(
        'data' => JSON::valueToMap($users),
        'companies' => $companies,
        'companies_flipped' => array_flip($companies),
        'name' => $name,
        'responsible_name' => $responsible_name,
        'choose_responsible' => $choose_responsible,
        'choose_subscribers' => $choose_subscribers,
        'assignees' => $other_assignee_ids,
        'can_see_private' => $can_see_private,
        'supports_multiple_assignees' => $object->assignees()->getSupportsMultipleAssignees(),
        'small_form_visibility' => $small_form  ? $object instanceof IVisibility && $object->getVisibility() : false
      );
      
      if($choose_responsible) {
        $options['responsible_user_id'] = $responsible_id;
      } // if
      
      if($choose_subscribers) {
        $options['subscribers_name'] = $subscribers_name;
        $options['subscribers'] = $subscriber_ids;
      } // if

      if ($object instanceof ProjectObject && $object->getProject()->canManagePeople($user)) {
        $options['add_people_url'] = $object->getProject()->getAddPeopleUrl();
      } // if

      AngieApplication::useWidget('select_assignees', ASSIGNEES_FRAMEWORK);
      return '<div id="' . $params['id'] . '"></div><script type="text/javascript">$(\'#' . $params['id'] . '\').selectAssignees(' . JSON::encode($options, $user) . ')</script>';
    } //if
  } // smarty_function_select_assignees