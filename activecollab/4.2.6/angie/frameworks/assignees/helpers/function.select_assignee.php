<?php

  /**
   * select_assignee helper
   *
   * @package angie.frameworks.assignees
   * @subpackage helpers
   */
  
  /**
   * Render select assignee select box
   * 
   * Parameters:
   * 
   * - parent - Parent object
   * - user - User who'll be using the form
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_assignee($params, &$smarty) {
    $parent = array_required_var($params, 'parent', true, 'IAssignees');
    $user = array_required_var($params, 'user', true, 'IUser');
    
    $value = array_var($params, 'value', null, true);
    $exclude_ids = array_var($params, 'exclude', null, true);

    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('select_assignee');
    } // if
    
    if(isset($params['class'])) {
      $params['class'] .= ' select_assignee';
    } else {
      $params['class'] = 'select_assignee';
    } // if

    $grouped = $parent->assignees()->getAvailableUsersForSelect($user, $exclude_ids);
    
    $options = array();
    if(is_foreachable($grouped)) {
      foreach($grouped as $group_name => $users) {
        $group_options = array();
        
        foreach($users as $user_id => $user_display) {
          $group_options[] = HTML::optionForSelect($user_display, $user_id, $user_id == $value);
        } // foreach
        
        $options[] = HTML::optionGroup($group_name, $group_options);
      } // foreach
    } // if
    
    return array_var($params, 'optional', true, true) ?
      HTML::optionalSelect($params['name'], $options, $params, lang('Nobody')) :  
      HTML::select($params['name'], $options, $params);
  } // smarty_function_select_assignee